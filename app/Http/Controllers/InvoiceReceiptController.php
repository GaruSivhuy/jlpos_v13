<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class InvoiceReceiptController extends Controller
{
    public function __invoke(Request $request): View
    {
        $invoiceId = Hashids::connection(Invoice::class)->decode((string) $request->query('sale_id'))[0] ?? null;

        abort_if($invoiceId === null, 404);

        $invoice = Invoice::query()
            ->branch()
            ->with(['invoiceItems.product.remarks', 'invoiceItems.metric', 'customer', 'createdBy', 'paymentGateway', 'exchange'])
            ->findOrFail($invoiceId);

        return view('sale.receipt', [
            'invoice' => $invoice,
            'company' => env('company_name', 'ជីងឡុង បោះដុំ'),
            'address' => env('company_address', 'ផ្ទះលេខ #Q1, ផ្លូវទី១ សង្កាត់ចោមចៅ ខណ្ឌ ពោធិ៍សែនជ័យ រាជធានីភ្នំពេញ'),
        ]);
    }
}
