<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PpesExport implements FromArray, WithEvents, ShouldAutoSize, WithTitle
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Equipment::query();

        // ✅ APPLY FILTERS BASED ON PAGE
        if ($this->request->filled('date_from') && $this->request->filled('date_to')) {
            $query->whereBetween('acquisition_date', [$this->request->date_from, $this->request->date_to]);
        } elseif ($this->request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $this->request->date_from);
        } elseif ($this->request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $this->request->date_to);
        }
        if ($this->request->filled('classification')) {
            $query->where('article', 'like', '%' . $this->request->classification . '%');
        }

        $query->where('condition', 'unserviceable');
        $equipment = $query->orderBy('acquisition_date', 'desc')->get();

        // ✅ MAP DATA
        $ppesItems = $equipment->map(function ($item) {
            return [
                'date_acquired' => $item->acquisition_date ? $item->acquisition_date->format('m/d/Y') : '',
                'particulars_articles' => $item->article . ' - ' . $item->description,
                'property_no' => $item->property_number ?? '',
                'qty' => 1,
                'unit_cost' => $item->unit_value ?? 0,
                'total_cost' => $item->unit_value ?? 0,
                'accumulated_depreciation' => 0,
                'accumulated_impairment_losses' => 0,
                'carrying_amount' => $item->unit_value ?? 0,
                'remarks' => $item->remarks ?? '',
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

        $asOfRaw = $this->request->query('as_of');
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
        if (!empty($dateRange)) {
            $filters[] = 'Date Range: ' . $dateRange;
        }
        if ($this->request->filled('classification')) {
            $filters[] = 'Classification: ' . $this->request->classification;
        }
        $appliedFilters = implode(', ', $filters);

        $header = [
            'entity_name' => $this->request->entity_name ?? '',
            'as_of' => $asOfRaw ? \Carbon\Carbon::parse($asOfRaw)->format('F d, Y') : '',
            'date_range' => $dateRange,
            'applied_filters' => $appliedFilters,
            'accountable_person' => $this->request->accountable_person ?? '',
            'position' => $this->request->position ?? '',
            'office' => $this->request->office ?? '',
            'fund_cluster' => $this->request->fund_cluster ?? '',
        ];

        $data = [];

        // ✅ REPORT HEADER (PLAIN FORMAT)
        $data[] = ['INVENTORY AND INSPECTION REPORT OF UNSERVICEABLE PROPERTY'];
        $data[] = ['(ATI-RTC I)'];
        $data[] = [''];
        $data[] = ['As of: ' . ($header['as_of'] ?: '_____________')];
        if (!empty($header['date_range'])) {
            $data[] = [$header['date_range']];
        }
        $data[] = ['Entity Name: ' . ($header['entity_name'] ?: '_____________________________')];
        $data[] = [''];
        $data[] = [''];
        $data[] = [''];
        $data[] = [
            '', '', '', '', '', '', '', '', '',
            ($header['accountable_person'] ?: '_____________________________')
        ];
        $data[] = [
            '', '', '', '', '', '', '', '', '',
            '(Name of Accountable Officer)'
        ];
        $data[] = [
            '', '', '', '', '', '', '', '', '',
            ($header['position'] ?: '_____________________________')
        ];
        $data[] = [
            '', '', '', '', '', '', '', '', '',
            '(Designation)'
        ];
        $data[] = [
            '', '', '', '', '', '', '', '', '',
            ($header['office'] ?: '_____________________________')
        ];
        $data[] = [
            '', '', '', '', '', '', '', '', '',
            '(Station)'
        ];
        $data[] = [
            'Fund Cluster: ' . ($header['fund_cluster'] ?: '___________')
        ];
        $data[] = [''];

        // ✅ TABLE HEADER
        $data[] = [
            'INVENTORY', '', '', '', '', '', '', '', '', '',
            'INSPECTION and DISPOSAL', '', '', '', '',
            'Appraised Value', 'RECORD OF SALES', ''
        ];
        $data[] = [
            'Date Acquired', 'Particulars/Articles', 'Property No.', 'Qty', 'Unit Cost', 'Total Cost',
            'Accumulated Depreciation', 'Accumulated Impairment Losses', 'Carrying Amount', 'Remarks',
            'DISPOSAL', '', '', '', '',
            '', 'OR No.', 'Amount'
        ];
        $data[] = [
            '', '', '', '', '', '', '', '', '', '',
            'Sale', 'Transfer', 'Destruction', 'Others (Specify)', 'Total',
            '', '', ''
        ];
        $data[] = [
            '(1)', '(2)', '(3)', '(4)', '(5)', '(6)', '(7)', '(8)', '(9)', '(10)',
            '(11)', '(12)', '(13)', '(14)', '(15)', '(16)', '(17)', '(18)'
        ];

        // ✅ TABLE DATA
        foreach ($ppesItems as $item) {
            $data[] = [
                $item['date_acquired'],
                $item['particulars_articles'],
                $item['property_no'],
                $item['qty'],
                number_format($item['unit_cost'], 2),
                number_format($item['total_cost'], 2),
                number_format($item['accumulated_depreciation'], 2),
                number_format($item['accumulated_impairment_losses'], 2),
                number_format($item['carrying_amount'], 2),
                $item['remarks'],
                $item['sale'],
                $item['transfer'],
                $item['destruction'],
                $item['others'],
                $item['total_disposal'],
                number_format((float)$item['appraised_value'], 2),
                $item['or_no'],
                number_format((float)$item['amount'], 2),
            ];
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ✅ PAGE SETTINGS
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(0.4)->setLeft(0.5)->setRight(0.5)->setBottom(0.4);

                // ✅ FONT & ALIGNMENT
                $sheet->getStyle('A:R')->getFont()->setName('Arial')->setSize(10);
                $sheet->getDefaultRowDimension()->setRowHeight(18);

                // ✅ TITLE
                $sheet->mergeCells('A1:R1');
                $sheet->mergeCells('A2:R2');
                $sheet->getStyle('A1:A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ✅ HEADER AREA
                $sheet->getStyle('A3:R15')->applyFromArray([
                    'font' => ['size' => 10],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ✅ TABLE HEADER MERGING
                $sheet->mergeCells('A17:J17');
                $sheet->mergeCells('K17:O17');
                $sheet->mergeCells('P17:P17');
                $sheet->mergeCells('Q17:R17');

                $sheet->mergeCells('A18:A20');
                $sheet->mergeCells('B18:B20');
                $sheet->mergeCells('C18:C20');
                $sheet->mergeCells('D18:D20');
                $sheet->mergeCells('E18:E20');
                $sheet->mergeCells('F18:F20');
                $sheet->mergeCells('G18:G20');
                $sheet->mergeCells('H18:H20');
                $sheet->mergeCells('I18:I20');
                $sheet->mergeCells('J18:J20');
                $sheet->mergeCells('K18:O18');
                $sheet->mergeCells('P18:P20');
                $sheet->mergeCells('Q18:Q20');
                $sheet->mergeCells('R18:R20');

                // ✅ HEADER STYLE
                $sheet->getStyle('A17:R20')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // ✅ TABLE BODY STYLE
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A21:R{$highestRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // ✅ COLUMN WIDTHS
                $widths = [
                    'A' => 15, 'B' => 40, 'C' => 20, 'D' => 8, 'E' => 15, 'F' => 15,
                    'G' => 20, 'H' => 20, 'I' => 15, 'J' => 20,
                    'K' => 10, 'L' => 10, 'M' => 12, 'N' => 12, 'O' => 12,
                    'P' => 15, 'Q' => 12, 'R' => 15,
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // ✅ REMOVE BACKGROUND COLORS
                $sheet->getStyle("A1:R{$highestRow}")->getFill()->setFillType('none');
            }
        ];
    }

    public function title(): string
    {
        return 'PPES Report';
    }
}
