<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\Category;
use App\Models\ChangeProduct;
use App\Models\Customer;
use App\Models\ExchangeMoney;
use App\Models\InventoryAdjustment;
use App\Models\Location;
use App\Models\MainCategory;
use App\Models\Metric;
use App\Models\Metricsables;
use App\Models\OverMoney;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Remark;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Hashidable
{
    private $routeModelMapping = [
        'filament.admin.resources.products.view' => Product::class,
        'filament.admin.resources.products.edit' => Product::class,

        'filament.admin.resources.purchase.view' => Purchase::class,
        'filament.admin.resources.purchase.edit' => Purchase::class,

        'filament.admin.resources.branches.view' => Branch::class,
        'filament.admin.resources.branches.edit' => Branch::class,

        'filament.admin.resources.categories.view' => Category::class,
        'filament.admin.resources.categories.edit' => Category::class,

        'filament.admin.resources.change-products.view' => ChangeProduct::class,
        'filament.admin.resources.change-products.edit' => ChangeProduct::class,

        'filament.admin.resources.customers.view' => Customer::class,
        'filament.admin.resources.customers.edit' => Customer::class,

        'filament.admin.resources.exchange-money.view' => ExchangeMoney::class,
        'filament.admin.resources.exchange-money.edit' => ExchangeMoney::class,

        'filament.admin.resources.inventory-adjustments.view' => InventoryAdjustment::class,
        'filament.admin.resources.inventory-adjustments.edit' => InventoryAdjustment::class,

        'filament.admin.resources.inventory-transfers.view' => StockTransfer::class,
        'filament.admin.resources.inventory-transfers.edit' => StockTransfer::class,

        'filament.admin.resources.locations.view' => Location::class,
        'filament.admin.resources.locations.edit' => Location::class,

        'filament.admin.resources.main-categories.view' => MainCategory::class,
        'filament.admin.resources.main-categories.edit' => MainCategory::class,

        'filament.admin.resources.metrics.view' => Metric::class,
        'filament.admin.resources.metrics.edit' => Metric::class,

        'filament.admin.resources.over-money.view' => OverMoney::class,
        'filament.admin.resources.over-money.edit' => OverMoney::class,

        'filament.admin.resources.price-list.view' => Metricsables::class,
        'filament.admin.resources.price-list.edit' => Metricsables::class,

        'filament.admin.resources.remarks.view' => Remark::class,
        'filament.admin.resources.remarks.edit' => Remark::class,

        'filament.admin.resources.suppliers.view' => Supplier::class,
        'filament.admin.resources.suppliers.edit' => Supplier::class,

        'filament.admin.resources.users.view' => User::class,
        'filament.admin.resources.users.edit' => User::class,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (\Illuminate\Http\Response|RedirectResponse)  $next
     * @return \Illuminate\Http\Response|RedirectResponse
     */
    private function getModelId($model, $routeKey)
    {
        return \Hashids::connection($model)->decode($routeKey)[0] ?? $routeKey;
    }

    public function handle($request, Closure $next): Response
    {
        if (request()->route('parent')) {
            if (in_array($request->route()->getName(), array_keys($this->routeModelMapping))) {
                $request->route()->setParameter('parent', $this->getModelId($this->routeModelMapping[$request->route()->getName()], $request->route('parent')));
            }

            // dd(request()->route('company'));
            return $next($request);
        }

        if (in_array($request->route()->getName(), array_keys($this->routeModelMapping))) {
            $request->route()->setParameter('record', $this->getModelId($this->routeModelMapping[$request->route()->getName()], $request->route('record')));
        }

        return $next($request);
    }
}
