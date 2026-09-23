<?php

use App\Models\Branch;
use App\Models\Category;
use App\Models\ChangeProduct;
use App\Models\Customer;
use App\Models\ExchangeMoney;
use App\Models\InventoryAdjustment;
use App\Models\Invoice;
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

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'salt' => '',
            'length' => 0,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        'alternative' => [
            'salt' => 'your-salt-string',
            'length' => 'your-length-integer',
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Product::class => [
            'salt' => Product::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Purchase::class => [
            'salt' => Purchase::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Invoice::class => [
            'salt' => Invoice::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Branch::class => [
            'salt' => Branch::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Category::class => [
            'salt' => Category::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        ChangeProduct::class => [
            'salt' => ChangeProduct::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Customer::class => [
            'salt' => Customer::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        ExchangeMoney::class => [
            'salt' => ExchangeMoney::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        InventoryAdjustment::class => [
            'salt' => InventoryAdjustment::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Location::class => [
            'salt' => Location::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        MainCategory::class => [
            'salt' => MainCategory::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Metric::class => [
            'salt' => Metric::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Metricsables::class => [
            'salt' => Metricsables::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        OverMoney::class => [
            'salt' => OverMoney::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Remark::class => [
            'salt' => Remark::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        StockTransfer::class => [
            'salt' => StockTransfer::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        Supplier::class => [
            'salt' => Supplier::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

        User::class => [
            'salt' => User::class.env('APP_KEY'),
            'length' => 12,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],

    ],

];
