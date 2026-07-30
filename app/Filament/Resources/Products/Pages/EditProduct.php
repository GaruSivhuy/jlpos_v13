<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\Products\ProductResource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EditProduct extends BaseEditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ViewAction::make(),
            // DeleteAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __("global.edit")." ".__("global.product");
    }

    protected $listeners = ['getCheckDuplicate' => 'getCheckDuplicate', 'getCheckDuplicateKH', 'getCheckDuplicateKH'];

    public function getCheckDuplicate(): void
    {
        try {
            $this->resetErrorBag('data.name');
            $name = $this->data['name'];
            $branch_id = $this->data['branch_id'];
            $main_cat_id = $this->data['main_cat_id'];
            $category_id = $this->data['category_id'];
            $id          = $this->data['id'] ?? null;
            if(blank($name)){
                $this->addError(
                    'data.name',
                    trans('validation.required', ['attribute' => __('global.pname')])
                );
                return;
            }
            if(blank($branch_id)){
                $this->addError(
                    'data.branch_id',
                    trans('validation.required', ['attribute' => __('global.branch')])
                );
                data_set($this->data, 'name', null);
                return;
            }
            if(blank($main_cat_id)){
                $this->addError(
                    'data.main_cat_id',
                    trans('validation.required', ['attribute' => __('global.main_category')])
                );
                data_set($this->data, 'name', null);
                return;
            }
            if(blank($category_id)){
                $this->addError(
                    'data.category_id',
                    trans('validation.required', ['attribute' => __('global.cname_kh')])
                );
                data_set($this->data, 'name', null);
                return;
            } 
            
            $validator = Validator::make(
                ['name' => $this->data['name']],
                [
                    'name' => [
                        'required',
                        Rule::unique('inventories', 'name')
                            ->where('branch_id', $branch_id)
                            ->ignore($id),
                    ],
                ],
                [
                    'name.required' => __('validation.required', ['attribute' => __('global.pname')]),
                    'name.unique'   => __('validation.unique', ['attribute' => __('global.pname')]),
                ]
            );

            $this->resetErrorBag('data.name');

            if ($validator->fails()) {
                data_set($this->data, 'name', null);
                $this->addError('data.name', $validator->errors()->first('name'));
            }

        } catch (\Throwable $e) {
            $this->addError('data.name', $e->getMessage());
            data_set($this->data, 'name', null);
        }
    }

    public function getCheckDuplicateKH(): void
    {
        try {
            $this->resetErrorBag('data.name_kh');
            $name_kh = $this->data['name_kh'];
            $branch_id = $this->data['branch_id'];
            $main_cat_id = $this->data['main_cat_id'];
            $category_id = $this->data['category_id'];
            $id          = $this->data['id'] ?? null;

            if(blank($name_kh)){
                $this->addError(
                    'data.name_kh',
                    trans('validation.required', ['attribute' => __('global.pname_kh')])
                );
                return;
            }
            if(blank($branch_id)){
                $this->addError(
                    'data.branch_id',
                    trans('validation.required', ['attribute' => __('global.branch')])
                );
                data_set($this->data, 'name_kh', null);
                return;
            }
            if(blank($main_cat_id)){
                $this->addError(
                    'data.main_cat_id',
                    trans('validation.required', ['attribute' => __('global.main_category')])
                );
                data_set($this->data, 'name_kh', null);
                return;
            }
            if(blank($category_id)){
                $this->addError(
                    'data.category_id',
                    trans('validation.required', ['attribute' => __('global.cname_kh')])
                );
                data_set($this->data, 'name_kh', null);
                return;
            } 
            
            $validator = Validator::make(
                ['name_kh' => $this->data['name_kh']],
                [
                    'name_kh' => [
                        'required',
                        Rule::unique('inventories', 'name_kh')
                            ->where('branch_id', $branch_id)
                            ->ignore($id),
                    ],
                ],
                [
                    'name_kh.required' => __('validation.required', ['attribute' => __('global.pname_kh')]),
                    'name_kh.unique'   => __('validation.unique', ['attribute' => __('global.pname_kh')]),
                ]
            );

            $this->resetErrorBag('data.name_kh');

            if ($validator->fails()) {
                data_set($this->data, 'name_kh', null);
                $this->addError('data.name_kh', $validator->errors()->first('name_kh'));
            }

        } catch (\Throwable $e) {
            $this->addError('data.name_kh', $e->getMessage());
            data_set($this->data, 'name_kh', null);
        }
    }
}
