<?php

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
Route::get('coupons/history',\App\Http\Controllers\CouponOwnerController::class)->name('coupons.history');// invoke
Route::get('myInfo', \App\Http\Controllers\UserInfoController::class)->name('myInfo');
Route::resource('mealPlan', \App\Http\Controllers\DailyMealPlanController::class)->parameter('mealPlan','dailyMealPlan')->only('index','show');
Route::redirect('coupons/purchase','/coupons/purchase/create');
Route::resource('coupons/purchase', \App\Http\Controllers\PurchaseCouponController::class,['as'=>'coupons'])->only('create','store');
Route::redirect('entryChecking','/entryChecking/create');
Route::resource('entryChecking', \App\Http\Controllers\EntryCheckingController::class)->only('create','store');
Route::redirect('coupons/transfer','/coupons/transfer/create');
Route::resource('coupons/transfer', \App\Http\Controllers\TransferCouponController::class,['as'=>'coupons'])->parameter('transfer','transferCoupon')->only('create','store','show');
