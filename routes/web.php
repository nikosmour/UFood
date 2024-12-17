<?php

use App\Http\Controllers\CardApplicationDocumentController;
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
Route::resource('api/cardApplication/document', CardApplicationDocumentController::class)->only('show');

Route::get('/{any}', function () {
    return view('router');
})->where('any', '.*');

//Auth::routes();
