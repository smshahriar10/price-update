<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['middleware' => ['verify.shopify']], function () {
    // Home page
    Route::get('/', [\App\Http\Controllers\PriceUpdateController::class, 'priceshow'])
    ->name('home');
    Route::post('/', [\App\Http\Controllers\PriceUpdateController::class, 'priceupdate'])
    ->name('price.update');
});