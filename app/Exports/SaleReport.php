<?php

namespace App\Exports;

use App\Exports\Concerns\StylesReportSheet;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SaleReport implements FromView, WithEvents
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
        $results = Invoice::query()
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('inventories', 'invoice_items.inventory_id', '=', 'inventories.id')
            ->where('invoices.status', Invoice::PAID)
            ->when($this->branchId, fn ($query) => $query->where('invoices.branch_id', $this->branchId))
            ->whereDate('invoices.payment_date', '>=', $this->fromDate)
            ->whereDate('invoices.payment_date', '<=', $this->toDate)
            ->when($this->mainCatId, fn ($query) => $query->where('inventories.main_cat_id', $this->mainCatId))
            ->select('inventories.name_kh', 'inventories.pbar_code', 'invoice_items.price', 'invoice_items.qty', 'invoice_items.discount', 'invoice_items.discount_type', 'invoice_items.amount', 'invoice_items.invoice_id', 'invoice_items.metric_id', 'invoices.payment_gateway', 'invoices.payment_date', 'invoices.status')
            ->get();

        $this->rowCount = $results->count();

        return view('exports.reports.sale', [
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
