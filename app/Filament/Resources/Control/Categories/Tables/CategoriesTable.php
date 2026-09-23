<?php

namespace App\Filament\Resources\Control\Categories\Tables;

use App\Models\Category;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('global.id'))
                    ->sortable()
                    ->state(fn ($record) => 'CAT-'.str_pad($record->id, env('ID_PAD_LENGTH', 4), '0', STR_PAD_LEFT)),
                TextColumn::make('main_category.cat_name_kh')->label(__('global.cat_name')),
                TextColumn::make('name')->label(__('global.cname'))->searchable()->sortable(),
                TextColumn::make('name_kh')->label(__('global.cname_kh'))->searchable()->sortable(),
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
                    ->before(function (DeleteAction $action, Category $record): void {
                        if (Product::withTrashed()->where('category_id', $record->id)->exists()) {
                            Notification::make()
                                ->danger()
                                ->title(__('global.error'))
                                ->body(__('global.category_in_use'))
                                ->send();

                            $action->halt();

                            return;
                        }

                        $record->update(['user_updated' => auth()->id()]);
                    }),
            ], position: RecordActionsPosition::BeforeCells);
    }
}
