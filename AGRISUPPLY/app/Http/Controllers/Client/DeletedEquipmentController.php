<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class DeletedEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check read permission
        if (!auth()->user()->hasPermission('read')) {
            abort(403, 'You do not have permission to view deleted equipment.');
        }

        $query = Equipment::onlyTrashed();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'deleted_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $deletedEquipment = $query->paginate(15);

        return view('client.deleted-equipment.index', compact('deletedEquipment'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Check read permission
        if (!auth()->user()->hasPermission('read')) {
            abort(403, 'You do not have permission to view deleted equipment details.');
        }

        $equipment = Equipment::onlyTrashed()->findOrFail($id);

        return view('client.deleted-equipment.show', compact('equipment'));
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        // Check update permission
        if (!auth()->user()->hasPermission('update')) {
            return response()->json(['error' => 'You do not have permission to restore equipment.'], 403);
        }

        $equipment = Equipment::onlyTrashed()->findOrFail($id);
        $equipment->restore();

        return redirect()->route('deleted-equipment.index')->with('success', 'Equipment restored successfully!');
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function permanentDelete($id)
    {
        // Check delete permission
        if (!auth()->user()->hasPermission('delete')) {
            return response()->json(['error' => 'You do not have permission to permanently delete equipment.'], 403);
        }

        $equipment = Equipment::onlyTrashed()->findOrFail($id);
        $equipment->forceDelete();

        return redirect()->route('deleted-equipment.index')->with('success', 'Equipment permanently deleted!');
    }
}
