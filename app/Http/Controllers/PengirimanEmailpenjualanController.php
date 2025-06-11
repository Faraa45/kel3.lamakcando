<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengirimanemailpenjualan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoicepenjualanMail;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanEmailPenjualanController extends Controller
{
    public function proses_kirim_email_invoice_penjualan(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        // 1. Ambil data penjualan yang sudah dibayar tapi belum dikirim email
        $data = DB::table('penjualan')
            ->join('costumer', 'penjualan.costumer_id', '=', 'costumer.id')
            ->join('users', 'costumer.user_id', '=', 'users.id')
            ->where('status', 'bayar')
            ->whereNotIn('penjualan.id', function ($query) {
                $query->select('penjualan_id')->from('pengirimanemailpenjualan');
            })
            ->select('penjualan.id', 'penjualan.no_faktur', 'users.email', 'penjualan.costumer_id')
            ->get();

        foreach ($data as $p) {
            $id = $p->id;
            $no_faktur = $p->no_faktur;
            $email = $p->email;
            $costumer_id = $p->costumer_id;

            // Ambil detail menu yang dibeli
            $menu = DB::table('penjualan')
                ->join('penjualan_menu', 'penjualan.id', '=', 'penjualan_menu.penjualan_id')
                ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                ->join('menu', 'penjualan_menu.menu_id', '=', 'menu.id')
                ->join('costumer', 'penjualan.costumer_id', '=', 'costumer.id')
                ->select(
                    'penjualan.id',
                    'penjualan.no_faktur',
                    'costumer.nama_costumer',
                    'penjualan_menu.menu_id',
                    'menu.nama_menu',
                    'penjualan_menu.harga_jual',
                    'menu.foto_menu',
                    DB::raw('SUM(penjualan_menu.jml) as total_menu'),
                    DB::raw('SUM(penjualan_menu.harga_jual * penjualan_menu.jml) as total_belanja')
                )
                ->where('penjualan.costumer_id', $costumer_id)
                ->where('penjualan.id', $id)
                ->groupBy(
                    'penjualan.id',
                    'penjualan.no_faktur',
                    'costumer.nama_costumer',
                    'penjualan_menu.menu_id',
                    'menu.nama_menu',
                    'penjualan_menu.harga_jual',
                    'menu.foto_menu'
                )
                ->get();

            $pdf = Pdf::loadView('pdf.invoice-penjualan', [
                'no_faktur' => $no_faktur,
                'nama_costumer' => $menu[0]->nama_costumer ?? '-',
                'items' => $menu,
                'total' => $menu->sum('total_belanja'),
                'tanggal' => now()->format('d-M-Y'),
            ]);

            // Data untuk email
            $dataAtributPelanggan = [
                'costumer_name' => $menu[0]->nama_costumer ?? '-',
                'invoice_number' => $no_faktur
            ];

            // Kirim email
            Mail::to($email)->send(new InvoicepenjualanMail($dataAtributPelanggan, $pdf->output()));

            // Delay untuk menghindari spam atau throttle
            sleep(5);

            // Catat pengiriman email
            Pengirimanemailpenjualan::create([
                'penjualan_id' => $id,
                'status' => 'sudah terkirim',
                'tgl_pengiriman_pesan' => now(),
            ]);
        }

        // Kembali ke view autorefresh
        return view('autorefresh_email');
    }
}
