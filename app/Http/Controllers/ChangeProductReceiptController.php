<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Control\ChangeProducts\ChangeProductResource;
use App\Models\ChangeProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ChangeProductReceiptController extends Controller
{
    public function __invoke(Request $request): View
    {
        $results = ChangeProduct::query()
            ->branch()
            ->with(['inventories', 'metrics'])
            ->whereIn('id', Arr::wrap($request->query('ids')))
            ->orderBy('id')
            ->get();

        abort_if($results->isEmpty(), 404);

        return view('control.change-product-receipt', [
            'results' => $results,
            'showNumber' => $results->last()->id,
            'company' => env('company_name', 'ជីងឡុង បោះដុំ'),
            'address' => env('company_address', 'ផ្ទះលេខ #Q1, ផ្លូវទី១ សង្កាត់ចោមចៅ ខណ្ឌ ពោធិ៍សែនជ័យ រាជធានីភ្នំពេញ'),
            'phone' => env('company_phone', '096 2 8888 45'),
            'closeUrl' => ChangeProductResource::getUrl('index'),
        ]);
    }
}
