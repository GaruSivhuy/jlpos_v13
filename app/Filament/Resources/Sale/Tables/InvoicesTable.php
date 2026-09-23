<?php

namespace App\Filament\Resources\Sale\Tables;

use App\Filament\Resources\Inventories\Tables\Concerns\HasDateRangeFilter;
use App\Filament\Resources\Sale\InvoiceResource;
use App\Models\Invoice;
use App\Models\PaymentGateway;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
{
    use HasDateRangeFilter;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.invoice_code'))
                    ->formatStateUsing(fn ($record) => $record->invoice_code)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $digits = preg_replace('/\D/', '', $search);

                        return $digits === ''
                            ? $query->whereRaw('1 = 0')
                            : $query->where('invoices.id', 'like', '%'.ltrim($digits, '0').'%');
                    })
                    ->sortable(),
                TextColumn::make('paymentGateway.name')->label(__('global.payment_gateway')),
                TextColumn::make('payment_date')->label(__('global.payment_date'))->date('d/m/Y')->sortable(),
                TextColumn::make('currency')->label(__('global.currency')),
                TextColumn::make('gross_amount')
                    ->label(__('global.amount'))
                    ->state(fn (Invoice $record) => $record->total + $record->discount)
                    ->numeric(2),
                TextColumn::make('discount')->label(__('global.discount'))->numeric(2),
                TextColumn::make('total')->label(__('global.total'))->numeric(2)->sortable(),
                TextColumn::make('status')
                    ->label(__('global.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => static::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ((int) $state) {
                        Invoice::PAID => 'success',
                        Invoice::CANCEL => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('customer.name_kh')->label(__('global.customer'))->placeholder(__('global.general')),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('invoiced_at')->label(__('global.invoiced_at'))->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                static::getDataFilter(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormWidth('4xl')
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px')
            ->recordActions([
                ActionGroup::make([
                    static::getEditAction(),
                    static::getReceiptAction(),
                    static::getChangePaymentAction(),
                    static::getCancelAction(),
                ])
                    ->label('សកម្មភាព')
                    ->button()
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(Size::Small)
                    ->hidden(fn (Invoice $record) => $record->status == Invoice::CANCEL),
            ], position: RecordActionsPosition::BeforeCells);
    }

    /**
     * @return array<int, string>
     */
    public static function statusOptions(): array
    {
        return [
            Invoice::DRAFT => __('global.invoice_status_request'),
            Invoice::UNPAID => __('global.invoice_status_unpaid'),
            Invoice::PARTIAL => __('global.invoice_status_partial'),
            Invoice::PAID => __('global.invoice_status_paid'),
            Invoice::CANCEL => __('global.invoice_status_cancel'),
        ];
    }

    protected static function getEditAction(): Action
    {
        return Action::make('edit')
            ->label(__('global.edit'))
            ->icon('heroicon-o-pencil-square')
            ->visible(fn (Invoice $record) => $record->status == Invoice::UNPAID && InvoiceResource::userCan('sale:sale:edit'))
            ->url(fn (Invoice $record) => InvoiceResource::getUrl('pos', ['sale_id' => $record->getRouteKey()]));
    }

    protected static function getReceiptAction(): Action
    {
        return Action::make('receipt')
            ->label(__('global.receipt'))
            ->icon('heroicon-o-printer')
            ->url(fn (Invoice $record) => route('sale.receipt', ['sale_id' => $record->getRouteKey()]))
            ->openUrlInNewTab();
    }

    protected static function getChangePaymentAction(): Action
    {
        return Action::make('changePayment')
            ->label(__('global.change_payment'))
            ->icon('heroicon-o-arrow-path')
            ->visible(fn (Invoice $record) => $record->status == Invoice::PAID && InvoiceResource::userCan('sale:menu:change_payment'))
            ->modalHeading(__('global.change_payment'))
            ->fillForm(fn (Invoice $record) => ['payment_gateway' => $record->payment_gateway])
            ->schema([
                Select::make('payment_gateway')
                    ->label(__('global.payment_gateway'))
                    ->options(fn () => PaymentGateway::pluck('name', 'id')->toArray())
                    ->required(),
            ])
            ->action(function (array $data, Invoice $record) {
                $record->update([
                    'payment_gateway' => $data['payment_gateway'],
                    'user_updated' => auth()->id(),
                ]);

                Notification::make()
                    ->success()
                    ->title(__('global.payment_changed'))
                    ->send();
            });
    }

    protected static function getCancelAction(): Action
    {
        return Action::make('cancelInvoice')
            ->label(__('global.cancel_invoice'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (Invoice $record) => $record->status == Invoice::PAID && InvoiceResource::userCan('sale:menu:cancel'))
            ->requiresConfirmation()
            ->modalHeading(__('global.cancel_invoice'))
            ->modalDescription(__('global.cancel_invoice_text'))
            ->action(function (Invoice $record) {
                $record->cancel();

                Notification::make()
                    ->success()
                    ->title(__('global.invoice_cancelled'))
                    ->send();
            });
    }

    protected static function getDataFilter(): Filter
    {
        return Filter::make('filter_data')
            ->columnSpan(3)
            ->schema([
                Grid::make(4)
                    ->schema([
                        ...static::getDateRangeFilterFields(),
                        Select::make('date_type')
                            ->label(__('global.date_type'))
                            ->options([
                                '0' => __('global.date_type_invoice'),
                                '1' => __('global.date_type_payment'),
                            ])
                            ->default('0')
                            ->selectablePlaceholder(false)
                            ->native(false),
                        Select::make('invoice_status')
                            ->label(__('global.invoice_status'))
                            ->options([
                                Invoice::UNPAID => __('global.invoice_status_unpaid'),
                                Invoice::PAID => __('global.invoice_status_paid'),
                                Invoice::CANCEL => __('global.invoice_status_cancel'),
                            ])
                            ->placeholder(__('global.invoice_status_all'))
                            ->native(false),
                        TextInput::make('invoice_code')
                            ->label(__('global.invoice_code')),
                    ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                $dateColumn = ($data['date_type'] ?? '0') === '1' ? 'invoices.payment_date' : 'invoices.created_at';

                static::applyDateRangeFilter($query, $data, $dateColumn);

                if (filled($data['invoice_status'] ?? null)) {
                    $query->where('invoices.status', $data['invoice_status']);
                }

                if (filled($data['invoice_code'] ?? null)) {
                    $query->where('invoices.id', (int) preg_replace('/\D/', '', $data['invoice_code']));
                }

                return $query;
            });
    }
}
