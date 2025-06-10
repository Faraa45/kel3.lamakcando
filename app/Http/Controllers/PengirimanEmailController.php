<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengirimanemail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanEmailController extends Controller
{
    public static function proses_kirim_email_pembayaran()
    {
        date_default_timezone_set('Asia/Jakarta');

        // 1. Query data pembelian yang sudah bayar dan belum dikirim email
        $data = DB::table('pembelian')
            ->join('pembeli', 'pembelian.pembeli_id', '=', 'pembeli.id')
            ->join('users', 'pembeli.user_id', '=', 'users.id')
            ->where('status', 'bayar') // hanya ambil pembelian yang sudah bayar
            ->whereNotIn('pembelian.id', function ($query) {
                $query->select('pembelian_id')->from('pengirimanemail');
            })
            ->select('pembelian.id', 'pembelian.no_faktur', 'users.email', 'pembelian.pembeli_id')
            ->get();

        foreach ($data as $p) {
            $id = $p->id;
            $no_faktur = $p->no_faktur;
            $email = $p->email;
            $pembeli_id = $p->pembeli_id;

            // Query detail barang pembelian
            $barang = DB::table('pembelian')
                ->join('pembelian_barang', 'pembelian.id', '=', 'pembelian_barang.pembelian_id')
                ->join('pembayaran', 'pembelian.id', '=', 'pembayaran.pembelian_id')
                ->join('barang', 'pembelian_barang.barang_id', '=', 'barang.id')
                ->join('pembeli', 'pembelian.pembeli_id', '=', 'pembeli.id')
                ->select(
                    'pembelian.id',
                    'pembelian.no_faktur',
                    'pembeli.nama_pembeli',
                    'pembelian_barang.barang_id',
                    'barang.nama_barang',
                    'pembelian_barang.harga_beli',
                    'barang.foto',
                    DB::raw('SUM(pembelian_barang.jml) as total_barang'),
                    DB::raw('SUM(pembelian_barang.harga_beli * pembelian_barang.jml) as total_belanja')
                )
                ->where('pembelian.pembeli_id', '=', $pembeli_id)
                ->where('pembelian.id', '=', $id)
                ->groupBy(
                    'pembelian.id',
                    'pembelian.no_faktur',
                    'pembeli.nama_pembeli',
                    'pembelian_barang.barang_id',
                    'barang.nama_barang',
                    'pembelian_barang.harga_beli',
                    'barang.foto'
                )
                ->get();

            // Generate PDF invoice
            $pdf = Pdf::loadView('pdf.invoice', [
                'no_faktur' => $p->no_faktur,
                'nama_pembeli' => $barang[0]->nama_pembeli ?? '-',
                'items' => $barang,
                'total' => $barang->sum('total_belanja'),
                'tanggal' => now()->format('d-M-Y'),
            ]);

            // Data untuk email
            $dataAtributPelanggan = [
                'customer_name' => $barang[0]->nama_pembeli,
                'invoice_number' => $p->no_faktur
            ];

            // Kirim email
            Mail::to($email)->send(new InvoiceMail($dataAtributPelanggan, $pdf->output()));

            // Delay 5 detik antar email
            sleep(5);

            // Catat pengiriman
            Pengirimanemail::create([
                'pembelian_id' => $id,
                'status' => 'sudah terkirim',
                'tgl_pengiriman_pesan' => now(),
            ]);
        }

        return view('autorefresh_email');
    }
}

// proses pengiriman email
use App\Http\Controllers\PengirimanEmailController;
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailController::class, 'proses_kirim_email_pembayaran']);
