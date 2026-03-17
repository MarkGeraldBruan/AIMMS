<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Exports\IirupExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class IirupController extends Controller
{
    public function index(Request $request)
    {
        // Get equipment data for IIRUP report (focusing on unserviceable property)
        $query = Equipment::query();

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $request->date_to);
        }
        if ($request->filled('classification')) {
            $query->where('classification', 'like', '%' . $request->classification . '%');
        }

        // Apply disposed filter
        $disposedFilter = $request->get('disposed', 'all');
        
        // Focus on unserviceable equipment for IIRUP report
        $query->where('condition', 'Unserviceable');

        // Apply disposed filter after unserviceable filter
        if ($disposedFilter === 'disposed') {
            // Equipment with at least one disposal field filled
            $query->where(function($q) {
                $q->whereNotNull('sale')->where('sale', '!=', '')
                  ->orWhereNotNull('transfer')->where('transfer', '!=', '')
                  ->orWhereNotNull('destruction')->where('destruction', '!=', '')
                  ->orWhereNotNull('others')->where('others', '!=', '')
                  ->orWhereNotNull('appraised_value')->where('appraised_value', '!=', '')
                  ->orWhereNotNull('or_no')->where('or_no', '!=', '')
                  ->orWhereNotNull('amount')->where('amount', '!=', '');
            });
        } elseif ($disposedFilter === 'not_disposed') {
            // Equipment with all disposal fields empty
            $query->where(function($q) {
                $q->where(function($sq) {
                    $sq->whereNull('sale')->orWhere('sale', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('transfer')->orWhere('transfer', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('destruction')->orWhere('destruction', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('others')->orWhere('others', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('appraised_value')->orWhere('appraised_value', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('or_no')->orWhere('or_no', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('amount')->orWhere('amount', '=', '');
                });
            });
        }

        $ppesItems = $query->orderBy('acquisition_date', 'desc')
            ->get()
            ->map(function ($equipment) {
                return (object) [
                    'date_acquired' => $equipment->acquisition_date ? $equipment->acquisition_date->format('m/d/Y') : '---',
                    'particulars_articles' => $equipment->article . ' - ' . $equipment->description,
                    'property_no' => $equipment->property_number ?: '---',
                    'qty' => 1,
                    'unit_cost' => $equipment->unit_value,
                    'total_cost' => $equipment->unit_value,
                    'accumulated_depreciation' => 0,
                    'accumulated_impairment_losses' => 0,
                    'carrying_amount' => $equipment->unit_value,
                    'remarks' => $equipment->remarks ?: '---',
                    // Disposal columns
                    'sale' => $equipment->sale ?? '',
                    'transfer' => $equipment->transfer ?? '',
                    'destruction' => $equipment->destruction ?? '',
                    'others' => $equipment->others ?? '',
                    'total_disposal' => $equipment->total_disposal ?? '',
                    'appraised_value' => $equipment->appraised_value ?? '',
                    'or_no' => $equipment->or_no ?? '',
                    'amount' => $equipment->amount ?? '',
                ];
            });

        // Get unique classifications for filter dropdown
        $classifications = Equipment::whereNotNull('classification')
            ->distinct()
            ->pluck('classification')
            ->sort();

        $header = [
            'as_of' => $request->query('as_of') ? \Carbon\Carbon::parse($request->as_of)->format('F d, Y') : now()->format('F d, Y'),
            'entity_name' => $request->query('entity_name', ''),
            'fund_cluster' => $request->query('fund_cluster', ''),
            'accountable_person' => $request->query('accountable_person', ''),
            'position' => $request->query('position', ''),
            'office' => $request->query('office', ''),
            'assumption_date' => $request->query('assumption_date', ''),
        ];

        return view('client.report.iirup.index', compact('ppesItems', 'header', 'classifications'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new IirupExport($request), 'iirup_report_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPDF(Request $request)
    {
        // Reuse logic similar to index
        $query = Equipment::query();

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $request->date_to);
        }
        if ($request->filled('classification')) {
            $query->where('classification', 'like', '%' . $request->classification . '%');
        }

        // Apply disposed filter
        $disposedFilter = $request->get('disposed', 'all');
        
        // Focus on unserviceable equipment for IIRUP report
        $query->where('condition', 'Unserviceable');

        // Apply disposed filter after unserviceable filter
        if ($disposedFilter === 'disposed') {
            $query->where(function($q) {
                $q->whereNotNull('sale')->where('sale', '!=', '')
                  ->orWhereNotNull('transfer')->where('transfer', '!=', '')
                  ->orWhereNotNull('destruction')->where('destruction', '!=', '')
                  ->orWhereNotNull('others')->where('others', '!=', '')
                  ->orWhereNotNull('appraised_value')->where('appraised_value', '!=', '')
                  ->orWhereNotNull('or_no')->where('or_no', '!=', '')
                  ->orWhereNotNull('amount')->where('amount', '!=', '');
            });
        } elseif ($disposedFilter === 'not_disposed') {
            $query->where(function($q) {
                $q->where(function($sq) {
                    $sq->whereNull('sale')->orWhere('sale', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('transfer')->orWhere('transfer', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('destruction')->orWhere('destruction', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('others')->orWhere('others', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('appraised_value')->orWhere('appraised_value', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('or_no')->orWhere('or_no', '=', '');
                })
                ->where(function($sq) {
                    $sq->whereNull('amount')->orWhere('amount', '=', '');
                });
            });
        }

        $ppesItems = $query->orderBy('acquisition_date', 'desc')
            ->get()
            ->map(function ($equipment) {
                return (object) [
                    'date_acquired' => $equipment->acquisition_date ? $equipment->acquisition_date->format('m/d/Y') : '---',
                    'particulars_articles' => $equipment->article . ' - ' . $equipment->description,
                    'property_no' => $equipment->property_number ?: '---',
                    'qty' => 1,
                    'unit_cost' => $equipment->unit_value,
                    'total_cost' => $equipment->unit_value,
                    'accumulated_depreciation' => 0,
                    'accumulated_impairment_losses' => 0,
                    'carrying_amount' => $equipment->unit_value,
                    'remarks' => $equipment->remarks ?: '---',
                    'sale' => $equipment->sale ?? '',
                    'transfer' => $equipment->transfer ?? '',
                    'destruction' => $equipment->destruction ?? '',
                    'others' => $equipment->others ?? '',
                    'total_disposal' => $equipment->total_disposal ?? '',
                    'appraised_value' => $equipment->appraised_value ?? '',
                    'or_no' => $equipment->or_no ?? '',
                    'amount' => $equipment->amount ?? '',
                ];
            });

        $header = [
            'as_of' => $request->query('as_of') ? \Carbon\Carbon::parse($request->as_of)->format('F d, Y') : now()->format('F d, Y'),
            'entity_name' => $request->query('entity_name', ''),
            'fund_cluster' => $request->query('fund_cluster', ''),
            'accountable_person' => $request->query('accountable_person', ''),
            'position' => $request->query('position', ''),
            'office' => $request->query('office', ''),
            'assumption_date' => $request->query('assumption_date', ''),
        ];

        $data = [
            'ppesItems' => $ppesItems,
            'header' => $header,
            'serial_no' => now()->format('Y-m-d'),
            'date' => now()->format('F j, Y'),
        ];

        $pdf = Pdf::loadView('client.report.iirup.pdf', $data);
        return $pdf->download('iirup_report_' . now()->format('Y-m-d') . '.pdf');
    }
}
