<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RpcSemiLowExport implements FromArray, WithEvents
{
    protected $request;
    
    // Threshold for Low Value: Below ₱50,000
    const HIGH_VALUE_THRESHOLD = 50000;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Equipment::where('unit_value', '<', self::HIGH_VALUE_THRESHOLD)
            ->orderBy('classification')
            ->orderBy('article')
            ->orderBy('property_number');

        // Apply filters if provided
        if ($this->request->filled('date_from') && $this->request->filled('date_to')) {
            $query->whereBetween('acquisition_date', [$this->request->date_from, $this->request->date_to]);
        } elseif ($this->request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $this->request->date_from);
        } elseif ($this->request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $this->request->date_to);
        }

        if ($this->request->filled('classification')) {
            $query->where('article', $this->request->classification);
        }

        if ($this->request->filled('condition')) {
            $query->where('condition', $this->request->condition);
        }

        $equipment = $query->get();

        // Build header values from request
        $entityName = $this->request->query('entity_name') ?: '';
        $accountablePerson = $this->request->query('accountable_person') ?: '';
        $position = $this->request->query('position') ?: '';
        $office = $this->request->query('office') ?: '';
        $fundCluster = $this->request->query('fund_cluster') ?: '';
        $asOfRaw = $this->request->query('as_of');
        $formattedDate = $asOfRaw ? \Carbon\Carbon::parse($asOfRaw)->format('F d, Y') : '';
        $assumptionDate = $this->request->query('assumption_date') ?: '';

        // Build applied filters string
        $filters = [];
        if ($this->request->filled('date_from') && $this->request->filled('date_to')) {
            $filters[] = 'Date Range: From ' . \Carbon\Carbon::parse($this->request->date_from)->format('F d, Y') . ' to ' . \Carbon\Carbon::parse($this->request->date_to)->format('F d, Y');
        } elseif ($this->request->filled('date_from')) {
            $filters[] = 'Date Range: From ' . \Carbon\Carbon::parse($this->request->date_from)->format('F d, Y');
        } elseif ($this->request->filled('date_to')) {
            $filters[] = 'Date Range: Up to ' . \Carbon\Carbon::parse($this->request->date_to)->format('F d, Y');
        }
        if ($this->request->filled('classification')) {
            $filters[] = 'Classification: ' . $this->request->classification;
        }
        if ($this->request->filled('condition')) {
            $filters[] = 'Condition: ' . $this->request->condition;
        }
        if ($this->request->filled('description')) {
            $filters[] = 'Description: ' . $this->request->description;
        }
        $appliedFilters = implode(', ', $filters);

        $data = [];

        // Header rows - matching screen view exactly
        $data[] = ['REPORT ON THE PHYSICAL COUNT OF SEMI-EXPENDABLE PROPERTIES (LOW VALUE)', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $data[] = [$formattedDate ? 'As of ' . $formattedDate : '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $data[] = ['Properties with unit value below ₱50,000.00', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        if (!empty($appliedFilters)) {
            $data[] = [$appliedFilters, '', '', '', '', '', '', '', '', '', '', '', '', ''];
            $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        } else {
            $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        }

        // Header grid layout
        $data[] = ['Entity Name:', $entityName, '', '', 'Accountable Officer:', $accountablePerson, '', '', 'Position:', $position, '', '', 'Office:', $office];
        $data[] = ['', '', '', '', '(Name)', '', '', '', '(Designation)', '', '', '', '(Station)', ''];
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', 'Fund Cluster:', $fundCluster];
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', ''];

        // Accountability statement
        $accountabilityText = 'For which ' . ($accountablePerson ?: '___') . ', ' . ($position ?: '___') . ', ' . ($office ?: '___') . ' is accountable, having assumed such accountability on ' . ($assumptionDate ? \Carbon\Carbon::parse($assumptionDate)->format('F d, Y') : '__________') . '.';
        $data[] = [$accountabilityText, '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', ''];

        // Table headers
        $data[] = [
            'ARTICLE/ITEM',
            'DESCRIPTION',
            'PROPERTY NUMBER',
            'UNIT OF MEASURE',
            'UNIT VALUE',
            'Acquisition Date',
            'QUANTITY per PROPERTY CARD',
            'QUANTITY per PHYSICAL COUNT',
            'SHORTAGE/OVERAGE',
            '',
            'REMARKS',
            '',
            '',
            ''
        ];
        $data[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Quantity',
            'Value',
            'Person Responsible',
            'Responsibility Center',
            'Condition of Properties',
            ''
        ];

        // Group equipment by classification and article
        $groupedEquipment = $equipment->groupBy(['classification', 'article']);

        // Table rows
        foreach ($groupedEquipment as $classification => $articles) {
            foreach ($articles as $article => $items) {
                foreach ($items as $index => $item) {
                    $description = $item->description ?: '-';
                    $propertyNumber = $item->property_number ?: '-';
                    $unitOfMeasure = $item->unit_of_measurement ?: '-';
                    $unitValue = $item->unit_value ? number_format((float) $item->unit_value, 2) : '0.00';
                    $acquisitionDate = $item->acquisition_date ? $item->acquisition_date->format('M-d') : '-';
                    $responsiblePerson = $item->responsible_person ?: 'Unknown / Book of the Accountant';
                    $location = $item->location ?: '-';
                    $condition = $item->condition ?: '-';

                    if ($index === 0) {
                        $data[] = [
                            $article ?: '-',
                            $description,
                            $propertyNumber,
                            $unitOfMeasure,
                            $unitValue,
                            $acquisitionDate,
                            1,
                            1,
                            '-',
                            '-',
                            $responsiblePerson,
                            $location,
                            $condition,
                            ''
                        ];
                    } else {
                        $data[] = [
                            '',
                            $description,
                            $propertyNumber,
                            $unitOfMeasure,
                            $unitValue,
                            $acquisitionDate,
                            1,
                            1,
                            '-',
                            '-',
                            $responsiblePerson,
                            $location,
                            $condition,
                            ''
                        ];
                    }
                }
            }
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Row 1: Title
                $sheet->mergeCells('A1:N1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Row 2: As of date
                $sheet->mergeCells('A2:N2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Row 3: Value info
                $sheet->mergeCells('A3:N3');
                $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header grid styling
                $sheet->mergeCells('B5:D5');
                $sheet->mergeCells('F5:H5');
                $sheet->mergeCells('J5:L5');
                $sheet->mergeCells('N5:N5');
                $sheet->mergeCells('B6:D6');
                $sheet->mergeCells('F6:H6');
                $sheet->mergeCells('J6:L6');
                $sheet->mergeCells('N6:N6');
                $sheet->mergeCells('M7:N7');

                // Add borders
                $sheet->getStyle('A5:D7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('E5:H7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('I5:L7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('M5:N7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Style header labels
                $sheet->getStyle('A5:A7')->getFont()->setBold(true);
                $sheet->getStyle('E5:K7')->getFont()->setBold(true);
                $sheet->getStyle('M7')->getFont()->setBold(true);

                // Accountability statement
                $sheet->mergeCells('A9:N9');
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A9')->getFont()->setSize(11);

                // Table headers
                $sheet->mergeCells('I11:J11');
                $sheet->mergeCells('K11:N11');

                $sheet->getStyle('A11:N12')->getFont()->setBold(true)->setSize(10);
                $sheet->getStyle('A11:N12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A11:N12')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A11:N12')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A11:N12')->getAlignment()->setWrapText(true);

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(11)->setRowHeight(30);
                $sheet->getRowDimension(12)->setRowHeight(40);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(50);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(12);
                $sheet->getColumnDimension('J')->setWidth(12);
                $sheet->getColumnDimension('K')->setWidth(30);
                $sheet->getColumnDimension('L')->setWidth(25);
                $sheet->getColumnDimension('M')->setWidth(18);
                $sheet->getColumnDimension('N')->setWidth(5);

                // Style classification header rows
                $highestRow = $sheet->getHighestRow();
                for ($row = 13; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    if ($cellValue && !in_array($cellValue, ['-', '1']) && !is_numeric($cellValue) && strlen($cellValue) > 10) {
                        $sheet->mergeCells('A' . $row . ':N' . $row);
                        $sheet->getStyle('A' . $row . ':N' . $row)->getFont()->setBold(true)->setSize(11);
                        $sheet->getStyle('A' . $row . ':N' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // Add borders to data cells
                $sheet->getStyle("A11:N{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A11:N{$highestRow}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);

                // Set alignment
                $sheet->getStyle('A13:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B13:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C13:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('D13:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('E13:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E13:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('F13:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G13:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I13:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K13:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('N13:N' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
