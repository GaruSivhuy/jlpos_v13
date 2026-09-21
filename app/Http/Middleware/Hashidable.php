<?php

namespace App\Http\Middleware;

use App\Models\Product;
use App\Models\Purchase;
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
