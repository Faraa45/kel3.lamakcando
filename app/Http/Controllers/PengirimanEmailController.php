<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengirimanemail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengirimanEmailController extends Controller
{
    public static function proses_kirim_email_pembayaran()
    {
        date_default_timezone_set('Asia/Jakarta');

        $data = DB::table('pembelian')
            ->join('vendor', 'pembelian.vendor_id', '=', 'vendor.id')
            ->where('pembelian.status', 'pesan')
            ->whereNotNull('vendor.email')
            ->whereNotIn('pembelian.id', function ($query) {
                $query->select('pembelian_id')->from('pengirimanemail');
            })
            ->select(
                'pembelian.id',
                'pembelian.no_faktur_pembelian',
                'vendor.email',
                'vendor.nama_vendor',
                'pembelian.total_tagihan',
                'pembelian.tanggal' // <-- gunakan ini saja
            )
            ->get();

        foreach ($data as $pembelian) {
            $id = $pembelian->id;
            $no_faktur = $pembelian->no_faktur_pembelian;
            $email = $pembelian->email;
            $nama_vendor = $pembelian->nama_vendor;
            $tanggal = $pembelian->tanggal ?? now(); // <-- gunakan kolom yang ada

            // Ambil item pembelian detail
            $items = DB::table('pembelian_bahan_baku')
                ->join('bahan_baku', 'pembelian_bahan_baku.bahan_baku_id', '=', 'bahan_baku.id')
                ->select(
                    'bahan_baku.nama_bahan as nama_bahan_baku',
                    'pembelian_bahan_baku.harga_beli',
                    DB::raw('SUM(pembelian_bahan_baku.jumlah) as total_bahan_baku')
                )
                ->where('pembelian_bahan_baku.pembelian_id', $id)
                ->groupBy('bahan_baku.nama_bahan', 'pembelian_bahan_baku.harga_beli')
                ->get();

            $total = $items->sum(function ($item) {
                return $item->harga_beli * $item->total_bahan_baku;
            });

            // Generate PDF
            $pdf = Pdf::loadView('pdf.invoice', [
                'no_faktur' => $no_faktur,
                'nama_vendor' => $nama_vendor,
                'tanggal' => Carbon::parse($tanggal)->format('d M Y'),
                'items' => $items,
                'total' => $total,
            ]);

            // Kirim email
            Mail::to($email)->send(new InvoiceMail([
                'nama_vendor' => $nama_vendor,
                'no_faktur' => $no_faktur,
            ], $pdf->output()));

            // Simpan status pengiriman
            Pengirimanemail::create([
                'pembelian_id' => $id,
                'status' => 'terkirim',
                'tgl_pengiriman_pesan' => now(),
            ]);
        }
        return view('autorefresh_email');
    }
            

}
