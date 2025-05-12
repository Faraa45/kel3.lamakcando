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


use App\Mail\TransactionSuccessMail;

use Illuminate\Support\Facades\Mail;

// Route dasar
Route::get('/', function () {
    return view('login');
});


Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/selamat', function () {

    return view('Selamat',['nama' => 'Joko Susilo']);
});

Route::get('/nama', function () {
    return view('nama',['nama' => 'Joko Susilo']);
});

Route::get('/tes', [App\Http\Controllers\ContohController::class, 'tes']);

Route::resource('coa',App\Http\Controllers\CoaController::class);

// // untuk contoh perusahaan
// use App\Http\Controllers\PerusahaanController;
// Route::resource('perusahaan', PerusahaanController::class);
// Route::get('/perusahaan/destroy/{id}', [PerusahaanController::class,'destroy']);

Route::get('/', function () {
    // return view('welcome');
    // diarahkan ke login costumer
    return view('login');
});


// login costumer
Route::get('/depan', [App\Http\Controllers\KeranjangController::class, 'daftarmenu'])
     ->middleware(\App\Http\Middleware\CostumerMiddleware::class)
     ->name('depan');

    return view('Selamat', ['nama' => 'Joko Susilo']);
});

Route::get('/nama', function () {
    return view('nama', ['nama' => 'Joko Susilo']);
});

// Route login costumer
Route::get('/depan', [App\Http\Controllers\KeranjangController::class, 'daftarmenu'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class)
    ->name('depan');

// login costumer
Route::get('/depan', [App\Http\Controllers\KeranjangController::class,'costumer'])
     ->middleware(\App\Http\Middleware\CostumerMiddleware::class)
     ->name('depan');


Route::get('/login', function () {
    return view('login');
});


// Proses login
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

// Logout

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
    ->middleware(\App\Http\Middleware\costumerMiddleware::class)
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware(\App\Http\Middleware\costumerMiddleware::class)
;
// prosesubahpassword
// tambah keranjang
Route::post('/tambah', [App\Http\Controllers\KeranjangController::class, 'tambahKeranjang'])->middleware(\App\Http\Middleware\costumerMiddleware::class);
Route::get('/lihatkeranjang', [App\Http\Controllers\KeranjangController::class, 'lihatkeranjang'])->middleware(\App\Http\Middleware\costumerMiddleware::class);
Route::delete('/hapus/{nenu_id}', [App\Http\Controllers\KeranjangController::class, 'hapus'])->middleware(\App\Http\Middleware\costumerMiddleware::class);
Route::get('/lihatriwayat', [App\Http\Controllers\KeranjangController::class, 'lihatriwayat'])->middleware(\App\Http\Middleware\costumerMiddleware::class);
// untuk autorefresh
Route::get('/cek_status_pembayaran_pg', [App\Http\Controllers\KeranjangController::class, 'cek_status_pembayaran_pg']);
Route::get('/login', function () {
    return view('login');
});

// untuk contoh pdf
use App\Http\Controllers\PDFController;
Route::get('/contohpdf', [PDFController::class, 'contohpdf']);

// contoh simpan users ke pdf

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
 Route::get('/selamat', function () {


main
Route::get('/downloadpdfuser', function () {
    return 'Latihan PDF';
})->name('downloadpdf.user');


// contoh mengirim email
// use Illuminate\Support\Facades\Mail; sudah ada di atas
use App\Mail\TesMail;

// Route::get('/kirim-email', function () {
//     $nama = 'Bambang';

//     Mail::to('bams@gmail.com')->send(new TesMail($nama));

//     return 'Email berhasil dikirim ke Mailtrap!';
// });

// untuk contoh perusahaan
use App\Http\Controllers\PerusahaanController;
Route::resource('perusahaan', PerusahaanController::class);
Route::get('/perusahaan/destroy/{id}', [PerusahaanController::class,'destroy']);

// contoh sampel midtrans
use App\Http\Controllers\CobaMidtransController;
Route::get('/cekmidtrans', [CobaMidtransController::class, 'cekmidtrans']);

// proses pengiriman email
use App\Http\Controllers\PengirimanEmailController;
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailController::class, 'proses_kirim_email_pembayaran']);

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

