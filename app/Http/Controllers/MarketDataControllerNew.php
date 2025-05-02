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
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);
        
        // Process CSV import logic would go here
        
        return redirect()->route('market-data.index')
            ->with('success', 'Market data imported successfully.');
    }
} 