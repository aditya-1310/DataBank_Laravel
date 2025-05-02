<?php

namespace App\Http\Controllers;

use App\Models\MarketData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MarketDataControllerNew extends Controller
{
    /**
     * Display a listing of the market data.
     */
    public function index(Request $request): View
    {
        $query = MarketData::query();
        
        // Apply filters
        if ($request->filled('market_name')) {
            $query->where('market_name', 'like', '%' . $request->market_name . '%');
        }
        
        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $marketData = $query->orderBy('date', 'desc')->paginate(15);
        
        return view('market-data.index', [
            'marketData' => $marketData,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Show the form for creating a new market data entry.
     */
    public function create(): View
    {
        return view('market-data.create');
    }

    /**
     * Store a newly created market data in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'market_name' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:255',
        ]);
        
        $validated['submitted_by'] = Auth::id();
        $validated['status'] = 'pending';
        
        MarketData::create($validated);
        
        return redirect()->route('market-data.index')
            ->with('success', 'Market data submitted successfully.');
    }

    /**
     * Display the specified market data.
     */
    public function show(MarketData $marketData): View
    {
        return view('market-data.show', compact('marketData'));
    }

    /**
     * Show the form for editing the specified market data.
     */
    public function edit(MarketData $marketData): View
    {
        return view('market-data.edit', compact('marketData'));
    }

    /**
     * Update the specified market data in storage.
     */
    public function update(Request $request, MarketData $marketData)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'market_name' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
        ]);
        
        $marketData->update($validated);
        
        return redirect()->route('market-data.index')
            ->with('success', 'Market data updated successfully.');
    }

    /**
     * Remove the specified market data from storage.
     */
    public function destroy(MarketData $marketData)
    {
        $marketData->delete();
        
        return redirect()->route('market-data.index')
            ->with('success', 'Market data deleted successfully.');
    }
    
    /**
     * Show the bulk import form.
     */
    public function importForm(): View
    {
        return view('market-data.import');
    }
    
    /**
     * Process the bulk import request.
     */
    public function import(Request $request)
    {
        // For testing - log the request details
        \Log::info('Import request files: ' . json_encode($request->files->all()));
        \Log::info('Import request has file: ' . $request->hasFile('file'));
        
        // Validate the request - but with a fallback for testing
        if (!$request->hasFile('file') && $request->files->count() > 0) {
            // Try to get the first file from the files array
            $files = $request->files->all();
            if (isset($files['file']) && $files['file'] instanceof \Illuminate\Http\UploadedFile) {
                // File exists but validation isn't detecting it properly
                $file = $files['file'];
                \Log::info('Found file through alternative method: ' . $file->getClientOriginalName());
            } else {
                return redirect()->back()->with('error', 'The CSV file is required.');
            }
        } else {
            // Standard validation
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:10240',
            ]);
            $file = $request->file('file');
        }
        
        $path = $file->getRealPath();
        \Log::info('File path: ' . $path);
        
        // Counters for tracking import progress
        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // Parse CSV file
        if (($handle = fopen($path, 'r')) !== false) {
            // Read header row
            $header = fgetcsv($handle, 1000, ',');
            
            // Normalize header keys
            $header = array_map(function($item) {
                return strtolower(trim($item));
            }, $header);
            
            // Debugging headers
            \Log::info('CSV Headers: ' . implode(', ', $header));
            
            // Required columns
            $requiredColumns = ['date', 'market_name', 'product_name', 'price'];
            
            // Check if required columns exist
            $missingColumns = [];
            foreach ($requiredColumns as $column) {
                if (!in_array($column, $header)) {
                    $missingColumns[] = $column;
                }
            }
            
            if (!empty($missingColumns)) {
                fclose($handle);
                return redirect()->back()
                    ->with('error', 'Missing required columns: ' . implode(', ', $missingColumns));
            }
            
            // Process data rows
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rowCount++;
                
                // Skip if row has fewer items than header
                if (count($data) < count($header)) {
                    $errors[] = "Row {$rowCount}: Invalid number of columns";
                    $errorCount++;
                    continue;
                }
                
                // Combine header with data to create associative array
                $rowData = array_combine($header, $data);
                
                // Debug row data
                \Log::info("Processing row {$rowCount}: " . json_encode($rowData));
                
                // Validate row data
                if (empty($rowData['date']) || empty($rowData['market_name']) || 
                    empty($rowData['product_name']) || !is_numeric($rowData['price'])) {
                    $errors[] = "Row {$rowCount}: Missing or invalid required data";
                    $errorCount++;
                    continue;
                }
                
                try {
                    // Create new market data entry
                    $result = \App\Models\MarketData::create([
                        'date' => date('Y-m-d', strtotime($rowData['date'])),
                        'market_name' => $rowData['market_name'],
                        'product_name' => $rowData['product_name'],
                        'price' => (float) $rowData['price'],
                        'quantity' => isset($rowData['quantity']) && is_numeric($rowData['quantity']) 
                            ? (float) $rowData['quantity'] : null,
                        'source' => $rowData['source'] ?? null,
                        'status' => 'pending',
                        'submitted_by' => \Auth::id() ?? 1,
                        'user_id' => \Auth::id() ?? 1,
                    ]);
                    
                    \Log::info("Created record: " . $result->id);
                    $successCount++;
                } catch (\Exception $e) {
                    \Log::error("Exception: " . $e->getMessage());
                    $errors[] = "Row {$rowCount}: " . $e->getMessage();
                    $errorCount++;
                }
            }
            
            fclose($handle);
        }
        
        // Prepare response message
        if ($successCount > 0) {
            $message = "Successfully imported {$successCount} records.";
            if ($errorCount > 0) {
                $message .= " There were {$errorCount} errors.";
                // Store errors in session for displaying
                session()->flash('import_errors', $errors);
            }
            return redirect()->route('market-data.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', "No records were imported. Please check your CSV file format.")
                ->with('import_errors', $errors);
        }
    }

    /**
     * Show a list of pending market data entries
     */
    public function pendingList(Request $request): View
    {
        $query = MarketData::where('status', 'pending');
        
        // Apply filters
        if ($request->filled('market_name')) {
            $query->where('market_name', 'like', '%' . $request->market_name . '%');
        }
        
        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        
        $pendingData = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('market-data.pending', [
            'pendingData' => $pendingData,
            'filters' => $request->all(),
        ]);
    }
    
    /**
     * Approve a market data entry
     */
    public function approve(MarketData $marketData)
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            return redirect()->back()->with('error', 'Only administrators can approve market data entries.');
        }
        
        $oldStatus = $marketData->status;
        $marketData->status = 'approved';
        $marketData->save();
        
        // Record the status change
        $marketData->recordStatusChange($oldStatus, 'approved', auth()->id(), 'Approved by administrator');
        
        return redirect()->back()
            ->with('success', 'Market data entry approved successfully.');
    }
    
    /**
     * Reject a market data entry
     */
    public function reject(MarketData $marketData)
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            return redirect()->back()->with('error', 'Only administrators can reject market data entries.');
        }
        
        $oldStatus = $marketData->status;
        $marketData->status = 'rejected';
        $marketData->save();
        
        // Record the status change
        $marketData->recordStatusChange($oldStatus, 'rejected', auth()->id(), 'Rejected by administrator');
        
        return redirect()->back()
            ->with('success', 'Market data entry rejected successfully.');
    }
} 