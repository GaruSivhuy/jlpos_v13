<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');


Route::get('/', function () {
    return redirect('admin');
});
