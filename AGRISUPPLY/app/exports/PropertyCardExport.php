<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PropertyCardExport implements FromArray, WithEvents
{
    protected $equipment;

    public function __construct($equipment)
    {
        $this->equipment = $equipment;
    }

    public function array(): array
    {
        $data = [];

        // Header
        $data[] = ['Property Card', '', '', '', '', '', '', ''];
        $data[] = ['Property, Plant and Equipment: ' . $this->equipment->article, '', '', '', '', '', '', ''];
        $data[] = ['Description: ' . ($this->equipment->description ?: 'N/A'), '', '', '', '', '', '', ''];
        $data[] = ['Property Number: ' . $this->equipment->property_number, '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', ''];
        $data[] = ['Date', 'Reference/PAR No.', 'Receipt', '', 'Office/Officer', 'Balance Qty.', 'Amount', 'Remarks'];
        $data[] = ['', '', 'Qty.', 'Qty.', '', '', '', ''];

        // Equipment data
        $data[] = [
            $this->equipment->acquisition_date ? $this->equipment->acquisition_date->format('M d, Y') : $this->equipment->created_at->format('M d, Y'),
            $this->equipment->property_number,
            1,
            1,
            ($this->equipment->location ? $this->equipment->location : '') . ($this->equipment->location && $this->equipment->responsible_person ? ' / ' : '') . ($this->equipment->responsible_person ?: ''),
            1,
            number_format($this->equipment->unit_value, 2),
            // Ensure consistent formatting with 2 decimals
            $this->equipment->condition
        ];

        // Empty rows for future entries
        for ($i = 0; $i < 15; $i++) {
            $data[] = ['', '', '', '', '', '', '', ''];
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Title
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header styling
                $sheet->getStyle('A6:H7')->getFont()->setBold(true);
                $sheet->getStyle('A6:H7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A6:H7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(12);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(8);
                $sheet->getColumnDimension('D')->setWidth(8);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(12);

                // Borders for data
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A6:H{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Number format for amount column
                $sheet->getStyle("G8:G8")->getNumberFormat()->setFormatCode('#,##0.00');
            }
        ];
    }
}
