<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DeletedSupply;
use App\Models\Supplies;
use App\Models\StockAuditTrail;
use Illuminate\Http\Request;

class DeletedSupplyController extends Controller
{
    /**
     * Display deleted items history
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('read')) {
            abort(403, 'You do not have permission to view deleted items.');
        }

        $query = DeletedSupply::with('user');

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Filter by user
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->byUser($request->user_id);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $deletedItems = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get data for filters
        $categories = DeletedSupply::distinct()->pluck('category')->filter();
        $users = \App\Models\User::all();

        return view('client.deleted-supplies.index', compact('deletedItems', 'categories', 'users'));
    }

    /**
     * Restore a deleted item
     */
    public function restore($id)
    {
        if (!auth()->user()->hasPermission('create')) {
            return redirect()->back()->with('error', 'You do not have permission to restore items.');
        }

        $deletedItem = DeletedSupply::findOrFail($id);

        // Restore to supplies table
        $restored = Supplies::create([
            'name' => $deletedItem->name,
            'description' => $deletedItem->description,
            'quantity' => $deletedItem->quantity,
            'unit_price' => $deletedItem->unit_price,
            'unit' => $deletedItem->unit,
            'category' => $deletedItem->category,
            'supplier' => $deletedItem->supplier,
            'purchase_date' => $deletedItem->purchase_date,
            'minimum_stock' => $deletedItem->minimum_stock,
            'notes' => $deletedItem->notes
        ]);

        // Create audit trail for restoration
        StockAuditTrail::create([
            'user_id' => auth()->id(),
            'supply_id' => $restored->id,
            'stock_movement_id' => null,
            'action_type' => 'restored',
            'quantity' => $restored->quantity,
            'balance_before' => 0,
            'balance_after' => $restored->quantity,
            'reference' => 'RST-' . time(),
            'notes' => 'Item restored: ' . $restored->name . ' (from deletion)',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Soft delete the record from deleted_supplies
        $deletedItem->delete();

        return redirect()->route('deleted-supplies.index')
            ->with('success', 'Item restored successfully!');
    }

    /**
     * Permanently delete an item
     */
    public function permanentDelete($id)
    {
        if (!auth()->user()->hasPermission('delete')) {
            return redirect()->back()->with('error', 'You do not have permission to permanently delete items.');
        }

        $deletedItem = DeletedSupply::findOrFail($id);

        // Create final audit trail
        StockAuditTrail::create([
            'user_id' => auth()->id(),
            'supply_id' => $deletedItem->supply_id,
            'stock_movement_id' => null,
            'action_type' => 'permanently_deleted',
            'quantity' => $deletedItem->quantity,
            'balance_before' => 0,
            'balance_after' => 0,
            'reference' => 'PDEL-' . time(),
            'notes' => 'Item permanently deleted: ' . $deletedItem->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Force delete (permanent)
        $deletedItem->forceDelete();

        return redirect()->route('deleted-supplies.index')
            ->with('success', 'Item permanently deleted!');
    }

    /**
     * View details of a deleted item
     */
    public function show($id)
    {
        if (!auth()->user()->hasPermission('read')) {
            abort(403, 'You do not have permission to view deleted items.');
        }

        $deletedItem = DeletedSupply::with('user')->findOrFail($id);

        return view('client.deleted-supplies.show', compact('deletedItem'));
    }
}