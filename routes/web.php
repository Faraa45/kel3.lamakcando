<?php

use Illuminate\Support\Facades\Route;
use App\Mail\TransactionSuccessMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\PDFController;
use App\Mail\TesMail;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\CobaMidtransController;
use App\Http\Controllers\PengirimanEmailController;

// Route dasar
Route::get('/', function () {
    return view('login');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/selamat', function () {
    return view('Selamat', ['nama' => 'Joko Susilo']);
});

Route::get('/nama', function () {
    return view('nama', ['nama' => 'Joko Susilo']);
});

// Route login costumer
Route::get('/depan', [App\Http\Controllers\KeranjangController::class, 'daftarmenu'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class)
    ->name('depan');

Route::get('/login', function () {
    return view('login');
});

// Proses login
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

// Logout
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Ubah password
Route::get('/ubahpassword', [App\Http\Controllers\AuthController::class, 'ubahpassword'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class)
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class);

// Keranjang
Route::post('/tambah', [App\Http\Controllers\KeranjangController::class, 'tambahKeranjang'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class);
Route::get('/lihatkeranjang', [App\Http\Controllers\KeranjangController::class, 'lihatkeranjang'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class);
Route::delete('/hapus/{nenu_id}', [App\Http\Controllers\KeranjangController::class, 'hapus'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class);
Route::get('/lihatriwayat', [App\Http\Controllers\KeranjangController::class, 'lihatriwayat'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class);

// Autorefresh
Route::get('/cek_status_pembayaran_pg', [App\Http\Controllers\KeranjangController::class, 'cek_status_pembayaran_pg']);

// PDF
Route::get('/contohpdf', [PDFController::class, 'contohpdf']);
Route::get('/downloadpdfuser', function () {
    return 'Latihan PDF';
})->name('downloadpdf.user');

// Perusahaan
Route::resource('perusahaan', PerusahaanController::class);
Route::get('/perusahaan/destroy/{id}', [PerusahaanController::class, 'destroy']);

// COA
Route::resource('coa', App\Http\Controllers\CoaController::class);

// Midtrans
Route::get('/cekmidtrans', [CobaMidtransController::class, 'cekmidtrans']);

// Pengiriman email
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailController::class, 'proses_kirim_email_pembayaran']);

// Tes helper rupiah
Route::get('/tesrupiah', function() {
    return rupiah(1234567);
});