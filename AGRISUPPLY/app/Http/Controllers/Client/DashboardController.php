<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Supplies;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get dashboard statistics
        $totalUsers = User::count();
        $totalItems = Supplies::count();
        
        // Calculate items in stock (items with quantity > 0)
        $itemsInStock = Supplies::where('quantity', '>', 0)->count();
        
        // Get recently added items (last 5 items, ordered by creation date)
        $recentItems = Supplies::orderBy('created_at', 'desc')
                              ->take(5)
                              ->get();
        
        // Get low stock items (items where quantity <= minimum_stock)
        $lowStockItems = Supplies::lowStock()
                                ->orderBy('quantity', 'asc')
                                ->take(10)
                                ->get();
        
        // Get published announcements for non-admin users (last 6 announcements)
        $publishedAnnouncements = Announcement::where('status', 'published')
                                            ->orderBy('created_at', 'desc')
                                            ->take(6)
                                            ->get();
        
        // Additional statistics for the dashboard
        $totalValue = Supplies::sum(DB::raw('quantity * unit_price'));
        $lowStockCount = Supplies::lowStock()->count();
        $categoriesCount = Supplies::distinct()->count('category');
        
        // Get categories for any dropdowns
        $categories = Supplies::distinct()
                             ->whereNotNull('category')
                             ->pluck('category')
                             ->filter();
        
        return view('home', compact(
            'totalUsers',
            'totalItems',
            'itemsInStock', 
            'recentItems',
            'lowStockItems',
            'publishedAnnouncements',
            'totalValue',
            'lowStockCount',
            'categoriesCount',
            'categories'
        ));
    }
    
    /**
     * Get dashboard statistics as JSON (for AJAX requests)
     */
    public function getStats()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalItems' => Supplies::count(),
            'itemsInStock' => Supplies::where('quantity', '>', 0)->count(),
            'totalValue' => Supplies::sum(DB::raw('quantity * unit_price')),
            'lowStockCount' => Supplies::lowStock()->count(),
            'categoriesCount' => Supplies::distinct()->count('category'),
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Get recent items as JSON (for AJAX requests)
     */
    public function getRecentItems()
    {
        $recentItems = Supplies::orderBy('created_at', 'desc')
                              ->take(10)
                              ->get(['id', 'name', 'quantity', 'created_at']);

        return response()->json($recentItems);
    }
    
    /**
     * Get low stock items as JSON (for AJAX requests)
     */
    public function getLowStockItems()
    {
        $lowStockItems = Supplies::lowStock()
                                ->orderBy('quantity', 'asc')
                                ->take(10)
                                ->get(['id', 'name', 'quantity', 'minimum_stock']);
        
        return response()->json($lowStockItems);
    }
}