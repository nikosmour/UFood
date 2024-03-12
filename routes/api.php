<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('coupons/purchase', \App\Http\Controllers\PurchaseCouponController::class, ['as' => 'coupons'])->only('store');
Route::resource('entryChecking', \App\Http\Controllers\EntryCheckingController::class)->only('store');
Route::resource('cardApplication', \App\Http\Controllers\CardApplicationController::class)->only('update');
Route::resource('cardApplication/{cardApplication}/document', \App\Http\Controllers\CardApplicationDocumentController::class)->except('create', 'edit');
Route::resource('/cardApplication/{category}/checking', \App\Http\Controllers\CardApplicationCheckingController::class, ['as' => 'cardApplication'])
    ->whereIn('category', \App\Enum\CardStatusEnum::values()->toArray())->only('store');
