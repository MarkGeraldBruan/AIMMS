<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Supplies;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthorized access. Admin privileges required.'], 403);
                }
                abort(403, 'Unauthorized access. Admin privileges required.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Announcement::with(['creator', 'supplies']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('client.announcement.index', compact('announcements'));
    }

    public function create()
    {
        $supplies = Supplies::orderBy('name')->get();
        return view('client.announcement.create', compact('supplies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'event_date' => 'nullable|date',
            'supplies' => 'nullable|array',
            'supplies.*.supply_id' => 'required|exists:supplies,id',
            'supplies.*.quantity' => 'required|integer|min:1'
        ]);

        $validated['created_by'] = Auth::id();

        DB::beginTransaction();
        try {
            $announcement = Announcement::create($validated);

            // Attach supplies if provided
            if ($request->has('supplies')) {
                foreach ($request->supplies as $supply) {
                    $announcement->supplies()->attach($supply['supply_id'], [
                        'quantity_needed' => $supply['quantity'],
                        'status' => 'pending'
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('client.announcement.index')
                ->with('success', 'Announcement created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create announcement: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $announcement = Announcement::with(['creator', 'supplies'])->findOrFail($id);
        return view('client.announcement.show', compact('announcement'));
    }

    public function edit(string $id)
    {
        $announcement = Announcement::with('supplies')->findOrFail($id);
        $supplies = Supplies::orderBy('name')->get();
        return view('client.announcement.edit', compact('announcement', 'supplies'));
    }

    public function update(Request $request, string $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'event_date' => 'nullable|date',
            'supplies' => 'nullable|array',
            'supplies.*.supply_id' => 'required|exists:supplies,id',
            'supplies.*.quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $announcement->update($validated);

            // Sync supplies
            $suppliesData = [];
            if ($request->has('supplies')) {
                foreach ($request->supplies as $supply) {
                    $suppliesData[$supply['supply_id']] = [
                        'quantity_needed' => $supply['quantity'],
                        'status' => 'pending'
                    ];
                }
            }
            $announcement->supplies()->sync($suppliesData);

            DB::commit();
            return redirect()->route('client.announcement.index')
                ->with('success', 'Announcement updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update announcement: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('client.announcement.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Reserve supplies for an event
     */
    public function reserveSupplies(Request $request, string $id)
    {
        $announcement = Announcement::with('supplies')->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($announcement->supplies as $supply) {
                if ($supply->quantity < $supply->pivot->quantity_needed) {
                    throw new \Exception("Insufficient stock for {$supply->name}");
                }

                // Update pivot status
                $announcement->supplies()->updateExistingPivot($supply->id, [
                    'status' => 'reserved',
                    'reserved_at' => now()
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Supplies reserved successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process stock out for event supplies
     * FIXED: Properly refresh supply model after decrement and add detailed logging
     */
    public function stockOutSupplies(Request $request, string $id)
    {
        $announcement = Announcement::with('supplies')->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($announcement->supplies as $supply) {
                $quantityNeeded = $supply->pivot->quantity_needed;
                
                // Log before stock out
                Log::info("Stock Out - Before", [
                    'supply_id' => $supply->id,
                    'supply_name' => $supply->name,
                    'current_quantity' => $supply->quantity,
                    'quantity_needed' => $quantityNeeded
                ]);
                
                if ($supply->quantity < $quantityNeeded) {
                    throw new \Exception("Insufficient stock for {$supply->name}. Available: {$supply->quantity}, Needed: {$quantityNeeded}");
                }

                // Store old quantity
                $oldQuantity = $supply->quantity;
                
                // Deduct from supply - Use direct update instead of decrement
                $newQuantity = $oldQuantity - $quantityNeeded;
                
                // Update the supply quantity directly
                DB::table('supplies')
                    ->where('id', $supply->id)
                    ->update(['quantity' => $newQuantity]);
                
                // Refresh the model to get updated quantity
                $supply->refresh();

                // Log after stock out
                Log::info("Stock Out - After", [
                    'supply_id' => $supply->id,
                    'supply_name' => $supply->name,
                    'old_quantity' => $oldQuantity,
                    'quantity_deducted' => $quantityNeeded,
                    'new_quantity' => $newQuantity,
                    'refreshed_quantity' => $supply->quantity
                ]);

                // Create stock movement record with proper table handling
                $stockMovement = new StockMovement();
                $stockMovement->supply_id = $supply->id;
                $stockMovement->type = 'out';
                $stockMovement->quantity = $quantityNeeded;
                $stockMovement->balance_after = $newQuantity;
                $stockMovement->reference = StockMovement::generateReference();
                $stockMovement->notes = "Used for event: {$announcement->title}";
                $stockMovement->office_description = $announcement->title;
                $stockMovement->save();

                // Log stock movement creation
                Log::info("Stock Movement Created", [
                    'id' => $stockMovement->id,
                    'supply_id' => $supply->id,
                    'type' => 'out',
                    'quantity' => $quantityNeeded,
                    'balance_after' => $newQuantity
                ]);

                // Update pivot table
                $announcement->supplies()->updateExistingPivot($supply->id, [
                    'status' => 'used',
                    'quantity_used' => $quantityNeeded,
                    'used_at' => now()
                ]);
            }

            DB::commit();
            
            Log::info("Stock Out Complete", [
                'announcement_id' => $announcement->id,
                'announcement_title' => $announcement->title
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Stock out processed successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Stock Out Failed", [
                'announcement_id' => $announcement->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function toggleStatus(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->status = $announcement->status === 'published' ? 'draft' : 'published';
        $announcement->save();

        return redirect()->route('client.announcement.index')
            ->with('success', 'Announcement status updated successfully!');
    }

    public function bulkPublish(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id'
        ]);

        $count = Announcement::whereIn('id', $request->ids)
                           ->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => "{$count} announcement(s) published successfully!",
            'count' => $count
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id'
        ]);

        $count = Announcement::whereIn('id', $request->ids)->count();
        Announcement::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} announcement(s) deleted successfully!",
            'count' => $count
        ]);
    }
}