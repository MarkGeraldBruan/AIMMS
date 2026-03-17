<?php

namespace App\Exports;

use App\Models\StockMovement;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StockCardExport implements FromArray, WithEvents
{
    protected $supplyId;
    protected $supply;
    protected $movements;

    public function __construct($supplyId, $supply, $movements = null)
    {
        $this->supplyId = $supplyId;
        $this->supply = $supply;
        $this->movements = $movements;
    }

    public function array(): array
    {
        // Use the movements passed from the controller (what's currently displayed on the page)
        $movements = $this->movements;

        $data = [];

        // Header
        $data[] = ['Stock Card for ' . $this->supply->name, '', '', '', '', ''];
        $data[] = ['ID: #' . str_pad($this->supply->id, 4, '0', STR_PAD_LEFT), '', '', '', '', ''];
        $data[] = ['Description: ' . ($this->supply->description ?: 'N/A'), '', '', '', '', ''];
        $data[] = ['Category: ' . ($this->supply->category ?: 'Uncategorized'), '', '', '', '', ''];
        $data[] = ['Unit: ' . $this->supply->unit, '', '', '', '', ''];
        $data[] = ['Current Stock: ' . number_format($this->supply->quantity, 2), '', '', '', '', ''];
        // Ensure consistent formatting with 2 decimals for stock quantities
        $data[] = ['', '', '', '', '', ''];
        $data[] = ['Date', 'Reference', 'Receipt Qty.', 'Issue Qty.', 'Office', 'Balance Qty.'];

        // Movements - only export what's currently visible on the page
        foreach ($movements as $movement) {
            if ($movement->type === 'in') {
                $data[] = [
                    $movement->created_at->format('F d, Y'),
                    $movement->reference,
                    '+' . $movement->quantity,
                    '',
                    $movement->notes ?: 'Balance as of ' . $movement->created_at->format('F Y'),
                    $movement->balance_after
                ];
            } else {
                $data[] = [
                    $movement->created_at->format('F d, Y'),
                    $movement->reference,
                    '',
                    '-' . $movement->quantity,
                    $movement->notes ?: 'Issued',
                    $movement->balance_after
                ];
            }
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Title
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header styling
                $sheet->getStyle('A8:F8')->getFont()->setBold(true);
                $sheet->getStyle('A8:F8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:F8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(30);
                $sheet->getColumnDimension('F')->setWidth(12);

                // Borders for data
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A8:F{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}
