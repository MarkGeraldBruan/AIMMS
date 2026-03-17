<?php

namespace App\Exports;

use App\Models\Supplies;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;      
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SuppliesExport implements FromArray, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Supplies::query();

        // Apply same filters as in index method
        if ($this->request->has('search') && !empty($this->request->search)) {
            $query->search($this->request->search);
        }

        if ($this->request->has('category') && !empty($this->request->category)) {
            $query->where('category', $this->request->category);
        }

        if ($this->request->has('low_stock') && $this->request->low_stock == '1') {
            $query->lowStock();
        }

        $sortBy = $this->request->get('sort_by', 'name');
        $sortDirection = $this->request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $supplies = $query->get();

        $data = [];

        // Header
        $data[] = ['Supplies Export'];
        $data[] = ['Generated on: ' . now()->format('F d, Y H:i:s')];
        $data[] = [''];

        // Table header
        $data[] = [
            'ID',
            'Name',
            'Description',
            'Quantity',
            'Unit',
            'Unit Price',
            'Total Value',
            'Category',
            'Supplier',
            'Purchase Date',
            'Minimum Stock',
            'Notes'
        ];

        // Table data
        foreach ($supplies as $supply) {
            $data[] = [
                '#' . str_pad($supply->id, 4, '0', STR_PAD_LEFT),
                $supply->name,
                $supply->description ?: '',
                $supply->quantity,
                $supply->unit,
                $supply->unit_price,
                $supply->quantity * $supply->unit_price,
                $supply->category ?: '',
                $supply->supplier ?: '',
                $supply->purchase_date ? $supply->purchase_date->format('F d, Y') : '',
                $supply->minimum_stock,
                $supply->notes ?: ''
            ];
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Title
                $sheet->mergeCells('A1:L1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Header styling
                $sheet->getStyle('A4:L4')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(10); // ID
                $sheet->getColumnDimension('B')->setWidth(30); // Name
                $sheet->getColumnDimension('C')->setWidth(40); // Description
                $sheet->getColumnDimension('D')->setWidth(12); // Quantity
                $sheet->getColumnDimension('E')->setWidth(10); // Unit
                $sheet->getColumnDimension('F')->setWidth(15); // Unit Price
                $sheet->getColumnDimension('G')->setWidth(15); // Total Value
                $sheet->getColumnDimension('H')->setWidth(20); // Category
                $sheet->getColumnDimension('I')->setWidth(25); // Supplier
                $sheet->getColumnDimension('J')->setWidth(15); // Purchase Date
                $sheet->getColumnDimension('K')->setWidth(15); // Minimum Stock
                $sheet->getColumnDimension('L')->setWidth(30); // Notes

                // Data borders
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:L{$highestRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Number formatting for prices and quantities
                $sheet->getStyle('F5:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('D5:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('K5:K' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');
            }
        ];
    }
}
