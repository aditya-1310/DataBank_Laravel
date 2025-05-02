<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarketDataResource;
use App\Models\MarketData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MarketDataApiController extends Controller
{
    /**
     * Display a listing of market data.
     */
    public function index(Request $request)
    {
        $query = MarketData::query();
        
        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        // Filter by location if provided
        if ($request->has('location')) {
            $query->where('location', $request->location);
        }
        
        // Filter by date range if provided
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }
        
        // Search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // For regular users, only show approved data unless it's their own
        if (!Auth::guard('sanctum')->user()?->is_admin) {
            $query->where(function($q) {
                $q->where('is_approved', true);
                
                if (Auth::guard('sanctum')->check()) {
                    $q->orWhere('user_id', Auth::guard('sanctum')->id());
                }
            });
        }
        
        $marketData = $query->latest()->paginate(10);
        
        return MarketDataResource::collection($marketData);
    }

    /**
     * Store a newly created market data in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'additional_info' => 'nullable|json',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $validated = $validator->validated();
        
        $marketData = new MarketData($validated);
        $marketData->user_id = Auth::guard('sanctum')->id();
        
        // If user is admin, auto-approve
        if (Auth::guard('sanctum')->user()?->is_admin) {
            $marketData->is_approved = true;
        }
        
        $marketData->save();
        
        return response()->json([
            'success' => true,
            'data' => new MarketDataResource($marketData)
        ], 201);
    }

    /**
     * Display the specified market data.
     */
    public function show($id)
    {
        $marketData = MarketData::findOrFail($id);
        
        // Check if the user can view this data
        if (!Auth::guard('sanctum')->user()?->is_admin && 
            !$marketData->is_approved && 
            $marketData->user_id !== Auth::guard('sanctum')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        return new MarketDataResource($marketData);
    }

    /**
     * Update the specified market data in storage.
     */
    public function update(Request $request, $id)
    {
        $marketData = MarketData::findOrFail($id);
        
        // Only the owner or admin can update
        if (!Auth::guard('sanctum')->user()?->is_admin && 
            $marketData->user_id !== Auth::guard('sanctum')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'additional_info' => 'nullable|json',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $validated = $validator->validated();
        
        // If updated by user, reset approval status
        if (!Auth::guard('sanctum')->user()?->is_admin) {
            $marketData->is_approved = false;
        }
        
        $marketData->update($validated);
        
        return response()->json([
            'success' => true,
            'data' => new MarketDataResource($marketData)
        ]);
    }

    /**
     * Remove the specified market data from storage.
     */
    public function destroy($id)
    {
        $marketData = MarketData::findOrFail($id);
        
        // Only the owner or admin can delete
        if (!Auth::guard('sanctum')->user()?->is_admin && 
            $marketData->user_id !== Auth::guard('sanctum')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $marketData->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Market data deleted successfully'
        ]);
    }
    
    /**
     * Get categories and locations for filters.
     */
    public function getFilters()
    {
        $categories = MarketData::distinct()->pluck('category');
        $locations = MarketData::distinct()->pluck('location');
        
        return response()->json([
            'categories' => $categories,
            'locations' => $locations,
        ]);
    }
} 