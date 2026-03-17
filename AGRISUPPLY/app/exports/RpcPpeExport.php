<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RpcPpeExport implements FromArray, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Equipment::orderBy('classification')
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
        $data[] = ['REPORT ON THE PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT', '', '', '', '', '', '', '', '', '', '', '', '', '']; // Row 1 - Title
        $data[] = [$formattedDate ? 'As of ' . $formattedDate : '', '', '', '', '', '', '', '', '', '', '', '', '', '']; // Row 2 - Date
        if (!empty($appliedFilters)) {
            $data[] = [$appliedFilters, '', '', '', '', '', '', '', '', '', '', '', '', '']; // Row 3 - Applied Filters
            $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', '']; // Empty row (Row 4)
        } else {
            $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', '']; // Empty row (Row 3)
        }

        // Header grid layout - exactly matching screen view (4 columns)
        $data[] = ['Entity Name:', $entityName, '', '', 'Accountable Officer:', $accountablePerson, '', '', 'Position:', $position, '', '', 'Office:', $office]; // Row 4
        $data[] = ['', '', '', '', '(Name)', '', '', '', '(Designation)', '', '', '', '(Station)', '']; // Row 5
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', 'Fund Cluster:', $fundCluster]; // Row 6
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', '']; // Empty row (Row 7)

        // Accountability statement - matching screen view exactly
        $accountabilityText = 'For which ' . ($accountablePerson ?: '___') . ', ' . ($position ?: '___') . ', ' . ($office ?: '___') . ' is accountable, having assumed such accountability on ' . ($assumptionDate ? \Carbon\Carbon::parse($assumptionDate)->format('F d, Y') : '__________') . '.';
        $data[] = [$accountabilityText, '', '', '', '', '', '', '', '', '', '', '', '', '']; // Row 8
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', '']; // Empty row (Row 9)

        // Table headers - exactly matching screen view (2 rows)
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
        ]; // Row 10 - First header row
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
        ]; // Row 11 - Second header row

        // Group equipment by classification and article like the screen view
        $groupedEquipment = $equipment->groupBy(['classification', 'article']);

        // Table rows - matching screen view structure, ready for any classification/condition
        foreach ($groupedEquipment as $classification => $articles) {
            foreach ($articles as $article => $items) {
                foreach ($items as $index => $item) {
                    // Ensure all fields are properly handled for any data scenario
                    $description = $item->description ?: '-';
                    $propertyNumber = $item->property_number ?: '-';
                    $unitOfMeasure = $item->unit_of_measurement ?: '-';
                    $unitValue = $item->unit_value ? number_format((float) $item->unit_value, 2) : '0.00';
                    // Ensure all numeric values are formatted consistently with 2 decimals
                    $acquisitionDate = $item->acquisition_date ? $item->acquisition_date->format('M-d') : '-';
                    $responsiblePerson = $item->responsible_person ?: 'Unknown / Book of the Accountant';
                    $location = $item->location ?: '-';
                    $condition = $item->condition ?: '-';

                    if ($index === 0) {
                        // First item shows article name
                        $data[] = [
                            $article ?: '-',
                            $description,
                            $propertyNumber,
                            $unitOfMeasure,
                            $unitValue,
                            $acquisitionDate,
                            1, // Quantity per Property Card
                            1, // Quantity per Physical Count
                            '-', // Shortage/Overage Quantity
                            '-', // Shortage/Overage Value
                            $responsiblePerson,
                            $location,
                            $condition,
                            ''
                        ];
                    } else {
                        // Subsequent items don't show article name (merged cells effect)
                        $data[] = [
                            '',
                            $description,
                            $propertyNumber,
                            $unitOfMeasure,
                            $unitValue,
                            $acquisitionDate,
                            1, // Quantity per Property Card
                            1, // Quantity per Physical Count
                            '-', // Shortage/Overage Quantity
                            '-', // Shortage/Overage Value
                            $responsiblePerson,
                            $location,
                            $condition,
                            ''
                        ];
                    }
                }
            }
        }

        // Add example rows in the middle part of the table - more accurate positioning
        $headerRowsCount = 11; // Rows 0-10 are headers
        $dataRowsCount = count($data) - $headerRowsCount;
        $middleDataIndex = (int) ($dataRowsCount / 2);

        // Insert position: headers + middle of data rows
        $insertPosition = $headerRowsCount + $middleDataIndex;

        // Insert UNCLASSIFIED EQUIPMENT example at middle of data section (centered in middle column)
        array_splice($data, $insertPosition, 0, [
            ['', '', '', '', '', '', '', 'UNCLASSIFIED EQUIPMENT', '', '', '', '', '', '']
        ]);

        // Insert ADADADA example immediately after UNCLASSIFIED EQUIPMENT
        array_splice($data, $insertPosition + 1, 0, [
            ['ADADADA', 'Example Description', 'PROP-001', 'Piece', '1,500.00', 'Jan-15', 1, 1, '-', '-', 'John Doe', 'Main Office', 'Good', '']
        ]);

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Row 1: Title - Center and bold (matching screen view)
                $sheet->mergeCells('A1:N1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Row 2: As of date - Center and bold (matching screen view)
                $sheet->mergeCells('A2:N2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header grid styling - exactly matching screen view (4 columns)
                $sheet->mergeCells('B4:D4'); // Entity Name value
                $sheet->mergeCells('F4:H4'); // Accountable Officer value
                $sheet->mergeCells('J4:L4'); // Position value
                $sheet->mergeCells('N4:N4'); // Office value
                $sheet->mergeCells('B5:D5'); // (Name)
                $sheet->mergeCells('F5:H5'); // (Name) label
                $sheet->mergeCells('J5:L5'); // (Designation)
                $sheet->mergeCells('N5:N5'); // (Station)
                $sheet->mergeCells('M6:N6'); // Fund Cluster value

                // Add borders around header fields to create box effect (matching screen view)
                $sheet->getStyle('A4:D6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Entity Name box
                $sheet->getStyle('E4:H6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Accountable Officer box
                $sheet->getStyle('I4:L6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Position box
                $sheet->getStyle('M4:N6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Office box

                // Style header labels (matching screen view)
                $sheet->getStyle('A4:A6')->getFont()->setBold(true); // Entity Name, Accountable Officer, Fund Cluster labels
                $sheet->getStyle('E4:K6')->getFont()->setBold(true); // Other labels
                $sheet->getStyle('M6')->getFont()->setBold(true); // Fund Cluster label

                // Accountability statement - center aligned (matching screen view)
                $sheet->mergeCells('A8:N8');
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8')->getFont()->setSize(11);

                // Table headers - exactly matching screen view (2 rows)
                // Row 10: First header row - merge cells for SHORTAGE/OVERAGE and REMARKS
                $sheet->mergeCells('I10:J10'); // SHORTAGE/OVERAGE spans 2 columns
                $sheet->mergeCells('K10:N10'); // REMARKS spans 4 columns

                // Row 11: Second header row - individual columns
                $sheet->getStyle('A10:N11')->getFont()->setBold(true)->setSize(10);
                $sheet->getStyle('A10:N11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A10:N11')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A10:N11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A10:N11')->getAlignment()->setWrapText(true);

                // Set row heights (matching screen view)
                $sheet->getRowDimension(1)->setRowHeight(25); // Title
                $sheet->getRowDimension(10)->setRowHeight(30); // First header row
                $sheet->getRowDimension(11)->setRowHeight(40); // Second header row

                // Set column widths for complete text readability - ensure no text is cut off
                $sheet->getColumnDimension('A')->setWidth(30); // ARTICLE/ITEM - wider for article names
                $sheet->getColumnDimension('B')->setWidth(50); // DESCRIPTION - much wider for long descriptions
                $sheet->getColumnDimension('C')->setWidth(25); // PROPERTY NUMBER - wider for property numbers
                $sheet->getColumnDimension('D')->setWidth(20); // UNIT OF MEASURE - adequate for units
                $sheet->getColumnDimension('E')->setWidth(18); // UNIT VALUE - wider for currency values
                $sheet->getColumnDimension('F')->setWidth(18); // Acquisition Date - wider for date format
                $sheet->getColumnDimension('G')->setWidth(15); // QUANTITY per PROPERTY CARD
                $sheet->getColumnDimension('H')->setWidth(15); // QUANTITY per PHYSICAL COUNT
                $sheet->getColumnDimension('I')->setWidth(12); // SHORTAGE/OVERAGE Quantity
                $sheet->getColumnDimension('J')->setWidth(12); // SHORTAGE/OVERAGE Value
                $sheet->getColumnDimension('K')->setWidth(30); // Person Responsible - wider for names
                $sheet->getColumnDimension('L')->setWidth(25); // Responsibility Center - wider for locations
                $sheet->getColumnDimension('M')->setWidth(18); // Condition of Properties - wider for condition text
                $sheet->getColumnDimension('N')->setWidth(5); // Empty column

                // Style for classification header rows (remove green background, just bold)
                $highestRow = $sheet->getHighestRow();
                for ($row = 12; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    if ($cellValue && !in_array($cellValue, ['-', '1']) && !is_numeric($cellValue) && strlen($cellValue) > 10) {
                        // This is likely a classification header row
                        $sheet->mergeCells('A' . $row . ':N' . $row);
                        $sheet->getStyle('A' . $row . ':N' . $row)->getFont()->setBold(true)->setSize(11);
                        $sheet->getStyle('A' . $row . ':N' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // Add borders to all data cells
                $sheet->getStyle("A10:N{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Add thick border around the entire table
                $sheet->getStyle("A10:N{$highestRow}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);

                // Set proper alignment for data cells to match professional report format
                $sheet->getStyle('A12:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Article
                $sheet->getStyle('B12:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Description
                $sheet->getStyle('C12:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Property Number
                $sheet->getStyle('D12:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Unit of Measure
                $sheet->getStyle('E12:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Unit Value
                $sheet->getStyle('E12:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00'); // Format as currency
                $sheet->getStyle('F12:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Acquisition Date
                $sheet->getStyle('G12:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Quantities
                $sheet->getStyle('I12:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Shortage/Overage
                $sheet->getStyle('K12:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Remarks columns
                $sheet->getStyle('N12:N' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Empty column
            },
        ];
    }
}
