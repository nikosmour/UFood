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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs')->get('/user', function (Request $request) {
    $cardApplicant = $request->user()->cardApplicant;
    if ($cardApplicant) $cardApplicant->address;

    return $request->user();
});
Route::resource('coupons/purchase', \App\Http\Controllers\PurchaseCouponController::class, ['as' => 'coupons'])->only('store');
Route::resource('entryChecking', \App\Http\Controllers\EntryCheckingController::class)->only('store');
Route::resource('cardApplication', \App\Http\Controllers\CardApplicationController::class)->only('update');
Route::apiResource('cardApplication.document', \App\Http\Controllers\CardApplicationDocumentController::class)->shallow()
    ->names(['index' => 'document.index', 'store' => 'document.store',]);

Route::resource('/cardApplication/{category}/checking', \App\Http\Controllers\CardApplicationCheckingController::class, ['as' => 'cardApplication'])
    ->whereIn('category', \App\Enum\CardStatusEnum::values()->toArray())->only('store');
Route::post('cardApplication/checking/search', [\App\Http\Controllers\CardApplicationCheckingController::class, 'search'])->name('cardApplication.checking.search');
Route::post('login', [\App\Http\Controllers\AuthSanctum\LoginController::class, 'login']);//->name('login');
