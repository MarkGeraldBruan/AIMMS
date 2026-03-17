<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EquipmentExport implements FromArray, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Equipment::query();

        // Apply same filters as in index method
        if ($this->request->has('search') && $this->request->search) {
            $query->search($this->request->search);
        }

        if ($this->request->has('condition') && !empty($this->request->condition)) {
            $query->byCondition($this->request->condition);
        }

        $sortBy = $this->request->get('sort_by', 'created_at');
        $sortDirection = $this->request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $equipment = $query->get();

        $data = [];

        // Header
        $data[] = ['Equipment Export'];
        $data[] = ['Generated on: ' . now()->format('F d, Y H:i:s')];
        $data[] = [''];

        // Table header
        $data[] = [
            'ID',
            'Property Number',
            'Article',
            'Classification',
            'Description',
            'Unit of Measurement',
            'Unit Value',
            'Condition',
            'Acquisition Date',
            'Location',
            'Responsible Person',
            'Remarks'
        ];

        // Table data
        foreach ($equipment as $item) {
            $data[] = [
                '#' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                $item->property_number,
                $item->article,
                $item->classification ?: '',
                $item->description ?: '',
                $item->unit_of_measurement,
                $item->unit_value,
                $item->condition,
                $item->acquisition_date ? $item->acquisition_date->format('F d, Y') : '',
                $item->location ?: '',
                $item->responsible_person ?: '',
                $item->remarks ?: ''
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
                $sheet->getColumnDimension('B')->setWidth(18); // Property Number
                $sheet->getColumnDimension('C')->setWidth(25); // Article
                $sheet->getColumnDimension('D')->setWidth(15); // Classification
                $sheet->getColumnDimension('E')->setWidth(35); // Description
                $sheet->getColumnDimension('F')->setWidth(15); // Unit of Measurement
                $sheet->getColumnDimension('G')->setWidth(12); // Unit Value
                $sheet->getColumnDimension('H')->setWidth(12); // Condition
                $sheet->getColumnDimension('I')->setWidth(15); // Acquisition Date
                $sheet->getColumnDimension('J')->setWidth(20); // Location
                $sheet->getColumnDimension('K')->setWidth(20); // Responsible Person
                $sheet->getColumnDimension('L')->setWidth(30); // Remarks

                // Data borders
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:L{$highestRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Number formatting for unit value
                $sheet->getStyle('G5:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
            }
        ];
    }
}
