<?php

use App\Http\Controllers\SyncProvinController;
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

Route::get('/', [SyncProvinController::class, 'index'])
    ->name('sync.page');

Route::post('/provin/import', [SyncProvinController::class, 'startImport'])
    ->name('sync.import');
