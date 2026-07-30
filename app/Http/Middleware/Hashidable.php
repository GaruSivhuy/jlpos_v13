<?php

namespace App\Http\Middleware;

use App\Models\Covid\WorkAccident;
use App\Models\Covid\WorkerObservation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Hashidable
{
    private $routeModelMapping = [        
        'filament.admin.resources.products.view' => \App\Models\Product::class,
        'filament.admin.resources.products.edit' => \App\Models\Product::class,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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
