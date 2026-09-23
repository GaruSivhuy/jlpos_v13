<?php

namespace App\Exports;

use App\Exports\Concerns\StylesReportSheet;
use App\Stevebauman\Inventory\Models\InventoryStockMovement;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StockInReport implements FromView, WithEvents
{
    use StylesReportSheet;

    protected int $rowCount = 0;

    public function __construct(
        protected string $fromDate,
        protected string $toDate,
        protected ?int $mainCatId = null,
    ) {}

    public function view(): View
    {
        $results = InventoryStockMovement::query()
            ->join('inventory_stocks', 'inventory_stock_movements.stock_id', '=', 'inventory_stocks.id')
            ->join('inventories', 'inventory_stocks.inventory_id', '=', 'inventories.id')
            ->select('inventory_stock_movements.*', 'name_kh', 'pbar_code', 'inventories.id as pid')
            ->whereBetween('inventory_stock_movements.created_at', [
                $this->fromDate.' 00:00:00',
                $this->toDate.' 23:59:59',
            ])
            ->where('inventory_stock_movements.reason', 'Stock In')
            ->when($this->mainCatId, fn ($query) => $query->where('inventories.main_cat_id', $this->mainCatId))
            ->get();

        $this->rowCount = $results->count();

        return view('exports.reports.stock-in', [
            'results' => $results,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                foreach (['B' => 25, 'C' => 40, 'D' => 20, 'E' => 25, 'F' => 20, 'G' => 20, 'H' => 20] as $column => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray($this->centerStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(16);
                $event->sheet->getDelegate()->getStyle('A2')->applyFromArray($this->centerStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(12);
                $event->sheet->getDelegate()->getStyle('A3:H3')->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(12);

                $lastRow = $this->rowCount + 3;
                $event->sheet->getDelegate()->getStyle("A4:H{$lastRow}")->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Battambang')->setSize(12);
                $event->sheet->getDelegate()->getStyle("C4:C{$lastRow}")->applyFromArray($this->leftStyle())->getAlignment()->setWrapText(true);
            },
        ];
    }
}
