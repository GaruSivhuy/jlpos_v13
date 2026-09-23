<?php

namespace App\Exports;

use App\Exports\Concerns\StylesReportSheet;
use App\Models\ChangeProduct;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StockChangeReport implements FromView, WithEvents
{
    use StylesReportSheet;

    protected int $rowCount = 0;

    public function __construct(
        protected string $fromDate,
        protected string $toDate,
        protected ?int $mainCatId = null,
        protected ?int $branchId = null,
    ) {}

    public function view(): View
    {
        $results = ChangeProduct::query()
            ->join('inventories', 'change_product.inventory_id', '=', 'inventories.id')
            ->select('change_product.*', 'inventories.name_kh', 'inventories.pbar_code')
            ->when($this->mainCatId, fn ($query) => $query->where('inventories.main_cat_id', $this->mainCatId))
            ->when($this->branchId, fn ($query) => $query->where('change_product.branch_id', $this->branchId))
            ->whereDate('change_product.change_product_date', '>=', $this->fromDate)
            ->whereDate('change_product.change_product_date', '<=', $this->toDate)
            ->orderBy('change_product.change_product_date')
            ->get();

        $this->rowCount = $results->count();

        return view('exports.reports.stock-change', [
            'results' => $results,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                foreach (['B' => 20, 'C' => 40, 'D' => 20, 'E' => 20, 'F' => 20, 'G' => 20] as $column => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray($this->centerStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(16);
                $event->sheet->getDelegate()->getStyle('A2:G2')->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(12);

                $lastRow = $this->rowCount + 3;
                $event->sheet->getDelegate()->getStyle("A3:G{$lastRow}")->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Battambang')->setSize(12);
                $event->sheet->getDelegate()->getStyle("C4:C{$lastRow}")->applyFromArray($this->leftStyle())->getAlignment()->setWrapText(true);
            },
        ];
    }
}
