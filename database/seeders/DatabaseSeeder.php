<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MarketData;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);
        
        // Create regular user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_admin' => false,
        ]);
        
        // Create some sample market data
        $symbols = ['AAPL', 'GOOGL', 'MSFT', 'AMZN', 'TSLA'];
        $date = now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            foreach ($symbols as $symbol) {
                $open = rand(100, 1000) / 10;
                $close = rand(max(($open * 0.9), ($open * 0.8)), ($open * 1.2)) / 10;
                $high = rand(max($open, $close), max($open, $close) * 1.1) / 10;
                $low = rand(min($open, $close) * 0.9, min($open, $close)) / 10;
                
                MarketData::create([
                    'user_id' => $i % 3 === 0 ? $admin->id : $user->id,
                    'date' => clone $date,
                    'symbol' => $symbol,
                    'open' => $open,
                    'high' => $high,
                    'low' => $low,
                    'close' => $close,
                    'volume' => rand(10000, 1000000),
                    'is_approved' => $i % 4 === 0 ? true : false,
                ]);
            }
            
            $date->addDay();
        }
    }
}
