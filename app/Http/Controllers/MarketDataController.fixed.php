<?php

namespace App\Http\Controllers;

use App\Models\MarketData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class MarketDataController extends Controller
{
    /**
     * Display a listing of market data.
     */
    public function index(Request $request)
    {
        try {
            Log::info('MarketDataController@index called', [
                'user_id' => Auth::id(),
                'symbol' => $request->symbol
            ]);
            
            // Default symbol if none is provided
            $symbol = $request->symbol ?? 'AAPL';
            
            // Storage directory for market data
            $storageDir = storage_path('app/market_data');
            
            // Check if the directory exists, create if not
            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            
            // CSV file for the selected symbol
            $csvFile = $storageDir . '/' . $symbol . '.csv';
            
            // Handle case where file doesn't exist
            if (!file_exists($csvFile)) {
                return view('market-data.index', [
                    'data' => [],
                    'symbols' => $this->getAvailableSymbols(),
                    'currentSymbol' => $symbol,
                    'message' => "No data available for {$symbol}"
                ]);
            }
            
            // Parse CSV file
            $csv = Reader::createFromPath($csvFile, 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();
            
            // Convert to array for pagination
            $data = iterator_to_array($records);
            
            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                
                $data = array_filter($data, function($item) use ($startDate, $endDate) {
                    return $item['date'] >= $startDate && $item['date'] <= $endDate;
                });
            }
            
            // Sort by date (newest first)
            usort($data, function($a, $b) {
                return strcmp($b['date'], $a['date']);
            });
            
            // Manual pagination
            $perPage = 15;
            $currentPage = $request->input('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $paginatedData = array_slice($data, $offset, $perPage);
            $totalPages = ceil(count($data) / $perPage);
            
            return view('market-data.index', [
                'data' => $paginatedData,
                'symbols' => $this->getAvailableSymbols(),
                'currentSymbol' => $symbol,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in MarketDataController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('market-data.index', [
                'data' => [],
                'symbols' => $this->getAvailableSymbols(),
                'currentSymbol' => $request->symbol ?? 'AAPL',
                'error' => 'Error loading market data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new market data entry.
     */
    public function create()
    {
        return view('market-data.create');
    }

    /**
     * Store a newly created market data in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string|max:10',
            'date' => 'required|date',
            'open' => 'required|numeric',
            'high' => 'required|numeric',
            'low' => 'required|numeric',
            'close' => 'required|numeric',
            'volume' => 'required|integer',
        ]);
        
        $marketData = new MarketData($request->all());
        $marketData->user_id = Auth::id();
        $marketData->is_approved = Auth::user()->is_admin ? true : false;
        $marketData->save();
        
        return redirect()->route('market-data.index')
            ->with('success', 'Market data created successfully.');
    }

    /**
     * Display the specified market data.
     */
    public function show($id)
    {
        $marketData = MarketData::findOrFail($id);
        
        // Only approved data or owner's data can be viewed
        if (!$marketData->is_approved && $marketData->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('market-data.show', compact('marketData'));
    }

    /**
     * Show the form for editing the specified market data.
     */
    public function edit($id)
    {
        $marketData = MarketData::findOrFail($id);
        
        // Only owner can edit their data
        if ($marketData->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('market-data.edit', compact('marketData'));
    }

    /**
     * Update the specified market data in storage.
     */
    public function update(Request $request, MarketData $marketData)
    {
        // Only owner can update their data
        if ($marketData->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'symbol' => 'required|string|max:10',
            'date' => 'required|date',
            'open' => 'required|numeric',
            'high' => 'required|numeric',
            'low' => 'required|numeric',
            'close' => 'required|numeric',
            'volume' => 'required|integer',
        ]);
        
        $marketData->update($request->all());
        
        return redirect()->route('market-data.index')
            ->with('success', 'Market data updated successfully');
    }

    /**
     * Remove the specified market data from storage.
     */
    public function destroy(MarketData $marketData)
    {
        // Only owner or admin can delete
        if ($marketData->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access');
        }
        
        $marketData->delete();
        
        return redirect()->route('market-data.index')
            ->with('success', 'Market data deleted successfully');
    }
    
    /**
     * Export market data to CSV.
     */
    public function export(Request $request)
    {
        $symbol = $request->symbol ?? 'AAPL';
        
        // Storage directory for market data
        $storageDir = storage_path('app/market_data');
        
        // CSV file for the selected symbol
        $csvFile = $storageDir . '/' . $symbol . '.csv';
        
        // Handle case where file doesn't exist
        if (!file_exists($csvFile)) {
            return redirect()->back()
                ->with('error', "No data available for {$symbol}");
        }
        
        // Set headers for download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$symbol}_market_data.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        // Read the CSV file
        $csv = Reader::createFromPath($csvFile, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();
        
        // Convert to array for filtering
        $data = iterator_to_array($records);
        
        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $data = array_filter($data, function($item) use ($startDate, $endDate) {
                return $item['date'] >= $startDate && $item['date'] <= $endDate;
            });
        }
        
        // Sort by date (newest first)
        usort($data, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        
        // Create a new CSV in memory
        $output = Writer::createFromString('');
        
        // Add the headers as the first row
        $headers = array_keys(reset($data) ?: []);
        $output->insertOne($headers);
        
        // Add the data rows
        foreach ($data as $record) {
            $output->insertOne($record);
        }
        
        // Return the CSV as a download
        $callback = function() use ($output) {
            echo $output->getContent();
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Approve market data submission.
     */
    public function approve(MarketData $marketData)
    {
        // Only admin can approve
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized access');
        }
        
        $marketData->is_approved = true;
        $marketData->save();
        
        return redirect()->back()->with('success', 'Market data approved successfully.');
    }
    
    /**
     * Show the form for bulk importing market data.
     */
    public function importForm()
    {
        return view('market-data.import');
    }
    
    /**
     * Process the bulk import of market data.
     */
    public function importStore(Request $request)
    {
        try {
            // Validate file upload
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
                'symbol' => 'required|string|max:10|min:3|alpha_num',
            ]);

            // Check if user has admin privileges to import
            if (!Auth::user()->is_admin && !Auth::user()->can('import-market-data')) {
                return redirect()->back()->with('error', 'You do not have permission to import market data');
            }

            Log::info('MarketDataController@importStore called', [
                'user_id' => Auth::id(),
                'symbol' => $request->symbol,
                'file_size' => $request->file('file')->getSize()
            ]);

            $file = $request->file('file');
            $path = $file->getRealPath();
            $errors = [];
            $rowCount = 0;
            $successCount = 0;
            
            
            // Data storage directory
            $storageDir = storage_path('app/market_data');
            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            
            // Get file extension
            $extension = $file->getClientOriginalExtension();
            
            // Process based on file type
            if (in_array($extension, ['xlsx', 'xls'])) {
                try {
                    // Excel file processing
                    $this->processExcelImport($file, $request->symbol, $storageDir);
                    return redirect()->route('market-data.index')
                        ->with('success', "Successfully imported Excel data for {$request->symbol}.");
                } catch (\Exception $e) {
                    Log::error('Excel import error: ' . $e->getMessage());
                    return redirect()->back()
                        ->with('error', 'Error importing Excel data: ' . $e->getMessage());
                }
            }
            
            // Parse CSV file
            if (($handle = fopen($path, 'r')) !== false) {
                // Read header row
                $header = fgetcsv($handle, 1000, ',');
                
                // Normalize header keys
                $header = array_map(function($item) {
                    return strtolower(trim($item));
                }, $header);
                
                // Required fields
                $requiredFields = ['date', 'open', 'high', 'low', 'close', 'volume', 'symbol'];
                
                // Check if all required fields exist
                foreach ($requiredFields as $field) {
                    if (!in_array($field, $header)) {
                        fclose($handle);
                        return redirect()->back()
                            ->with('error', "Missing required column: {$field}");
                    }
                }
                
                // Store validated rows in arrays by symbol
                $validatedData = [];
                
                // Process data rows
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $rowCount++;
                    
                    // Skip empty rows
                    if (count(array_filter($data)) === 0) {
                        continue;
                    }
                    
                    // Convert to associative array
                    $rowData = [];
                    foreach ($header as $index => $key) {
                        if (isset($data[$index])) {
                            $rowData[$key] = trim($data[$index]);
                        } else {
                            $rowData[$key] = null;
                        }
                    }
                    
                    // Validate row data
                    $validator = Validator::make($rowData, [
                        'symbol' => 'required|string|max:10',
                        'date' => 'required|date_format:Y-m-d',
                        'open' => 'required|numeric',
                        'high' => 'required|numeric',
                        'low' => 'required|numeric',
                        'close' => 'required|numeric',
                        'volume' => 'required|integer',
                    ]);
                    
                    if ($validator->fails()) {
                        $errors[] = [
                            'row' => $rowCount,
                            'errors' => $validator->errors()->toArray()
                        ];
                        continue;
                    }
                    
                    // Use the symbol from the request
                    $rowSymbol = $request->symbol;
                    
                    // Add row to validated data
                    if (!isset($validatedData[$rowSymbol])) {
                        $validatedData[$rowSymbol] = [];
                    }
                    
                    // Add user ID and approval status
                    $rowData['user_id'] = Auth::id();
                    $rowData['is_approved'] = Auth::user()->is_admin ? '1' : '0';
                    
                    // Override symbol with the one from request
                    $rowData['symbol'] = $rowSymbol;
                    
                    // Add to validated data
                    $validatedData[$rowSymbol][] = $rowData;
                    $successCount++;
                }
                
                fclose($handle);
                
                // Process the validated data
                foreach ($validatedData as $symbol => $rows) {
                    // CSV file for storing market data
                    $csvFilename = $storageDir . '/' . $symbol . '.csv';
                    $isNewFile = !file_exists($csvFilename);
                    
                    // Open CSV file for writing (create or append)
                    $mode = $isNewFile ? 'w' : 'a';
                    $csvHandle = fopen($csvFilename, $mode);
                    
                    // Write headers if it's a new file
                    if ($isNewFile) {
                        fputcsv($csvHandle, $header);
                    }
                    
                    // Write validated rows
                    foreach ($rows as $row) {
                        fputcsv($csvHandle, $row);
                    }
                    
                    // Close file
                    fclose($csvHandle);
                    
                    // Update index file to include this symbol
                    $this->updateIndexFile($symbol);
                }
            }
            
            // Display results
            if (!empty($errors)) {
                session()->flash('failures', $errors);
                return redirect()->route('market-data.index')
                    ->with('warning', "Imported {$successCount} of {$rowCount} rows. Some rows had validation errors.");
            }
            
            return redirect()->route('market-data.index')
                ->with('success', "Successfully imported {$successCount} rows for {$request->symbol}.");
            
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate a shareable link for a market data entry.
     */
    public function share(MarketData $marketData)
    {
        // Only share approved data or owner's data
        if (!$marketData->is_approved && $marketData->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Generate a signed URL
        $url = URL::signedRoute(
            'market-data.shared',
            ['marketData' => $marketData->id],
            now()->addDays(30) // Link expires in 30 days
        );
        
        return redirect()->back()->with('share_url', $url);
    }
    
    /**
     * Display shared market data entry.
     */
    public function shared(Request $request, MarketData $marketData)
    {
        // Verify the signature is valid
        if (!$request->hasValidSignature()) {
            abort(401, 'This link has expired or is invalid.');
        }
        
        // Only approved data can be viewed with shared links
        if (!$marketData->is_approved) {
            abort(404, 'The requested data is not available.');
        }
        
        return view('market-data.shared', compact('marketData'));
    }
    
    /**
     * Export market data to different formats (CSV, Excel, PDF).
     */
    public function exportFormat(Request $request, $format)
    {
        // We only support CSV now
        if ($format !== 'csv') {
            return redirect()->back()->with('error', 'Only CSV export is supported.');
        }
        
        return $this->export($request);
    }
    
    /**
     * Download CSV template for market data import.
     */
    public function downloadCsvTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="market_data_template.csv"',
        ];
        
        $content = "date,open,high,low,close,volume,symbol\n";
        $content .= "2024-01-01,100.50,102.75,99.25,101.00,1000000,AAPL\n";
        
        return response($content, 200, $headers);
    }
    
    /**
     * Download Excel template for market data import.
     */
    public function downloadExcelTemplate()
    {
        return ExcelFacade::create("market_data_template", function($excel) {
            $excel->sheet('Market Data', function($sheet) {
                $sheet->cells('A1:G1', function($cells) {
                    $cells->setFontWeight('bold');
                });
                
                $data = [
                    ['date', 'open', 'high', 'low', 'close', 'volume', 'symbol'],
                    ['2024-01-01', 100.50, 102.75, 99.25, 101.00, 1000000, 'AAPL']
                ];
                
                $sheet->fromArray($data, null, 'A1', false, false);
            });
        })->export('xlsx');
    }
    
    /**
     * Process Excel file import
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $symbol
     * @param string $storageDir
     * @return void
     */
    private function processExcelImport($file, $symbol, $storageDir)
    {
        try {
            Log::info('Processing Excel import', [
                'user_id' => Auth::id(),
                'symbol' => $symbol,
                'file' => $file->getClientOriginalName()
            ]);
            
            // Load the Excel file
            $data = ExcelFacade::load($file->getRealPath(), function($reader) {
                $reader->noHeading();
            })->get();
            
            if (!$data || $data->count() === 0) {
                throw new \Exception('Excel file is empty or could not be read');
            }
            
            // Get headers from first row
            $headers = $data->first()->toArray();
            
            // Normalize header keys
            $headers = array_map(function($item) {
                return strtolower(trim($item));
            }, $headers);
            
            // Required fields
            $requiredFields = ['date', 'open', 'high', 'low', 'close', 'volume', 'symbol'];
            
            // Check if all required fields exist
            foreach ($requiredFields as $field) {
                if (!in_array($field, $headers)) {
                    throw new \Exception("Missing required column: {$field}");
                }
            }
            
            // CSV file for storing market data
            $csvFilename = $storageDir . '/' . $symbol . '.csv';
            $isNewFile = !file_exists($csvFilename);
            
            // Open CSV file for writing (create or append)
            $mode = $isNewFile ? 'w' : 'a';
            $csvHandle = fopen($csvFilename, $mode);
            
            // Write headers if it's a new file
            if ($isNewFile) {
                fputcsv($csvHandle, $headers);
            }
            
            // Skip the header row for processing
            $rows = $data->slice(1);
            $rowsProcessed = 0;
            
            // Store data
            foreach ($rows as $index => $row) {
                $rowData = $row->toArray();
                
                // Skip empty rows
                if (count(array_filter($rowData)) === 0) {
                    continue;
                }
                
                // Convert to associative array
                $rowAssoc = [];
                foreach ($headers as $i => $key) {
                    if (isset($rowData[$i])) {
                        $rowAssoc[$key] = trim($rowData[$i]);
                    } else {
                        $rowAssoc[$key] = null;
                    }
                }
                
                // Validate row data
                $validator = Validator::make($rowAssoc, [
                    'symbol' => 'required|string|max:10',
                    'date' => 'required|date_format:Y-m-d',
                    'open' => 'required|numeric',
                    'high' => 'required|numeric',
                    'low' => 'required|numeric',
                    'close' => 'required|numeric',
                    'volume' => 'required|integer',
                ]);
                
                if ($validator->fails()) {
                    Log::warning('Invalid row in Excel import', [
                        'row' => $index + 2, // +2 because of 0-indexing and header row
                        'errors' => $validator->errors()->toArray(),
                        'data' => $rowAssoc
                    ]);
                    continue;
                }
                
                // Override symbol with the one provided in request
                $rowAssoc['symbol'] = $symbol;
                
                // Add user ID and approval status
                $rowAssoc['user_id'] = Auth::id();
                $rowAssoc['is_approved'] = Auth::user()->is_admin ? '1' : '0';
                
                // Write to CSV
                fputcsv($csvHandle, array_values($rowAssoc));
                $rowsProcessed++;
            }
            
            // Close the file
            fclose($csvHandle);
            
            // Update index file
            $this->updateIndexFile($symbol);
            
            Log::info('Excel import completed successfully', [
                'user_id' => Auth::id(),
                'symbol' => $symbol,
                'rows_processed' => $rowsProcessed
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing Excel import', [
                'user_id' => Auth::id(),
                'symbol' => $symbol,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update the index file with a new symbol
     *
     * @param string $symbol
     * @return void
     */
    private function updateIndexFile($symbol)
    {
        $storageDir = storage_path('app/market_data');
        $indexFile = $storageDir . '/index.json';
        
        if (file_exists($indexFile)) {
            $index = json_decode(file_get_contents($indexFile), true);
        } else {
            $index = ['symbols' => []];
        }
        
        if (!in_array($symbol, $index['symbols'])) {
            $index['symbols'][] = $symbol;
        }
        
        $index['last_updated'] = now()->toDateTimeString();
        
        file_put_contents($indexFile, json_encode($index));
    }
    
    /**
     * Get list of available symbols from the index file
     *
     * @return array
     */
    private function getAvailableSymbols()
    {
        $storageDir = storage_path('app/market_data');
        $indexFile = $storageDir . '/index.json';
        
        if (file_exists($indexFile)) {
            $index = json_decode(file_get_contents($indexFile), true);
            return $index['symbols'] ?? [];
        }
        
        return ['AAPL']; // Default if no index file
    }
}
