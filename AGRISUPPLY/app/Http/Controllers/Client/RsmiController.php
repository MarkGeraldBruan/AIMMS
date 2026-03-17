<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Supplies;
use App\Exports\RsmiExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class RsmiController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplies::query();

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('purchase_date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }
        if ($request->filled('description')) {
            $query->where('name', 'like', '%' . $request->description . '%');
        }
        if ($request->filled('status')) {
            if ($request->status === 'issued') {
                // All supplies are considered issued for RSMI
                $query->where('quantity', '>', 0);
            } elseif ($request->status === 'pending') {
                $query->where('quantity', '=', 0);
            }
        }

        $supplies = $query->orderBy('created_at', 'desc')->get();

        $rsmiItems = $supplies->map(function ($supply) {
            return (object) [
                'issue_no' => 'RSMI-' . now()->format('Y') . '-' . str_pad($supply->id, 4, '0', STR_PAD_LEFT),
                'responsibility_center' => $supply->category ?? '---',
                'stock_no' => $supply->id,
                'item' => $supply->name,
                'description' => $supply->description,
                'unit' => $supply->unit,
                'quantity_issued' => $supply->quantity,
                'unit_cost' => $supply->unit_price,
                'amount' => $supply->unit_price * $supply->quantity,
            ];
        });

        $recapLeft = $rsmiItems->groupBy('stock_no')->map(function ($group) {
            return [
                'stock_no' => $group->first()->stock_no ?? '---',
                'quantity' => $group->sum('quantity_issued'),
            ];
        })->values();

        $recapRight = [
            'total_quantity' => $rsmiItems->sum('quantity_issued'),
            'total_cost' => $rsmiItems->sum('amount'),
            'uacs_code' => '---', // Placeholder, can be updated if UACS code is available
        ];

        // Get unique names for dropdown (choices of items like ballpen, etc.)
        $descriptions = Supplies::whereNotNull('name')->where('name', '!=', '')->distinct()->pluck('name')->sort()->values();

        // Build date range string
        $dateRange = '';
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateRange = 'From ' . Carbon::parse($request->date_from)->format('F d, Y') . ' to ' . Carbon::parse($request->date_to)->format('F d, Y');
        } elseif ($request->filled('date_from')) {
            $dateRange = 'From ' . Carbon::parse($request->date_from)->format('F d, Y');
        } elseif ($request->filled('date_to')) {
            $dateRange = 'Up to ' . Carbon::parse($request->date_to)->format('F d, Y');
        }

        // Build applied filters string
        $filters = [];
        if (!empty($dateRange)) {
            $filters[] = 'Date Range: ' . $dateRange;
        }
        if ($request->filled('description')) {
            $filters[] = 'Description: ' . $request->description;
        }
        if ($request->filled('status')) {
            $filters[] = 'Status: ' . $request->status;
        }
        $appliedFilters = implode(', ', $filters);

        $header = [
            'as_of' => $request->query('as_of') ? Carbon::parse($request->query('as_of'))->format('F Y') : '',
            'date_range' => $dateRange,
            'applied_filters' => $appliedFilters,
            'entity_name' => $request->query('entity_name', ''),
            'fund_cluster' => $request->query('fund_cluster', ''),
            'accountable_person' => $request->query('accountable_person', ''),
            'position' => $request->query('position', ''),
            'office' => $request->query('office', ''),
            'assumption_date' => $request->query('assumption_date', ''),
            'serial_no' => $request->query('serial_no', now()->format('Y-m-d')),
            'date' => $request->query('date', now()->format('F d, Y')),
            'accountability_text' => 'For which ' . ($request->query('accountable_person', '________________')) . ', ' . ($request->query('position', '________________')) . ', ' . ($request->query('office', '________________')) . ' is accountable, having assumed such accountability on ' . ($request->query('assumption_date', '________________')) . '.',
        ];

        return view('client.report.rsmi.index', [
            'rsmiItems' => $rsmiItems,
            'recapLeft' => $recapLeft,
            'recapRight' => $recapRight,
            'header' => $header,
            'descriptions' => $descriptions,
        ]);
    }

    public function exportPDF(Request $request)
    {
        $query = Supplies::query();

        // Apply same filters as index
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('purchase_date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }
        if ($request->filled('description')) {
            $query->where('name', 'like', '%' . $request->description . '%');
        }
        if ($request->filled('status')) {
            if ($request->status === 'issued') {
                $query->where('quantity', '>', 0);
            } elseif ($request->status === 'pending') {
                $query->where('quantity', '=', 0);
            }
        }

        $supplies = $query->orderBy('created_at', 'desc')->get();

        $rsmiItems = $supplies->map(function ($supply) {
            return (object) [
                'issue_no' => 'RSMI-' . now()->format('Y') . '-' . str_pad($supply->id, 4, '0', STR_PAD_LEFT),
                'responsibility_center' => $supply->category ?? '---',
                'stock_no' => $supply->id,
                'item' => $supply->name,
                'unit' => $supply->unit,
                'quantity_issued' => $supply->quantity,
                'unit_cost' => $supply->unit_price,
                'amount' => $supply->unit_price * $supply->quantity,
            ];
        });

        $recapLeft = $rsmiItems->groupBy('stock_no')->map(function ($group) {
            return [
                'stock_no' => $group->first()->stock_no ?? '---',
                'quantity' => $group->sum('quantity_issued'),
            ];
        })->values();

        $recapRight = [
            'unit_cost' => $rsmiItems->sum('unit_cost'),
            'total_cost' => $rsmiItems->sum('amount'),
            'uacs_code' => '---', // Placeholder, can be updated if UACS code is available
        ];

        $header = [
            'as_of' => $request->query('as_of') ? Carbon::parse($request->query('as_of'))->format('F Y') : '',
            'entity_name' => $request->query('entity_name', ''),
            'fund_cluster' => $request->query('fund_cluster', ''),
            'accountable_person' => $request->query('accountable_person', ''),
            'position' => $request->query('position', ''),
            'office' => $request->query('office', ''),
            'assumption_date' => $request->query('assumption_date', ''),
            'serial_no' => $request->query('serial_no', now()->format('Y-m-d')),
            'date' => $request->query('date', now()->format('F d, Y')),
            'accountability_text' => 'For which ' . ($request->query('accountable_person', '________________')) . ', ' . ($request->query('position', '________________')) . ', ' . ($request->query('office', '________________')) . ' is accountable, having assumed such accountability on ' . ($request->query('assumption_date', '________________')) . '.',
        ];

        $data = [
            'rsmiItems' => $rsmiItems,
            'recapLeft' => $recapLeft,
            'recapRight' => $recapRight,
            'header' => $header,
        ];
        $pdf = Pdf::loadView('client.report.rsmi.pdf', $data);
        return $pdf->download('rsmi_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new RsmiExport($request), 'rsmi_report_' . now()->format('Y-m-d') . '.xlsx');
    }
}
