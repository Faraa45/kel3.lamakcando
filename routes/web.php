<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\CobaMidtransController;
use App\Http\Controllers\PengirimanEmailController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\AbsensiController;

// Halaman utama diarahkan ke login
Route::get('/', function () {
    return view('login');
});

// Halaman login
Route::get('/login', function () {
    return view('login');
});

// Proses login
Route::post('/login', [AuthController::class, 'login']);

// Proses logout
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


// Halaman welcome (opsional)
Route::get('/welcome', function () {
    return view('welcome');
});

// PDF
Route::get('/contohpdf', [PDFController::class, 'contohpdf']);

// Midtrans
Route::get('/cekmidtrans', [CobaMidtransController::class, 'cekmidtrans']);

// Pengiriman Email
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailController::class, 'proses_kirim_email_pembayaran']);

// COA
Route::resource('coa', CoaController::class);

// Absensi
Route::resource('absensi', AbsensiController::class);

// Middleware khusus costumer
Route::middleware(\App\Http\Middleware\CostumerMiddleware::class)->group(function () {

    // Halaman costumer
    Route::get('/depan', [KeranjangController::class, 'costumer'])->name('depan');

    // Ubah password
    Route::get('/ubahpassword', [AuthController::class, 'ubahpassword'])->name('ubahpassword');
    Route::post('/prosesubahpassword', [AuthController::class, 'prosesubahpassword']);

    // Keranjang
    Route::post('/tambah', [KeranjangController::class, 'tambahKeranjang']);
    Route::get('/lihatkeranjang', [KeranjangController::class, 'lihatkeranjang']);
    Route::delete('/hapus/{nenu_id}', [KeranjangController::class, 'hapus']);
    Route::get('/lihatriwayat', [KeranjangController::class, 'lihatriwayat']);
});

// Autorefresh status pembayaran
Route::get('/cek_status_pembayaran_pg', [KeranjangController::class, 'cek_status_pembayaran_pg']);

