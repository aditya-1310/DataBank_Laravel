<?php

namespace App\Http\Controllers;

use App\Models\MarketData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_market_data' => MarketData::count(),
            'pending_approvals' => MarketData::where('is_approved', false)->count(),
            'recent_submissions' => MarketData::latest()->take(5)->get(),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
    
    /**
     * Display pending approval submissions.
     */
    public function pendingApprovals()
    {
        $pendingData = MarketData::where('is_approved', false)
            ->with('user')
            ->latest()
            ->paginate(15);
            
        return view('admin.pending-approvals', compact('pendingData'));
    }
    
    /**
     * Display all users.
     */
    public function users()
    {
        $users = User::withCount('marketData')->paginate(15);
        
        return view('admin.users', compact('users'));
    }
    
    /**
     * Toggle user admin status.
     */
    public function toggleAdmin(User $user)
    {
        // Prevent removing yourself as admin
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot remove your own admin privileges.');
        }
        
        $user->is_admin = !$user->is_admin;
        $user->save();
        
        return redirect()->back()->with('success', 'User admin status updated successfully.');
    }
} 