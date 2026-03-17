<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IirupExport;

class PpesController extends Controller
{
    public function index(Request $request)
    {
        // Get equipment data for PPES report (only unserviceable property)
        $query = Equipment::query();

        // Always filter for unserviceable equipment only
        $query->where('condition', 'unserviceable');

        // Apply other filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('acquisition_date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $request->date_to);
        }
        if ($request->filled('classification')) {
            $query->where('article', 'like', '%' . $request->classification . '%');
        }

        // Get all equipment for PPES report
        $ppesItems = $query->orderBy('acquisition_date', 'desc')
            ->get()
            ->map(function ($equipment) {
                $unitValue = (float) $equipment->unit_value;
                return (object) [
                    'date_acquired' => $equipment->acquisition_date ? $equipment->acquisition_date->format('m/d/Y') : '---',
                    'particulars_articles' => $equipment->article . ' - ' . $equipment->description,
                    'property_no' => $equipment->property_number ?: '---',
                    'qty' => 1, // Usually 1 for equipment items
                    'unit_cost' => $unitValue,
                    'total_cost' => $unitValue,
                    'accumulated_depreciation' => 0.00, // To be calculated based on business rules
                    'accumulated_impairment_losses' => 0.00, // To be calculated based on business rules
                    'carrying_amount' => $unitValue, // Total cost minus depreciation and impairment
                    'remarks' => $equipment->remarks ?: '---',
                    // Disposal columns
                    'sale' => '',
                    'transfer' => '',
                    'destruction' => '',
                    'others' => '',
                    'total_disposal' => '',
                    'appraised_value' => '',
                    // Record of Sales
                    'or_no' => '',
                    'amount' => 0.00,
                ];
            });

        // Get all unique articles (equipment names) for filter dropdown
        $classifications = Equipment::whereNotNull('article')
            ->where('article', '!=', '')
            ->distinct()
            ->pluck('article')
            ->sort()
            ->values();

        // Build date range string
        $dateRange = '';
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateRange = 'From ' . \Carbon\Carbon::parse($request->date_from)->format('F d, Y') . ' to ' . \Carbon\Carbon::parse($request->date_to)->format('F d, Y');
        } elseif ($request->filled('date_from')) {
            $dateRange = 'From ' . \Carbon\Carbon::parse($request->date_from)->format('F d, Y');
        } elseif ($request->filled('date_to')) {
            $dateRange = 'Up to ' . \Carbon\Carbon::parse($request->date_to)->format('F d, Y');
        }

        // Build applied filters string
        $filters = [];
        if (!empty($dateRange)) {
            $filters[] = 'Date Range: ' . $dateRange;
        }
        if ($request->filled('classification')) {
            $filters[] = 'Classification: ' . $request->classification;
        }
        $appliedFilters = implode(', ', $filters);

        $header = [
            'as_of' => $request->input('as_of') ? \Carbon\Carbon::parse($request->input('as_of'))->format('F d, Y') : '',
            'date_range' => $dateRange,
            'entity_name' => $request->input('entity_name') ?: '',
            'fund_cluster' => $request->input('fund_cluster') ?: '',
            'accountable_person' => $request->input('accountable_person') ?: '',
            'position' => $request->input('position') ?: '',
            'office' => $request->input('office') ?: '',
            'assumption_date' => $request->input('assumption_date') ?: '',
        ];

        $reportType = str_contains($request->route()->getName(), 'iirup') ? 'iirup' : 'ppes';

        return view('client.report.ppes.index', [
            'ppesItems' => $ppesItems,
            'classifications' => $classifications,
            'filters' => $request->all(),
            'header' => $header,
            'reportType' => $reportType,
        ]);
    }

    public function exportPDF(Request $request)
    {
        $query = Equipment::query();

        // Always filter for unserviceable equipment only
        $query->where('condition', 'unserviceable');

        // Apply other filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('acquisition_date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $request->date_to);
        }
        if ($request->filled('classification')) {
            $query->where('article', 'like', '%' . $request->classification . '%');
        }

        $ppesItems = $query->orderBy('acquisition_date', 'desc')
            ->get()
            ->map(function ($equipment) {
                $unitValue = (float) $equipment->unit_value;
                return (object) [
                    'date_acquired' => $equipment->acquisition_date ? $equipment->acquisition_date->format('m/d/Y') : '---',
                    'particulars_articles' => $equipment->article . ' - ' . $equipment->description,
                    'property_no' => $equipment->property_number ?: '---',
                    'qty' => 1,
                    'unit_cost' => $unitValue,
                    'total_cost' => $unitValue,
                    'accumulated_depreciation' => 0.00,
                    'accumulated_impairment_losses' => 0.00,
                    'carrying_amount' => $unitValue,
                    'remarks' => $equipment->remarks ?: '---',
                    'sale' => '',
                    'transfer' => '',
                    'destruction' => '',
                    'others' => '',
                    'total_disposal' => '',
                    'appraised_value' => '',
                    'or_no' => '',
                    'amount' => 0.00,
                ];
            });

        // Build date range string
        $dateRange = '';
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateRange = 'From ' . \Carbon\Carbon::parse($request->date_from)->format('F d, Y') . ' to ' . \Carbon\Carbon::parse($request->date_to)->format('F d, Y');
        } elseif ($request->filled('date_from')) {
            $dateRange = 'From ' . \Carbon\Carbon::parse($request->date_from)->format('F d, Y');
        } elseif ($request->filled('date_to')) {
            $dateRange = 'Up to ' . \Carbon\Carbon::parse($request->date_to)->format('F d, Y');
        }

        // Build applied filters string
        $filters = [];
        if (!empty($dateRange)) {
            $filters[] = 'Date Range: ' . $dateRange;
        }
        if ($request->filled('classification')) {
            $filters[] = 'Classification: ' . $request->classification;
        }
        $appliedFilters = implode(', ', $filters);

        $header = [
            'as_of' => $request->input('as_of') ? \Carbon\Carbon::parse($request->input('as_of'))->format('F d, Y') : '',
            'entity_name' => $request->input('entity_name') ?: '',
            'fund_cluster' => $request->input('fund_cluster') ?: '',
            'accountable_person' => $request->input('accountable_person') ?: '',
            'position' => $request->input('position') ?: '',
            'office' => $request->input('office') ?: '',
            'assumption_date' => $request->input('assumption_date') ?: '',
        ];

        // Determine which PDF view to use based on the route
        $reportType = str_contains($request->route()->getName(), 'iirup') ? 'iirup' : 'ppes';
        $pdfView = 'client.report.' . $reportType . '.pdf';
        $filename = ($reportType === 'iirup' ? 'IIRUP' : 'PPES') . '_Report_' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView($pdfView, [
            'ppesItems' => $ppesItems,
            'filters' => $request->all(),
            'header' => $header,
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new IirupExport($request), 'IIRUP_Report_' . now()->format('Y-m-d') . '.xlsx');
    }
}
