<?php

namespace App\Exports;

use App\Models\MarketData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MarketDataExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = MarketData::query();
        
        // Filter by category if provided
        if ($this->request->has('category')) {
            $query->where('category', $this->request->category);
        }
        
        // Filter by location if provided
        if ($this->request->has('location')) {
            $query->where('location', $this->request->location);
        }
        
        // Filter by date range if provided
        if ($this->request->has('date_from') && $this->request->has('date_to')) {
            $query->whereBetween('created_at', [$this->request->date_from, $this->request->date_to]);
        }
        
        // Search term
        if ($this->request->has('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // For regular users, only show approved data unless it's their own
        if (!Auth::user()->is_admin) {
            $query->where(function($q) {
                $q->where('is_approved', true)
                  ->orWhere('user_id', Auth::id());
            });
        }
        
        return $query->with('user')->latest()->get();
    }

    /**
     * @var MarketData $marketData
     */
    public function map($marketData): array
    {
        return [
            $marketData->id,
            $marketData->title,
            $marketData->product_name,
            $marketData->price,
            $marketData->location,
            $marketData->category,
            $marketData->description,
            $marketData->user->name,
            $marketData->is_approved ? 'Yes' : 'No',
            $marketData->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Product Name',
            'Price',
            'Location',
            'Category',
            'Description',
            'Submitted By',
            'Approved',
            'Submission Date',
        ];
    }
} 