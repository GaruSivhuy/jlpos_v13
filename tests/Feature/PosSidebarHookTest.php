<?php

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    Filament::getCurrentPanel()->boot();
});

it('tells the sidebar hook whether the current page is the pos', function (string $uri, string $expected) {
    $this->app->instance('originalRequest', tap(Request::create($uri), function (Request $request) {
        $request->setRouteResolver(fn () => Route::getRoutes()->match($request));
    }));

    $html = FilamentView::renderHook(PanelsRenderHook::BODY_START)->toHtml();

    expect($html)->toContain("isPos: {$expected}");
})->with([
    'pos' => ['/admin/sale/pos', 'true'],
    'invoice list' => ['/admin/sale', 'false'],
]);
