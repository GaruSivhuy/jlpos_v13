<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Control\ExchangeMoney\ExchangeMoneyResource;
use App\Models\ExchangeMoney;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ExchangeMoneyReceiptController extends Controller
{
    public function __invoke(Request $request): View
    {
        $exchange = ExchangeMoney::query()
            ->branch()
            ->with('user_create')
            ->findOrFail($request->query('id'));

        return view('control.exchange-money-receipt', [
            'exchange' => $exchange,
            'company' => env('company_name', 'ជីងឡុង បោះដុំ'),
            'address' => env('company_address', 'ផ្ទះលេខ #Q1, ផ្លូវទី១ សង្កាត់ចោមចៅ ខណ្ឌ ពោធិ៍សែនជ័យ រាជធានីភ្នំពេញ'),
            'phone' => env('company_phone', '096 2 8888 45'),
            'closeUrl' => ExchangeMoneyResource::getUrl('index'),
        ]);
    }
}
