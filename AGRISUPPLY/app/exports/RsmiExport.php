<?php

namespace App\Exports;

use App\Models\Supplies;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RsmiExport implements FromArray, WithEvents
{
    protected $request;
    protected $dataRowCount = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Supplies::query();

        if ($this->request->filled('date_from') && $this->request->filled('date_to')) {
            $query->whereBetween('purchase_date', [$this->request->date_from, $this->request->date_to]);
        } elseif ($this->request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $this->request->date_from);
        } elseif ($this->request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $this->request->date_to);
        }
        if ($this->request->filled('description')) {
            $query->where('name', 'like', '%' . $this->request->description . '%');
        }
        if ($this->request->filled('status')) {
            if ($this->request->status === 'issued') {
                $query->where('quantity', '>', 0);
            } elseif ($this->request->status === 'pending') {
                $query->where('quantity', '=', 0);
            }
        }

        $supplies = $query->orderBy('created_at', 'desc')->get();

        $rsmiItems = $supplies->map(function ($supply) {
            $unitCost = $supply->unit_price ?? 0;
            $amount = $unitCost * ($supply->quantity ?? 0);

            // Always include as numeric values, even if 0
            $unitCostVal = (float) $unitCost;
            $amountVal = (float) $amount;
            $qtyVal = (int) ($supply->quantity ?? 0);

            return [
                'issue_no' => 'RSMI-' . now()->format('Y') . '-' . str_pad($supply->id, 4, '0', STR_PAD_LEFT),
                'responsibility_center' => $supply->category ?? '---',
                'stock_no' => $supply->id ?? '---',
                'item' => $supply->name,
                'unit' => $supply->unit,
                'quantity_issued' => (string) $qtyVal, // return as string to ensure display
                'unit_cost' => $unitCostVal,
                'amount' => $amountVal,
            ];
        });

        // store data row count for event styling
        $this->dataRowCount = $rsmiItems->count();

        // Build date range string
        $dateRange = '';
        if ($this->request->filled('date_from') && $this->request->filled('date_to')) {
            $dateRange = 'From ' . \Carbon\Carbon::parse($this->request->date_from)->format('F d, Y') . ' to ' . \Carbon\Carbon::parse($this->request->date_to)->format('F d, Y');
        } elseif ($this->request->filled('date_from')) {
            $dateRange = 'From ' . \Carbon\Carbon::parse($this->request->date_from)->format('F d, Y');
        } elseif ($this->request->filled('date_to')) {
            $dateRange = 'Up to ' . \Carbon\Carbon::parse($this->request->date_to)->format('F d, Y');
        }

        // Build applied filters string
        $filters = [];
        if ($this->request->filled('description')) {
            $filters[] = 'Description: ' . $this->request->description;
        }
        if ($this->request->filled('status')) {
            $filters[] = 'Status: ' . $this->request->status;
        }
        $appliedFilters = implode(', ', $filters);

        // Header info (use input() so it works with POST as well)
        $entityName = $this->request->input('entity_name', '');
        $accountablePerson = $this->request->input('accountable_person', '');
        $position = $this->request->input('position', '');
        $office = $this->request->input('office', '');
        $fundCluster = $this->request->input('fund_cluster', '');
        $asOfRaw = $this->request->input('as_of');
        $asOfMonth = $asOfRaw ? \Carbon\Carbon::parse($asOfRaw)->format('F Y') : '';
        $assumptionDate = $this->request->input('assumption_date', '');

        $data = [];

        // Title & Header (rows 1-6)
        $data[] = ['Republic of the Philippines'];
        $data[] = [$entityName ?: '____________________________'];
        $data[] = ['Report of Supplies and Materials Issued'];
        $data[] = ['For the Month of ' . $asOfMonth];
        if (!empty($appliedFilters)) {
            $data[] = [$appliedFilters];
        }
        $data[] = ['Fund Cluster: ' . ($fundCluster ?: '________________________')];
        $data[] = ['']; // row 6 blank

        // Accountability line (row 7)
        $accountabilityText = 'For which ' . ($accountablePerson ?: '_________________') . ', ' .
            ($position ?: '_________________') . ', ' .
            ($office ?: '_________________') . ' is accountable, having assumed such accountability on ' .
            ($assumptionDate ? \Carbon\Carbon::parse($assumptionDate)->format('F d, Y') : '_________________') . '.';
        $data[] = [$accountabilityText];

        $data[] = ['']; // row 8 blank

        // Table header (row 9)
        $data[] = [
            'RIS No.',
            'Responsibility Center Code',
            'Stock No.',
            'Item',
            'Unit',
            'Quantity Issued',
            'Unit Cost',
            'Amount'
        ];

        // Table rows start at row 10
        foreach ($rsmiItems as $item) {
            $data[] = [
                $item['issue_no'],
                $item['responsibility_center'],
                $item['stock_no'],
                $item['item'],
                $item['unit'],
                $item['quantity_issued'], // integer
                number_format($item['unit_cost'], 2), // formatted as 0.00
                number_format($item['amount'], 2), // formatted as 0.00
            ];
        }

        // After table: one blank row
        $data[] = [''];

        // Recapitulation header row
        $data[] = ['Recapitulation:', '', '', '', 'Recapitulation:', '', '', ''];
        $data[] = ['Stock No.', 'Quantity', '', '', 'Unit Cost', 'Total Cost', 'UACS Object Code', ''];

        // Prepare recapitulation left (group by stock_no)
        $recapLeft = $rsmiItems->groupBy('stock_no')->map(function ($group, $stock) {
            $qty = $group->sum(function ($i) {
                return $i['quantity_issued'] ?? 0;
            });
            return [
                'stock_no' => $stock,
                'quantity' => (int) $qty,
            ];
        })->values();

        // Prepare recapitulation right totals
        $recapRightTotals = [
            'unit_cost_total' => $rsmiItems->sum(function ($i) {
                return $i['unit_cost'] ?? 0;
            }),
            'amount_total' => $rsmiItems->sum(function ($i) {
                return $i['amount'] ?? 0;
            }),
            'uacs' => '---'
        ];

        // We will print as many rows as $recapLeft (at least 1)
        $maxRecapRows = max($recapLeft->count(), 1);
        for ($i = 0; $i < $maxRecapRows; $i++) {
            $leftStock = $recapLeft[$i]['stock_no'] ?? '---';
            $leftQty = $recapLeft[$i]['quantity'] ?? 0;

            // Show right totals always, even if 0
            $rightUnit = ($i === 0) ? (float) $recapRightTotals['unit_cost_total'] : null;
            $rightTotal = ($i === 0) ? (float) $recapRightTotals['amount_total'] : null;
            $rightUacs = ($i === 0) ? $recapRightTotals['uacs'] : null;

            $data[] = [
                $leftStock,
                $leftQty,
                '',
                '',
                number_format($rightUnit, 2),
                number_format($rightTotal, 2),
                $rightUacs,
                ''
            ];
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Rows: header occupies 1-7, blank 8, table header row 9, data starts row 10
                $tableHeaderRow = 9;
                $tableFirstDataRow = 10;
                $dataEndRow = $tableHeaderRow + $this->dataRowCount; // last data row (if zero rows, equals 9)

                // compute highestRow for final ranges (some recaps and blank rows exist)
                $highestRow = $sheet->getHighestRow();

                // --- Header styling & merging ---
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'font' => ['bold' => true, 'size' => 14],
                ]);

                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'font' => ['size' => 12],
                ]);

                $sheet->mergeCells('A3:H3');
                $sheet->getStyle('A3')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'font' => ['bold' => true, 'size' => 13],
                ]);

                $sheet->mergeCells('A4:H4');
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A5:H5');
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Accountability line (center + wrap + row height)
                $sheet->mergeCells('A7:H7');
                $sheet->getStyle('A7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'font' => ['size' => 11]
                ]);
                $sheet->getRowDimension(7)->setRowHeight(36);

                // --- Table header ---
                $sheet->getStyle("A{$tableHeaderRow}:H{$tableHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);

                // --- Column widths ---
                $sheet->getColumnDimension('A')->setWidth(30); // RIS No.
                $sheet->getColumnDimension('B')->setWidth(30); // Responsibility Center Code
                $sheet->getColumnDimension('C')->setWidth(18); // Stock No.
                $sheet->getColumnDimension('D')->setWidth(55); // Item
                $sheet->getColumnDimension('E')->setWidth(12); // Unit
                $sheet->getColumnDimension('F')->setWidth(18); // Quantity Issued
                $sheet->getColumnDimension('G')->setWidth(20); // Unit Cost
                $sheet->getColumnDimension('H')->setWidth(20); // Amount

                // --- Data table borders (only where data exists) ---
                if ($this->dataRowCount > 0) {
                    $sheet->getStyle("A{$tableHeaderRow}:H{$dataEndRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ]
                    ]);
                } else {
                    // still put border on header row
                    $sheet->getStyle("A{$tableHeaderRow}:H{$tableHeaderRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ]
                    ]);
                }

                // --- Number formatting: Unit Cost (G) and Amount (H) only for data rows and recap rows ---
                $unitCostRange = "G{$tableFirstDataRow}:G{$dataEndRow}";
                $amountRange = "H{$tableFirstDataRow}:H{$dataEndRow}";
                if ($this->dataRowCount > 0) {
                    $sheet->getStyle($unitCostRange)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle($amountRange)->getNumberFormat()->setFormatCode('#,##0.00');
                }

                // Also apply number format to recapitulation right side (we'll approximate rows)
                // Find recap start row: it's dataEndRow + 2 (one blank) + 1 (recap header) + 1 (recap labels) => +3
                $recapStartRow = $dataEndRow + 2;
                $recapDataStartRow = $recapStartRow + 2; // the first recap data row
                $recapDataEndRow = $highestRow; // safe upper bound (we only format cells that exist)
                // Apply formatting to potential recap columns if they exist
                $sheet->getStyle("E{$recapDataStartRow}:F{$recapDataEndRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                // --- Center all used cells vertically & horizontally ---
                $sheet->getStyle("A1:H{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                // --- Recapitulation section merging & borders ---
                $sheet->mergeCells("A{$recapStartRow}:B{$recapStartRow}");
                $sheet->mergeCells("E{$recapStartRow}:G{$recapStartRow}");
                $sheet->getStyle("A{$recapStartRow}:G{$recapStartRow}")->getFont()->setBold(true);

                // Add border boxes for left and right recap (3 rows high to match layout)
                $sheet->getStyle("A" . ($recapStartRow + 1) . ":B" . ($recapStartRow + 3))
                      ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("E" . ($recapStartRow + 1) . ":G" . ($recapStartRow + 3))
                      ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Make quantity center in recap left
                $sheet->getStyle("B" . ($recapStartRow + 1) . ":B" . ($recapStartRow + 3))
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Done.
            }
        ];
    }
}
 