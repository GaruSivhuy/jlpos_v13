<?php

namespace App\Exports;

use App\Exports\Concerns\StylesReportSheet;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StockReport implements FromView, WithEvents
{
    use StylesReportSheet;

    protected int $rowCount = 0;

    public function __construct(protected ?int $mainCatId = null) {}

    public function view(): View
    {
        $results = DB::table('inventory_stocks')
            ->join('inventory_stock_movements', 'inventory_stocks.id', '=', 'inventory_stock_movements.stock_id')
            ->join('inventories', 'inventory_stocks.inventory_id', '=', 'inventories.id')
            ->select(
                'inventory_stocks.quantity',
                'inventory_stocks.inventory_id',
                'inventories.name_kh',
                'inventories.pbar_code',
                DB::raw('SUM(CASE WHEN inventory_stock_movements.reason = "Stock In" THEN inventory_stock_movements.cost END) as total_cost'),
                DB::raw('SUM(CASE WHEN inventory_stock_movements.reason = "Stock In" THEN inventory_stock_movements.before END) as total_before'),
                DB::raw('SUM(CASE WHEN inventory_stock_movements.reason = "Stock In" THEN inventory_stock_movements.after END) as total_after'),
            )
            ->when($this->mainCatId, fn ($query) => $query->where('inventories.main_cat_id', $this->mainCatId))
            ->where('inventory_stocks.quantity', '>', 0)
            ->groupBy('inventory_stocks.id', 'inventory_stocks.inventory_id')
            ->get();

        $this->rowCount = $results->count();

        return view('exports.reports.stock', [
            'results' => $results,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                foreach (['B' => 40, 'C' => 20, 'D' => 20, 'E' => 20, 'F' => 20] as $column => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray($this->centerStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(16);
                $event->sheet->getDelegate()->getStyle('A2:G2')->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(12);

                $lastRow = $this->rowCount + 2;
                $event->sheet->getDelegate()->getStyle("A3:G{$lastRow}")->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Battambang')->setSize(12);
                $event->sheet->getDelegate()->getStyle("B3:B{$lastRow}")->applyFromArray($this->leftStyle())->getAlignment()->setWrapText(true);
            },
        ];
    }
}
