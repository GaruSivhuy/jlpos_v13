<?php

use App\Http\Controllers\ChangeProductReceiptController;
use App\Http\Controllers\ExchangeMoneyReceiptController;
use App\Http\Controllers\InvoiceReceiptController;
use App\Http\Controllers\ReportsPrintController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');

Route::get('/', function () {
    return redirect('admin');
});

Route::get('sale/receipt', InvoiceReceiptController::class)
    ->middleware(Authenticate::class)
    ->name('sale.receipt');

Route::get('exchange-money/receipt', ExchangeMoneyReceiptController::class)
    ->middleware(Authenticate::class)
    ->name('exchange-money.receipt');

Route::get('change-product/receipt', ChangeProductReceiptController::class)
    ->middleware(Authenticate::class)
    ->name('change-product.receipt');

Route::get('reports/print', ReportsPrintController::class)
    ->middleware(Authenticate::class)
    ->name('reports.print');
