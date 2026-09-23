<?php

namespace App\Filament\Resources\Control\Metrics\Tables;

use App\Models\Metric;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class MetricsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.id'))
                    ->sortable()
                    ->state(fn ($record) => 'MID-'.str_pad($record->id, env('ID_PAD_LENGTH', 4), '0', STR_PAD_LEFT)),
                TextColumn::make('name')->label(__('global.mname'))->searchable()->sortable(),
                TextColumn::make('name_kh')->label(__('global.mname_kh'))->searchable()->sortable(),
                TextColumn::make('qty')->label(__('global.mqty'))->sortable(),
                TextColumn::make('name_show')->label(__('global.name_show')),
                TextColumn::make('branch.name_kh')->label(__('global.branch')),
                TextColumn::make('user_create.name')->label(__('global.created_by')),
                TextColumn::make('user_update.name')->label(__('global.updated_by')),
                TextColumn::make('created_at')->label(__('global.created_at'))->dateTime('d-m-Y H:i:s')->sortable(),
                TextColumn::make('updated_at')->label(__('global.updated_at'))->dateTime('d-m-Y H:i:s')->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->tooltip(__('global.edit'))
                    ->hiddenLabel()
                    ->icon(Heroicon::PencilSquare),
                DeleteAction::make()
                    ->tooltip(__('global.delete'))
                    ->hiddenLabel()
                    ->modalHeading(__('global.delete_title'))
                    ->modalDescription(__('global.delete_text'))
                    ->modalSubmitActionLabel(__('global.delete'))
                    ->modalCancelActionLabel(__('global.cancel'))
                    ->before(function (DeleteAction $action, Metric $record): void {
                        if (DB::table('metricsables')->where('metric_id', $record->id)->exists()) {
                            Notification::make()
                                ->danger()
                                ->title(__('global.error'))
                                ->body(__('global.metric_in_use'))
                                ->send();

                            $action->halt();

                            return;
                        }

                        $record->update(['user_updated' => auth()->id()]);
                    }),
            ], position: RecordActionsPosition::BeforeCells);
    }
}
