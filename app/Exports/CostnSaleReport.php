<?php

namespace App\Exports;

use App\Exports\Concerns\StylesReportSheet;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CostnSaleReport implements FromView, WithEvents
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
        $from = $this->fromDate.' 00:00:00';
        $to = $this->toDate.' 23:59:59';

        $results = DB::table('inventory_stock_movements')
            ->join('inventory_stocks', 'inventory_stock_movements.stock_id', '=', 'inventory_stocks.id')
            ->join('inventories', 'inventory_stocks.inventory_id', '=', 'inventories.id')
            ->select(
                'inventories.name_kh',
                'inventories.pbar_code',
                DB::raw('SUM(CASE WHEN inventory_stock_movements.reason = "Stock In" THEN inventory_stock_movements.cost END) as total_cost'),
                DB::raw('SUM(CASE WHEN inventory_stock_movements.reason = "Stock In" THEN inventory_stock_movements.before END) as total_before'),
                DB::raw('SUM(CASE WHEN inventory_stock_movements.reason = "Stock In" THEN inventory_stock_movements.after END) as total_after'),
            )
            ->selectSub(fn (Builder $query) => $query->from('invoice_items')
                ->selectRaw('SUM(invoice_items.quantity)')
                ->whereColumn('invoice_items.inventory_id', 'inventory_stocks.inventory_id')
                ->whereBetween('invoice_items.created_at', [$from, $to])
                ->whereNull('invoice_items.deleted_at'), 'total_sale_qty')
            ->selectSub(fn (Builder $query) => $query->from('invoice_items')
                ->selectRaw('SUM(invoice_items.amount)')
                ->whereColumn('invoice_items.inventory_id', 'inventory_stocks.inventory_id')
                ->whereBetween('invoice_items.created_at', [$from, $to])
                ->whereNull('invoice_items.deleted_at'), 'total_sale_amount')
            ->when($this->mainCatId, fn ($query) => $query->where('inventories.main_cat_id', $this->mainCatId))
            ->groupBy('inventory_stocks.inventory_id')
            ->get();

        $this->rowCount = $results->filter(fn ($row) => $row->total_sale_qty > 0)->count();

        return view('exports.reports.cost-and-sale', [
            'results' => $results,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                foreach (['B' => 40, 'C' => 30, 'D' => 30, 'E' => 30, 'F' => 30, 'G' => 30, 'H' => 30, 'I' => 30, 'J' => 20] as $column => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray($this->centerStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(16);
                $event->sheet->getDelegate()->getStyle('A2')->applyFromArray($this->centerStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(12);
                $event->sheet->getDelegate()->getStyle('A3:J3')->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(12);

                $lastRow = $this->rowCount + 3;
                $event->sheet->getDelegate()->getStyle("A4:J{$lastRow}")->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Battambang')->setSize(12);
                $event->sheet->getDelegate()->getStyle("B4:B{$lastRow}")->applyFromArray($this->leftStyle())->getAlignment()->setWrapText(true);
            },
        ];
    }
}
