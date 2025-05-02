<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MarketData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Enable query logging
DB::enableQueryLog();

// Describe the market_data table structure
echo "\nMarket data table structure:\n";
try {
    $columns = DB::select('SHOW COLUMNS FROM market_data');
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type}) " . 
             ($column->Null === 'YES' ? 'NULLABLE' : 'NOT NULL') . 
             ($column->Default ? " DEFAULT '{$column->Default}'" : '') . "\n";
    }
} catch (\Exception $e) {
    echo "Error getting table structure: " . $e->getMessage() . "\n";
}

// Get first user
$user = User::first();
if (!$user) {
    echo "No users found! Creating one...\n";
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
}

echo "Using user: {$user->name} (ID: {$user->id})\n";

// Try to authenticate
Auth::login($user);

// Try to insert a record directly
try {
    $marketData = new MarketData([
        'date' => '2024-05-15',
        'market_name' => 'Test Market',
        'product_name' => 'Test Product',
        'price' => 10.99,
        'quantity' => 100,
        'source' => 'Manual Test',
        'status' => 'pending',
        'submitted_by' => $user->id,
        'user_id' => $user->id,
    ]);
    
    echo "Created model...\n";
    $marketData->save();
    echo "Saved model successfully! ID: {$marketData->id}\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Show the query that failed
    $queries = DB::getQueryLog();
    if (!empty($queries)) {
        $lastQuery = end($queries);
        echo "Last SQL query: {$lastQuery['query']}\n";
        echo "Bindings: " . json_encode($lastQuery['bindings']) . "\n";
    }
}

// Try to insert with raw query
try {
    echo "Trying raw SQL insert...\n";
    $result = DB::table('market_data')->insert([
        'date' => '2024-05-16',
        'market_name' => 'Test Market Raw',
        'product_name' => 'Test Product Raw',
        'price' => 9.99,
        'quantity' => 50,
        'source' => 'Manual Test Raw',
        'status' => 'pending',
        'submitted_by' => $user->id,
        'user_id' => $user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Raw insert result: " . ($result ? "Success" : "Failure") . "\n";
} catch (\Exception $e) {
    echo "Raw error: " . $e->getMessage() . "\n";
    
    // Show the query that failed
    $queries = DB::getQueryLog();
    if (!empty($queries)) {
        $lastQuery = end($queries);
        echo "Last SQL query: {$lastQuery['query']}\n";
        echo "Bindings: " . json_encode($lastQuery['bindings']) . "\n";
    }
}

// Check market data count
$count = MarketData::count();
echo "Total market data records: {$count}\n";

// List all market data records
echo "\nMarket data records:\n";
$records = MarketData::all();
foreach ($records as $record) {
    echo "ID: {$record->id}, Market: {$record->market_name}, Product: {$record->product_name}, Price: {$record->price}\n";
} 