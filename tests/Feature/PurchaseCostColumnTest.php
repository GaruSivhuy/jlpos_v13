<?php

use App\Filament\Resources\Purchases\Pages\ListPurchases;
use App\Models\Purchase;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    // The legacy tables exist in the real database but not in the repo migrations.
    if (! Schema::hasTable('purchase')) {
        Schema::create('purchase', function ($table) {
            $table->increments('id');
            $table->string('po_code')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->date('po_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->integer('status')->default(0);
            $table->text('remark')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('user_updated')->nullable();
            $table->integer('branch_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    if (! Schema::hasTable('purchase_detail')) {
        Schema::create('purchase_detail', function ($table) {
            $table->increments('id');
            $table->integer('purchase_id');
            $table->integer('inventory_id')->nullable();
            $table->integer('metric_id')->nullable();
            $table->integer('uom_id')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->date('produce_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->timestamps();
        });
    }

    Filament::setCurrentPanel('admin');
    $this->actingAs(User::factory()->create());
});

function createPurchaseWithLines(string $poCode, array $lines): Purchase
{
    $purchase = Purchase::create(['po_code' => $poCode, 'cost' => 0]);

    foreach ($lines as [$price, $quantity]) {
        DB::table('purchase_detail')->insert([
            'purchase_id' => $purchase->id,
            'price' => $price,
            'quantity' => $quantity,
        ]);
    }

    return $purchase;
}

it('shows the cost as the sum of price times quantity of the purchase items', function () {
    $purchase = createPurchaseWithLines('PO-1', [[2.5, 4], [10, 3]]);

    Livewire::test(ListPurchases::class)
        ->assertCanSeeTableRecords([$purchase])
        ->assertTableColumnFormattedStateSet('cost', '40.00', $purchase);
});

it('shows a zero cost for a purchase without items', function () {
    $purchase = createPurchaseWithLines('PO-EMPTY', []);

    Livewire::test(ListPurchases::class)
        ->assertTableColumnFormattedStateSet('cost', '0.00', $purchase);
});
