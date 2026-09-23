<?php

namespace App\Exports;

use App\Exports\Concerns\StylesReportSheet;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BestSaleReport implements FromView, WithEvents
{
    use StylesReportSheet;

    protected int $rowCount = 0;

    public function __construct(
        protected string $fromDate,
        protected string $toDate,
        protected ?int $branchId = null,
        protected ?int $mainCatId = null,
    ) {}

    public function view(): View
    {
        $results = DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('inventories', 'invoice_items.inventory_id', '=', 'inventories.id')
            ->where('invoices.status', Invoice::PAID)
            ->when($this->branchId, fn ($query) => $query->where('invoices.branch_id', $this->branchId))
            ->whereDate('invoices.payment_date', '>=', $this->fromDate)
            ->whereDate('invoices.payment_date', '<=', $this->toDate)
            ->when($this->mainCatId, fn ($query) => $query->where('inventories.main_cat_id', $this->mainCatId))
            ->select('inventories.id', 'inventories.name_kh', 'inventories.pbar_code', DB::raw('sum(invoice_items.quantity) as total_qty'))
            ->groupBy('invoice_items.inventory_id')
            ->orderByDesc('total_qty')
            ->get();

        $this->rowCount = $results->count() + 1;

        return view('exports.reports.best-sale', [
            'results' => $results,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                foreach (['B' => 40, 'C' => 20, 'D' => 20] as $column => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray($this->centerStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(16);
                $event->sheet->getDelegate()->getStyle('A2')->applyFromArray($this->centerStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(16);
                $event->sheet->getDelegate()->getStyle('A3:D3')->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Muol Light')->setSize(12);

                $lastRow = $this->rowCount + 2;
                $event->sheet->getDelegate()->getStyle("A4:D{$lastRow}")->applyFromArray($this->borderedCenterStyle())->getFont()->setName('Khmer OS Battambang')->setSize(12);
                $event->sheet->getDelegate()->getStyle("B4:B{$lastRow}")->applyFromArray($this->leftStyle())->getAlignment()->setWrapText(true);
            },
        ];
    }
}
