<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Exports\RpcPpeExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class RpcPpeController extends Controller
{
    public function index(Request $request)
    {
        // Get all equipment, ordered by classification and article
        $equipmentQuery = Equipment::orderBy('classification')
            ->orderBy('article')
            ->orderBy('property_number');

        // Apply filters if provided
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $equipmentQuery->whereBetween('acquisition_date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $equipmentQuery->whereDate('acquisition_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $equipmentQuery->whereDate('acquisition_date', '<=', $request->date_to);
        }

        if ($request->filled('classification')) {
            $equipmentQuery->where('article', $request->classification);
        }

        if ($request->filled('condition')) {
            $equipmentQuery->where('condition', $request->condition);
        }

        if ($request->filled('description')) {
            $equipmentQuery->where('description', 'like', '%' . $request->description . '%');
        }

        $allEquipment = $equipmentQuery->get();

        // Build applied filters string
        $filters = [];
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filters[] = 'Date Range: From ' . \Carbon\Carbon::parse($request->date_from)->format('F d, Y') . ' to ' . \Carbon\Carbon::parse($request->date_to)->format('F d, Y');
        } elseif ($request->filled('date_from')) {
            $filters[] = 'Date Range: From ' . \Carbon\Carbon::parse($request->date_from)->format('F d, Y');
        } elseif ($request->filled('date_to')) {
            $filters[] = 'Date Range: Up to ' . \Carbon\Carbon::parse($request->date_to)->format('F d, Y');
        }
        if ($request->filled('classification')) {
            $filters[] = 'Classification: ' . $request->classification;
        }
        if ($request->filled('condition')) {
            $filters[] = 'Condition: ' . $request->condition;
        }
        if ($request->filled('description')) {
            $filters[] = 'Description: ' . $request->description;
        }
        $appliedFilters = implode(', ', $filters);

        // Group equipment by classification
        $groupedEquipment = $allEquipment->groupBy('classification');

        // Get all unique articles (equipment names) for filter dropdown
        $classifications = Equipment::whereNotNull('article')
            ->where('article', '!=', '')
            ->distinct()
            ->pluck('article')
            ->sort()
            ->values();

        // Calculate totals
        $totalValue = $allEquipment->sum('unit_value');
        $totalItems = $allEquipment->count();
        $serviceableCount = $allEquipment->where('condition', 'Serviceable')->count();
        $unserviceableCount = $allEquipment->where('condition', 'Unserviceable')->count();

        $header = [
            'as_of' => $request->query('as_of') ? \Carbon\Carbon::parse($request->query('as_of'))->format('F d, Y') : '',
            'entity_name' => $request->query('entity_name') ?: '',
            'fund_cluster' => $request->query('fund_cluster') ?: '',
            'accountable_person' => $request->query('accountable_person') ?: '',
            'position' => $request->query('position') ?: '',
            'office' => $request->query('office') ?: '',
            'assumption_date' => $request->query('assumption_date') ?: '',
            'serial_no' => $request->query('serial_no', now()->format('Y-m-d')),
            'date' => $request->query('date', now()->format('F d, Y')),
            'accountability_text' => 'For which ' . ($request->query('accountable_person') ?: '________________') . ', ' . ($request->query('position') ?: '________________') . ', ' . ($request->query('office') ?: '________________') . ' is accountable, having assumed such accountability on ' . ($request->query('assumption_date') ?: '________________') . '.',
        ];

        return view('client.report.rpc-ppe.index', compact(
            'groupedEquipment',
            'classifications',
            'totalValue',
            'totalItems',
            'serviceableCount',
            'unserviceableCount',
            'header'
        ));
    }

    public function exportPDF(Request $request)
    {
        // Get all equipment with same filters as index
        $equipmentQuery = Equipment::orderBy('classification')
            ->orderBy('article')
            ->orderBy('property_number');

        // Apply filters if provided
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $equipmentQuery->whereBetween('acquisition_date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $equipmentQuery->whereDate('acquisition_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $equipmentQuery->whereDate('acquisition_date', '<=', $request->date_to);
        }

        if ($request->filled('classification')) {
            $equipmentQuery->where('article', $request->classification);
        }

        if ($request->filled('condition')) {
            $equipmentQuery->where('condition', $request->condition);
        }

        $allEquipment = $equipmentQuery->get();

        // Group equipment by classification
        $groupedEquipment = $allEquipment->groupBy('classification');

        // Build applied filters string
        $filters = [];
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filters[] = 'Date Range: From ' . \Carbon\Carbon::parse($request->date_from)->format('F d, Y') . ' to ' . \Carbon\Carbon::parse($request->date_to)->format('F d, Y');
        } elseif ($request->filled('date_from')) {
            $filters[] = 'Date Range: From ' . \Carbon\Carbon::parse($request->date_from)->format('F d, Y');
        } elseif ($request->filled('date_to')) {
            $filters[] = 'Date Range: Up to ' . \Carbon\Carbon::parse($request->date_to)->format('F d, Y');
        }
        if ($request->filled('classification')) {
            $filters[] = 'Classification: ' . $request->classification;
        }
        if ($request->filled('condition')) {
            $filters[] = 'Condition: ' . $request->condition;
        }
        if ($request->filled('description')) {
            $filters[] = 'Description: ' . $request->description;
        }
        $appliedFilters = implode(', ', $filters);

        $header = [
            'as_of' => $request->query('as_of') ? \Carbon\Carbon::parse($request->query('as_of'))->format('F d, Y') : '',
            'entity_name' => $request->query('entity_name') ?: '',
            'fund_cluster' => $request->query('fund_cluster') ?: '',
            'accountable_person' => $request->query('accountable_person') ?: '',
            'position' => $request->query('position') ?: '',
            'office' => $request->query('office') ?: '',
            'assumption_date' => $request->query('assumption_date') ?: '',
            'serial_no' => $request->query('serial_no', now()->format('Y-m-d')),
            'date' => $request->query('date', now()->format('F d, Y')),
            'accountability_text' => 'For which ' . ($request->query('accountable_person') ?: '________________') . ', ' . ($request->query('position') ?: '________________') . ', ' . ($request->query('office') ?: '________________') . ' is accountable, having assumed such accountability on ' . ($request->query('assumption_date') ?: '________________') . '.',
        ];

        $data = [
            'groupedEquipment' => $groupedEquipment,
            'header' => $header,
        ];

        $pdf = Pdf::loadView('client.report.rpc-ppe.pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('rpc_ppe_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new RpcPpeExport($request), 'rpc_ppe_report_' . now()->format('Y-m-d') . '.xlsx');
    }
}
