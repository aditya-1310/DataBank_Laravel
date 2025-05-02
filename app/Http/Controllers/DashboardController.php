<?php

namespace App\Http\Controllers;

use App\Models\MarketData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with market data statistics.
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_entries' => MarketData::count(),
            'approved_entries' => MarketData::where('is_approved', true)->count(),
            'pending_entries' => MarketData::where('is_approved', false)->count(),
            'my_entries' => MarketData::where('user_id', Auth::id())->count(),
            'symbols_count' => MarketData::distinct('symbol')->count('symbol'),
        ];
        
        // Top symbols
        $topSymbols = MarketData::select('symbol', DB::raw('count(*) as count'))
            ->groupBy('symbol')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
            
        // Latest approved market data
        $latestMarketData = MarketData::where('is_approved', true)
            ->latest()
            ->limit(5)
            ->get();
            
        // My recent submissions
        $myRecentSubmissions = MarketData::where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();
            
        // Price statistics
        $priceStats = [
            'avg_open' => MarketData::avg('open'),
            'min_open' => MarketData::min('open'),
            'max_open' => MarketData::max('open'),
            'avg_close' => MarketData::avg('close'),
            'min_close' => MarketData::min('close'),
            'max_close' => MarketData::max('close'),
        ];
        
        return view('dashboard', compact(
            'stats', 
            'topSymbols', 
            'latestMarketData', 
            'myRecentSubmissions',
            'priceStats'
        ));
    }
} 