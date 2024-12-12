<?php

use App\Enum\CardStatusEnum;
use App\Http\Controllers\CardApplicationCheckingController;
use App\Http\Controllers\CardApplicationController;
use App\Http\Controllers\CardHistoryController;
use App\Http\Controllers\DailyMealPlanController;
use App\Http\Controllers\EntryCheckingController;
use App\Http\Controllers\ExportStatisticsController;
use App\Http\Controllers\PurchaseCouponController;
use App\Http\Controllers\TransferCouponController;
use App\Http\Controllers\UserInfoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('router');
});

//Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('card/history', CardHistoryController::class)->name('card.history');// invoke
Route::get('myInfo', UserInfoController::class)->name('myInfo');
Route::resource('mealPlan', DailyMealPlanController::class)->parameter('mealPlan', 'dailyMealPlan');
Route::redirect('coupons/purchase', '/coupons/purchase/create');
Route::resource('coupons/purchase', PurchaseCouponController::class, ['as' => 'coupons'])->only('create');
Route::redirect('entryChecking', '/entryChecking/create');
Route::resource('entryChecking', EntryCheckingController::class)->only('create');
Route::redirect('coupons/transfer', '/coupons/transfer/create');
Route::resource('coupons/transfer', TransferCouponController::class, ['as' => 'coupons'])->parameter(
    'transfer', 'transferCoupon')->only('create', 'store', 'show');
Route::resource('cardApplication', CardApplicationController::class)->except(['create', 'update', 'destroy']);
//Route::resource('cardApplication/{cardApplication}/document', \App\Http\Controllers\CardApplicationDocumentController::class)->only('show');

Route::resource('/cardApplication/{category}/checking', CardApplicationCheckingController::class, ['as' => 'cardApplication'])
    ->whereIn('category', CardStatusEnum::values()->toArray())->only('index');

Route::post('statistics', ExportStatisticsController::class)->name('statistics');// invoke
