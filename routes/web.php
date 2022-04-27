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
Route::get('mealPlan', \App\Http\Controllers\DailyMealPlanController::class);
Route::get('myInfo', \App\Http\Controllers\UserInfoController::class)->name('myInfo');
Route::redirect('coupons/purchase','coupons/purchase/create');
Route::resource('coupons/purchase', \App\Http\Controllers\PurchaseCouponController::class,['as'=>'coupons'])->only('create','store');
Route::redirect('entryChecking','entryChecking/create');
Route::resource('entryChecking', \App\Http\Controllers\EntryCheckingController::class)->only('create','store');
