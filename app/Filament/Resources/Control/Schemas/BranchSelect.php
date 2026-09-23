<?php

namespace App\Filament\Resources\Control\Schemas;

use App\Models\Branch;
use Filament\Forms\Components\Select;

class BranchSelect
{
    public static function make(string $name = 'branch_id'): Select
    {
        return Select::make($name)
            ->label(__('global.branch'))
            ->searchable()
            ->required()
            ->live(onBlur: true)
            ->options(fn (): array => static::options())
            ->validationMessages([
                'required' => __('validation.required', ['attribute' => __('global.branch')]),
            ]);
    }

    /**
     * Admins can pick any branch, everybody else only their own.
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        $user = auth()->user();

        $branches = $user->is_admin ? Branch::query()->get() : $user->branch()->get();

        return $branches
            ->mapWithKeys(fn (Branch $branch): array => [
                $branch->id => collect([$branch->name_en, $branch->name_kh])->filter()->implode(' - '),
            ])
            ->all();
    }
}
