<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Exports\RpciExport;
use App\Exports\RpcPpeExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('client.report.index');
    }

    // RSMI Report
    public function rsmi(Request $request)
    {
        $rsmiData = collect();  
        
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $department = $request->input('department');
        $status = $request->input('status');
        
        return view('client.report.rsmi.index', compact('rsmiData'));
    }

    // RPCI Report
    public function rpci(Request $request)
    {
        // Build same data shown in the exported PDF so the index view mirrors the export
        $query = \App\Models\Supplies::query();

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '=', $request->date_from);
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
                'article' => '---',
                'description' => $supply->name,
                'stock_number' => '---',
                'unit_of_measure' => $supply->unit,
                'unit_value' => $supply->unit_price,
                'balance_per_card' => $supply->quantity,
                'on_hand_per_count' => $supply->quantity,
                'shortage_overage_quantity' => 0,
                'shortage_overage_value' => 0,
                'remarks' => '---',
            ];
        });

        $asOfRaw = $request->query('date_from') ?: $request->query('as_of');
        $header = [
            // as_of shown formatted, but allow empty for other fields so the view can render blanks
            'as_of' => $asOfRaw ? \Carbon\Carbon::parse($asOfRaw)->format('F d, Y') : now()->format('F d, Y'),
            'entity_name' => $request->query('entity_name', ''),
            'fund_cluster' => $request->query('fund_cluster', ''),
            'accountable_person' => $request->query('accountable_person', ''),
            'position' => $request->query('position', ''),
            'office' => $request->query('office', ''),
            'assumption_date' => $request->query('assumption_date', ''),
        ];

        return view('client.report.rpci.index', compact('rpciItems', 'header'));
    }

    // RPCI export - PDF
    public function exportRpciPdf(Request $request)
    {
        // Reuse logic similar to RpciController
        $query = \App\Models\Supplies::query();

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

        $asOfRaw = $request->query('date_from') ?: $request->query('as_of');
        $header = [
            'as_of' => $asOfRaw ? \Carbon\Carbon::parse($asOfRaw)->format('F d, Y') : now()->format('F d, Y'),
            'entity_name' => $request->query('entity_name', ''),
            'fund_cluster' => $request->query('fund_cluster', ''),
            'accountable_person' => $request->query('accountable_person', ''),
            'position' => $request->query('position', ''),
            'office' => $request->query('office', ''),
            'assumption_date' => $request->query('assumption_date', ''),
        ];

        $data = [
            'rpciItems' => $rpciItems,
            'header' => $header,
            'serial_no' => now()->format('Y-m-d'),
            'date' => now()->format('F j, Y'),
        ];

        $pdf = Pdf::loadView('client.report.rpci.pdf', $data);
        return $pdf->download('rpci_report_' . now()->format('Y-m-d') . '.pdf');
    }

    // RPCI export - Excel
    public function exportRpciExcel(Request $request)
    {
        return Excel::download(new RpciExport($request), 'rpci_report_' . now()->format('Y-m-d') . '.xlsx');
    }

    // PPES Report
    public function ppes(Request $request)
    {
        // Get equipment data for PPES report (focusing on unserviceable property)
        $query = Equipment::query();

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $request->date_to);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->filled('classification')) {
            $query->where('classification', 'like', '%' . $request->classification . '%');
        }

        // Focus on unserviceable equipment for PPES report
        $ppesItems = $query->where('condition', 'Unserviceable')
            ->orderBy('acquisition_date', 'desc')
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
                    'sale' => '',
                    'transfer' => '',
                    'destruction' => '',
                    'others' => '',
                    'total_disposal' => '',
                    'appraised_value' => '',
                    'or_no' => '',
                    'amount' => '',
                ];
            });

        return view('client.report.ppes.index', [
            'ppesItems' => $ppesItems,
            'filters' => $request->all()
        ]);
    }

    // RPC PPE Report - DEPRECATED: Use RpcPpeController instead
    public function rpcPpe(Request $request)
    {
        return redirect()->route('client.report.rpc-ppe');
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}