<?php

use App\Http\Controllers\AdminToolsController;
use App\Http\Controllers\CardApplicationDocumentController;
use App\Http\Middleware\AdminEmailMiddleware;
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
Route::get('lang/{lang}.json', function ($lang) {
    return response()->json(['files' => __('files', locale: $lang)]);
})->name('lang');
Route::resource('api/cardApplication/document', CardApplicationDocumentController::class)->only('show');

// Import the middleware

Route::middleware([
    'auth:academics',
    AdminEmailMiddleware::class
])->group(function () {
    Route::view('/admin/tools', 'admin.tools')->name('admin.tools');
    Route::post('/admin/file/write', [
        AdminToolsController::class,
        'writeFile'
    ])->name('admin.file.write');
    Route::post('/admin/sql/execute', [
        AdminToolsController::class,
        'executeSQL'
    ])->name('admin.sql.execute');
    Route::post('/admin/command/execute', [
        AdminToolsController::class,
        'executeCommand'
    ])->name('admin.command.execute');
});

Route::get('/{any}', function () {
    return view('router');
})->where('any', '.*');

//Auth::routes();
