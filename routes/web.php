<?php

use App\Http\Controllers\CardHistoryController;
use App\Http\Controllers\CouponOwnerController;
use App\Http\Controllers\DailyMealPlanController;
use App\Http\Controllers\EntryCheckingController;
use App\Http\Controllers\PurchaseCouponController;
use App\Http\Controllers\TransferCouponController;
use App\Http\Controllers\UserInfoController;
use Illuminate\Support\Facades\Auth;
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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('coupons/history', CouponOwnerController::class)->name('coupons.history');// invoke
Route::get('myInfo', UserInfoController::class)->name('myInfo');
Route::resource('mealPlan', DailyMealPlanController::class)->parameter('mealPlan', 'dailyMealPlan');
Route::redirect('coupons/purchase', '/coupons/purchase/create');
Route::resource('coupons/purchase', PurchaseCouponController::class)->only('create', 'store');
Route::redirect('entryChecking', '/entryChecking/create');
Route::resource('entryChecking', EntryCheckingController::class)->only('create', 'store');
Route::redirect('coupons/transfer', '/coupons/transfer/create');
Route::resource('coupons/transfer', TransferCouponController::class)->parameter('transfer', 'transferCoupon')->only('create', 'store', 'show');
Route::get('card/history', CardHistoryController::class);// invoke
