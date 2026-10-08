<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Control\ServiceFees\Schemas\ServiceFeeForm;
use App\Filament\Resources\Control\ServiceFees\ServiceFeeResource;
use App\Models\ServiceFee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ServiceFeeReceiptController extends Controller
{
    public function __invoke(Request $request): View
    {
        $serviceFee = ServiceFee::query()
            ->with(['user_create', 'paymentGateway'])
            ->findOrFail($request->query('id'));

        return view('control.service-fee-receipt', [
            'serviceFee' => $serviceFee,
            'serviceType' => ServiceFeeForm::typeOptions()[$serviceFee->service_type] ?? null,
            'company' => env('company_name', 'ជីងឡុង បោះដុំ'),
            'address' => env('company_address', 'ផ្ទះលេខ #Q1, ផ្លូវទី១ សង្កាត់ចោមចៅ ខណ្ឌ ពោធិ៍សែនជ័យ រាជធានីភ្នំពេញ'),
            'phone' => env('company_phone', '096 2 8888 45'),
            'closeUrl' => ServiceFeeResource::getUrl('index'),
        ]);
    }
}
