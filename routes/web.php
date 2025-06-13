<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\CobaMidtransController;
use App\Http\Controllers\PengirimanEmailController;
use App\Http\Controllers\CoaController;
//use App\Http\Controllers\AbsensiController;


// Route dasar

// Halaman utama diarahkan ke login

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


// Halaman login

Route::get('/login', function () {
    return view('login');
});

// Proses login

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

// Logout

Route::post('/login', [AuthController::class, 'login']);

// Proses logout

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
Route::delete('/hapus/{menu_id}', [App\Http\Controllers\KeranjangController::class, 'hapus'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class);
Route::get('/lihatriwayat', [App\Http\Controllers\KeranjangController::class, 'lihatriwayat'])
    ->middleware(\App\Http\Middleware\CostumerMiddleware::class);

// Autorefresh
Route::get('/cek_status_pembayaran_pg', [App\Http\Controllers\KeranjangController::class, 'cek_status_pembayaran_pg']);
Route::get('/depan', [KeranjangController::class, 'daftarmenu'])->name('depan');

// PDF
Route::get('/contohpdf', [PDFController::class, 'contohpdf']);
Route::get('/downloadpdfuser', function () {
    return 'Latihan PDF';
})->name('downloadpdf.user');

// Perusahaan
// Route::resource('perusahaan', PerusahaanController::class);
// Route::get('/perusahaan/destroy/{id}', [PerusahaanController::class, 'destroy']);

// COA
Route::resource('coa', App\Http\Controllers\CoaController::class);


// untuk ubah password
Route::get('/ubahpassword', [App\Http\Controllers\AuthController::class, 'ubahpassword'])
    ->middleware('costumer')
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware('costumer')
;
// prosesubahpassword

//absensi
//Route::resource('absensi', AbsensiController::class);

// Route::resource('penggajian', PenggajianController::class);

Route::get('/laporan-penggajian', [\App\Http\Controllers\PDFController::class, 'penggajianPdf']);
// Halaman welcome (opsional)
Route::get('/welcome', function () {
    return view('welcome');
});

// PDF
Route::get('/contohpdf', [PDFController::class, 'contohpdf']);


// Midtrans
Route::get('/cekmidtrans', [CobaMidtransController::class, 'cekmidtrans']);

// Tes helper rupiah
Route::get('/tesrupiah', function() {
    return rupiah(1234567);
});

// COA
Route::resource('coa', CoaController::class);

// Absensi
//Route::resource('absensi', AbsensiController::class);

// Middleware khusus costumer
Route::middleware(\App\Http\Middleware\CostumerMiddleware::class)->group(function () {

    // Halaman costumer

    Route::get('/depan', [KeranjangController::class, 'daftarmenu'])->name('depan');

    //Route::get('/depan', [KeranjangController::class, 'costumer'])->name('depan');


    // Ubah password
    Route::get('/ubahpassword', [AuthController::class, 'ubahpassword'])->name('ubahpassword');
    Route::post('/prosesubahpassword', [AuthController::class, 'prosesubahpassword']);

    // Keranjang
    Route::post('/tambah', [KeranjangController::class, 'tambahKeranjang']);
    Route::get('/lihatkeranjang', [KeranjangController::class, 'lihatkeranjang']);
    Route::delete('/hapus/{menu_id}', [KeranjangController::class, 'hapus']);
    Route::get('/lihatriwayat', [KeranjangController::class, 'lihatriwayat']);
});

// Autorefresh status pembayaran
Route::get('/cek_status_pembayaran_pg', [KeranjangController::class, 'cek_status_pembayaran_pg']);
// proses pengiriman email
use App\Http\Controllers\PengirimanEmailGajiController;
Route::get('/proses_kirim_email_pembayaran_gaji', [PengirimanEmailGajiController::class, 'proses_kirim_email_pembayaran_gaji']);

Route::get('/test-email', function () {
    Mail::raw('Tes kirim email dari Laravel ke Mailtrap.', function ($message) {
        $message->to('tes@example.com') // bebas, Mailtrap akan tetap menerima
                ->subject('Tes Email');
    });
    return 'Email dikirim';
});

Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailController::class, 'proses_kirim_email_pembayaran']);

Route::get('/test-email', function () {
    Mail::raw('Tes kirim email dari Laravel ke Mailtrap.', function ($message) {
        $message->to('tes@example.com') // bebas, Mailtrap akan tetap menerima
                ->subject('Tes Email');
    });
    return 'Email dikirim';
});

use App\Http\Controllers\PengirimanEmailpenjualanController;
Route::get('/proses_kirim_email_invoice_penjualan', [PengirimanEmailpenjualanController::class, 'proses_kirim_email_invoice_penjualan']);

Route::get('/test-email', function () {
    Mail::raw('Tes kirim email dari Laravel ke Mailtrap.', function ($message) {
        $message->to('tes@example.com') // bebas, Mailtrap akan tetap menerima
                ->subject('Tes Email');
    });
    return 'Email dikirim';
});


