<?php

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

Route::get('/', function () {
    return view('welcome');
});
// login costumer
Route::get('/depan', [App\Http\Controllers\KeranjangController::class,'costumer'])
     ->middleware(\App\Http\Middleware\CostumerMiddleware::class)
     ->name('depan');

Route::get('/login', function () {
    return view('login');
});
// tambahan route untuk proses login
use Illuminate\Http\Request;
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// untuk ubah password
Route::get('/ubahpassword', [App\Http\Controllers\AuthController::class, 'ubahpassword'])
    ->middleware('costumer')
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware('costumer')
;
// prosesubahpassword

//absensi
Route::resource('absensi', AbsensiController::class);

Route::resource('penggajian', PenggajianController::class);

Route::get('/laporan-penggajian', [\App\Http\Controllers\PDFController::class, 'penggajianPdf']);

