<?php

use App\Enum\CardStatusEnum;
use App\Http\Controllers\AuthSanctum\LoginController;
use App\Http\Controllers\CardApplicantController;
use App\Http\Controllers\CardApplicationCheckingController;
use App\Http\Controllers\CardApplicationController;
use App\Http\Controllers\CardApplicationDocumentController;
use App\Http\Controllers\CardHistoryController;
use App\Http\Controllers\CouponTransactionController;
use App\Http\Controllers\EntryCheckingController;
use App\Http\Controllers\ExportStatisticsController;
use App\Http\Controllers\PurchaseCouponController;
use App\Http\Controllers\TransactionCouponConformationDetailsController;
use App\Http\Controllers\TransferCouponController;
use App\Http\Controllers\UserInfoController;
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
Route::get('/user', [UserInfoController::class, 'index'])->name('user');
Route::post('/user/create', [UserInfoController::class, 'store'])->name('user.store');

Route::resource('coupons/purchase', PurchaseCouponController::class, ['as' => 'coupons'])->only('store');
Route::resource('entryChecking', EntryCheckingController::class)->only('store');
Route::resource('cardApplication', CardApplicationController::class)->only('update');
Route::apiResource('cardApplication.document', CardApplicationDocumentController::class)->shallow()->except(['show'])
    ->names(['index' => 'document.index', 'store' => 'document.store',]);

Route::resource('/cardApplication/{category}/checking', CardApplicationCheckingController::class, ['as' => 'cardApplication'])
    ->whereIn('category', CardStatusEnum::values()->toArray())->only('store');
Route::get('cardApplication/checking/search', [CardApplicationCheckingController::class, 'search'])->name('cardApplication.checking.search');
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/transaction/confirm', [TransactionCouponConformationDetailsController::class, '__invoke'])->name('transaction.confirm');
Route::resource('cardApplicant', CardApplicantController::class)->only(["index", "store"]);
Route::get('coupons/history', CouponTransactionController::class)->name('coupons.history');// invoke
Route::get('card/history', CardHistoryController::class)->name('card.history');// invoke
Route::resource('entryChecking', EntryCheckingController::class)->only('create');
Route::resource('coupons/transfer', TransferCouponController::class, ['as' => 'coupons'])->only('store');
Route::resource('cardApplication', CardApplicationController::class)->except(['create', 'update', 'destroy']);
Route::get('card/history', CardHistoryController::class)->name('card.history');// invoke


//
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::redirect('coupons/purchase', '/coupons/purchase/create');
//Route::resource('coupons/purchase', PurchaseCouponController::class, ['as' => 'coupons'])->only('create');
//Route::redirect('entryChecking', '/entryChecking/create');
//Route::redirect('coupons/transfer', '/coupons/transfer/create');


Route::resource('/cardApplication/{category}/checking', CardApplicationCheckingController::class, ['as' => 'cardApplication'])
    ->whereIn('category', CardStatusEnum::values()->toArray())->only('index');

Route::post('statistics', ExportStatisticsController::class)->name('statistics');// invoke

Route::post('/isLogin', function () {
    return response(status: 204);
})->name('isLogin')->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');