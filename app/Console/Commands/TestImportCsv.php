<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MarketDataControllerNew;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TestImportCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-import-csv {file=sample_import.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test CSV import functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting CSV import test...");
        
        // Get the CSV file path
        $filePath = $this->argument('file');
        $this->info("Using file: {$filePath}");
        
        if (!file_exists($filePath)) {
            $this->error("File {$filePath} does not exist!");
            return 1;
        }

        // Print CSV file contents
        $this->info("CSV file contents:");
        $csvContent = file_get_contents($filePath);
        $this->line($csvContent);

        // Get the first user as the authenticated user
        $user = User::first();
        if (!$user) {
            $this->error("No users found in the database!");
            return 1;
        }

        // Set the authenticated user
        Auth::login($user);
        $this->info("Logged in as user: {$user->name} (ID: {$user->id})");

        // Create an uploaded file
        $uploadedFile = new UploadedFile(
            $filePath,
            basename($filePath),
            'text/csv',
            null,
            true // Test mode
        );

        // Create a request with the uploaded file
        $request = new Request();
        $request->files->add(['file' => $uploadedFile]);
        
        $this->info("Created file upload with name: " . $uploadedFile->getClientOriginalName());

        // Create the controller and call the import method
        $controller = new MarketDataControllerNew();
        
        try {
            $this->info("Calling controller import method...");
            $result = $controller->import($request);
            $this->info("Import completed successfully.");
            
            // Check if any records were created
            $count = \App\Models\MarketData::count();
            $this->info("Total market data records in database: {$count}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
} 