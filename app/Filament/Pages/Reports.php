<?php

namespace App\Filament\Pages;

use App\Exports\BestSaleReport;
use App\Exports\CostnSaleReport;
use App\Exports\SaleReport;
use App\Exports\StockChangeReport;
use App\Exports\StockInReport;
use App\Exports\StockReport;
use App\Filament\Resources\Control\Schemas\BranchSelect;
use App\Models\MainCategory;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Reports extends Page
{
    /**
     * Report types that stream an Excel download instead of opening a print-ready page.
     */
    public const array EXCEL_REPORT_TYPES = [
        'rpt_stock_in',
        'rpt_stock',
        'rpt_sale',
        'rpt_best_sale',
        'rpt_cost_sale',
        'rpt_stock_change',
    ];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected string $view = 'filament.pages.reports';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function getTitle(): string
    {
        return __('global.reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('global.reports');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(1)
                            ->inlineLabel()
                            ->schema([
                                Select::make('branch_id')
                                    ->label(__('global.branch'))
                                    ->placeholder(__('global.report_all_branches'))
                                    ->options(fn (): array => BranchSelect::options())
                                    ->searchable(),
                                DatePicker::make('from_date')
                                    ->label(__('global.from_date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->displayFormat('d-m-Y')
                                    ->required(fn (Get $get): bool => $get('report_type') !== 'rpt_stock')
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.from_date')]),
                                    ]),
                                DatePicker::make('to_date')
                                    ->label(__('global.to_date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->displayFormat('d-m-Y')
                                    ->required(fn (Get $get): bool => $get('report_type') !== 'rpt_stock')
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.to_date')]),
                                    ]),
                                Select::make('report_type')
                                    ->label(__('global.report_type'))
                                    ->placeholder(__('global.report_select_type'))
                                    ->options($this->reportTypeOptions())
                                    ->live()
                                    ->required()
                                    ->searchable()
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.report_type')]),
                                    ]),
                                Select::make('main_cat_id')
                                    ->label(__('global.main_category'))
                                    ->options(fn (): array => MainCategory::query()->branch()->pluck('cat_name_kh', 'id')->all())
                                    ->searchable()
                                    ->required(fn (Get $get): bool => $get('report_type') === 'rpt_sale_by_main_cat_detail')
                                    ->validationMessages([
                                        'required' => __('validation.required', ['attribute' => __('global.main_category')]),
                                    ]),
                                Select::make('user_id')
                                    ->label(__('global.user'))
                                    ->options(fn (): array => User::query()->pluck('name', 'id')->all())
                                    ->searchable(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * Validates the current filters and opens the matching print-ready report in a new tab.
     */
    public function openPrint(): void
    {
        $data = $this->form->getState();

        if (in_array($data['report_type'], self::EXCEL_REPORT_TYPES, true)) {
            return;
        }

        $url = route('reports.print', array_filter([
            'report_type' => $data['report_type'],
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'main_cat_id' => $data['main_cat_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
        ]));

        $this->js('window.open('.json_encode($url).", '_blank')");
    }

    public function downloadExcel(): BinaryFileResponse
    {
        $data = $this->form->getState();
        $reportType = $data['report_type'] ?? null;
        $fromDate = filled($data['from_date'] ?? null) ? Carbon::parse($data['from_date'])->format('Y-m-d') : null;
        $toDate = filled($data['to_date'] ?? null) ? Carbon::parse($data['to_date'])->format('Y-m-d') : null;
        $mainCatId = $data['main_cat_id'] ?? null;
        $branchId = $data['branch_id'] ?? null;

        abort_unless(in_array($reportType, self::EXCEL_REPORT_TYPES, true), 404);

        return match ($reportType) {
            'rpt_stock_in' => Excel::download(new StockInReport($fromDate, $toDate, $mainCatId), 'report_stock_in.xlsx'),
            'rpt_stock' => Excel::download(new StockReport($mainCatId), 'report_stock.xlsx'),
            'rpt_sale' => Excel::download(new SaleReport($fromDate, $toDate, $branchId, $mainCatId), 'report_sale.xlsx'),
            'rpt_best_sale' => Excel::download(new BestSaleReport($fromDate, $toDate, $branchId, $mainCatId), 'report_best_sale.xlsx'),
            'rpt_cost_sale' => Excel::download(new CostnSaleReport($fromDate, $toDate, $mainCatId), 'report_cost_sale.xlsx'),
            'rpt_stock_change' => Excel::download(new StockChangeReport($fromDate, $toDate, $mainCatId, $branchId), 'report_stock_change.xlsx'),
        };
    }

    /**
     * @return array<string, array<string, string>>
     */
    protected function reportTypeOptions(): array
    {
        return [
            __('global.report_group_sale') => [
                'rpt_sale_total' => __('global.report_sale_total'),
                'rpt_sale_total_detail' => __('global.report_sale_total_detail'),
                'rpt_sale_by_payment_gateway' => __('global.report_sale_by_payment_gateway'),
                'rpt_sale_by_payment_gateway_detail' => __('global.report_sale_by_payment_gateway_detail'),
                'rpt_sale_by_main_cat_detail' => __('global.report_sale_by_main_cat_detail'),
            ],
            __('global.report_group_exchange_money') => [
                'rpt_exchange_money' => __('global.report_exchange_money'),
                'rpt_over_money' => __('global.report_over_money'),
            ],
            __('global.report_group_change_product') => [
                'rpt_exchange_product' => __('global.report_exchange_product'),
            ],
            __('global.report_group_stock') => [
                'rpt_stock_in' => __('global.report_stock_in'),
                'rpt_stock' => __('global.report_stock'),
                'rpt_sale' => __('global.report_sale_by_product'),
                'rpt_best_sale' => __('global.report_best_sale'),
                'rpt_cost_sale' => __('global.report_cost_sale'),
                'rpt_stock_change' => __('global.report_stock_change'),
            ],
        ];
    }
}
