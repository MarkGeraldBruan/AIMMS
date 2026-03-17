<?php 

namespace App\Exports;

use App\Models\Supplies;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RpciExport implements FromArray, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Supplies::query();

        // Apply filters
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

        // Format each row based on table format
        $rpciItems = $supplies->map(function ($supply) {
            return [
                'article' => $supply->name ?? '',
                'description' => $supply->description ?? '',
                'stock_number' => $supply->id ?? '',
                'unit_of_measure' => $supply->unit ?? '',
                'unit_value' => (float) ($supply->unit_price ?? 0.00), // return as float for Excel formatting
                'balance_per_card' => (string) ((int) ($supply->quantity ?? 0)), // return as string to ensure display
                'on_hand_per_count' => (string) ((int) ($supply->quantity ?? 0)), // return as string to ensure display
                'shortage_overage_quantity' => '0', // return as string to ensure display
                'shortage_overage_value' => '0.00', // return as string to ensure display
                'remarks' => '---',
            ];
        });

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

        $asOfRaw = $this->request->query('as_of');
        $header = [
            'as_of' => $asOfRaw ? \Carbon\Carbon::parse($asOfRaw)->format('F d, Y') : '',
            'date_range' => $dateRange,
            'applied_filters' => $appliedFilters,
            'fund_cluster' => $this->request->query('fund_cluster', ''),
            'accountable_person' => $this->request->query('accountable_person', ''),
            'position' => $this->request->query('position', ''),
            'office' => $this->request->query('office', ''),
            'assumption_date' => $this->request->query('assumption_date', ''),
        ];

        $data = [];

        // Report headers
        $data[] = ['REPORT ON THE PHYSICAL COUNT OF INVENTORIES', '', '', '', '', '', '', '', '', ''];
        $data[] = ['COMMON SUPPLIES AND EQUIPMENT', '', '', '', '', '', '', '', '', ''];
        $data[] = ['(REGULAR)', '', '', '', '', '', '', '', '', ''];
        $data[] = ['As of ' . $header['as_of'], '', '', '', '', '', '', '', '', ''];
        if (!empty($header['applied_filters'])) {
            $data[] = [$header['applied_filters'], '', '', '', '', '', '', '', '', ''];
        }
        $data[] = ['Fund Cluster : ' . $header['fund_cluster'], '', '', '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '', ''];

        // Accountability section
        $data[] = [
            'For which ' . ($header['accountable_person'] ?: '_________') . ', ' .
            ($header['position'] ?: '_________') . ', ' .
            ($header['office'] ?: '_________') . ' is accountable, having assumed such accountability',
            '', '', '', '', '', '', '', '', ''
        ];
        $data[] = [
            'on ' . ($header['assumption_date']
                ? \Carbon\Carbon::parse($header['assumption_date'])->format('F d, Y')
                : '__________') . '.',
            '', '', '', '', '', '', '', '', ''
        ];
        $data[] = ['', '', '', '', '', '', '', '', '', ''];

        // Table headers
        $data[] = [
            'Article', 'Description', 'Stock Number', 'Unit of Measure', 'Unit Value',
            'Balance Per Card', 'On Hand Per Count', 'Shortage/Overage', '', 'Remarks'
        ];
        $data[] = ['', '', '', '', '', '', '', 'Quantity', 'Value', ''];

        // Table body
        foreach ($rpciItems as $item) {
            $data[] = [
                $item['article'],
                $item['description'],
                $item['stock_number'],
                $item['unit_of_measure'],
                $item['unit_value'],
                $item['balance_per_card'],
                $item['on_hand_per_count'],
                $item['shortage_overage_quantity'],
                $item['shortage_overage_value'],
                $item['remarks'],
            ];
        }

        return $data;
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge and center headers
                foreach ([1, 2, 3] as $row) {
                    $sheet->mergeCells("A{$row}:J{$row}");
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(15);
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->mergeCells('A4:J4');
                $sheet->mergeCells('A5:J5');
                $sheet->getStyle('A4:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4:A5')->getFont()->setSize(12)->setBold(true);

                // Accountability section
                $sheet->mergeCells('A7:J7');
                $sheet->mergeCells('A8:J8');
                $sheet->getStyle('A7:A8')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);

                // Table header merge
                $sheet->mergeCells('H10:I10');
                $sheet->getStyle('A10:J11')->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle('A10:J11')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A10:J11')->getBorders()->getAllBorders()
                      ->setBorderStyle(Border::BORDER_THIN);

                // Column widths
                $widths = [
                    'A' => 20,
                    'B' => 45,
                    'C' => 20,
                    'D' => 20,
                    'E' => 18,
                    'F' => 18,
                    'G' => 18,
                    'H' => 18,
                    'I' => 18,
                    'J' => 25,
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Format table data (no color, plain text)
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A12:J{$highestRow}")
                      ->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A12:J{$highestRow}")
                      ->getBorders()
                      ->getAllBorders()
                      ->setBorderStyle(Border::BORDER_THIN);

                // Wrap text for Description
                $sheet->getStyle("B12:B{$highestRow}")
                      ->getAlignment()->setWrapText(true);

                // Set specific number formats for numeric columns
                $sheet->getStyle("E12:E{$highestRow}")
                      ->getNumberFormat()
                      ->setFormatCode(NumberFormat::FORMAT_NUMBER_00); // Unit Value with .00
                $sheet->getStyle("F12:F{$highestRow}")
                      ->getNumberFormat()
                      ->setFormatCode(NumberFormat::FORMAT_NUMBER); // Balance Per Card as integer
                $sheet->getStyle("G12:G{$highestRow}")
                      ->getNumberFormat()
                      ->setFormatCode(NumberFormat::FORMAT_NUMBER); // On Hand Per Count as integer
                $sheet->getStyle("H12:H{$highestRow}")
                      ->getNumberFormat()
                      ->setFormatCode(NumberFormat::FORMAT_NUMBER); // Shortage/Overage Quantity as integer
                $sheet->getStyle("I12:I{$highestRow}")
                      ->getNumberFormat()
                      ->setFormatCode(NumberFormat::FORMAT_NUMBER_00); // Shortage/Overage Value with .00
            },
        ];
    }
}
