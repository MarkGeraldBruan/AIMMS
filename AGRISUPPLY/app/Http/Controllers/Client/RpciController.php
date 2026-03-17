<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Supplies;
use App\Exports\RpciExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class RpciController extends Controller
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
                $query->where('quantity', '>', 0);
            } elseif ($request->status === 'pending') {
                $query->where('quantity', '=', 0);
            }
        }

        $supplies = $query->orderBy('created_at', 'desc')->get();

        // Get unique names for dropdown (choices of items like ballpen, etc.)
        $descriptions = Supplies::whereNotNull('name')->where('name', '!=', '')->distinct()->pluck('name')->sort()->values();

        $rpciItems = $supplies->map(function ($supply) {
            return (object) [
                'article' => $supply->name,
                'description' => $supply->description,
                'stock_number' => $supply->id, // Placeholder for stock number
                'unit_of_measure' => $supply->unit,
                'unit_value' => $supply->unit_price,
                'balance_per_card' => $supply->quantity,
                'on_hand_per_count' => $supply->quantity, // Assuming same as balance for now
                'shortage_overage_quantity' => 0, // Placeholder
                'shortage_overage_value' => 0, // Placeholder
                'remarks' => '---', // Placeholder
            ];
        });

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
            'as_of' => $request->query('as_of') ? Carbon::parse($request->query('as_of'))->format('F d, Y') : '',
            'date_range' => $dateRange,
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

        return view('client.report.rpci.index', [
            'rpciItems' => $rpciItems,
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

        $rpciItems = $supplies->map(function ($supply) {
            return (object) [
                'article' => $supply->name,
                'description' => $supply->description,
                'stock_number' => $supply->id,
                'unit_of_measure' => $supply->unit,
                'unit_value' => $supply->unit_price,
                'balance_per_card' => $supply->quantity,
                'on_hand_per_count' => $supply->quantity,
                'shortage_overage_quantity' => 0,
                'shortage_overage_value' => 0,
                'remarks' => '---',
            ];
        });

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
            'as_of' => $request->query('as_of') ? Carbon::parse($request->query('as_of'))->format('F d, Y') : '',
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

        $data = [
            'rpciItems' => $rpciItems,
            'header' => $header,
        ];

        $pdf = Pdf::loadView('client.report.rpci.pdf', $data);
        return $pdf->download('rpci_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new RpciExport($request), 'rpci_report_' . now()->format('Y-m-d') . '.xlsx');
    }
}
