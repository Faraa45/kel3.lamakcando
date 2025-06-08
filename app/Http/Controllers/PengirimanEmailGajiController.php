<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengirimanemailgaji;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PembayaranGaji;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengirimanEmailGajiController extends Controller
{
    public static function proses_kirim_email_pembayaran_gaji()
    {
        date_default_timezone_set('Asia/Jakarta');

        $data = DB::table('penggajian')
            ->join('pegawai', 'penggajian.pegawai_id', '=', 'pegawai.id')
            ->where('status_pembayaran', 'dibayar')
            ->whereNotNull('pegawai.email')
            ->whereNotIn('penggajian.id', function ($query) {
                $query->select('penggajian_id')->from('pengirimanemailgaji');
            })
            ->select(
                'penggajian.id',
                'penggajian.no_slip_gaji',
                'pegawai.email',
                'pegawai.nama_pegawai',
                'penggajian.total_gaji',
                'penggajian.tgl'
            )
            ->get();

        foreach ($data as $gaji) {
            $id = $gaji->id;
            $no_slip = $gaji->no_slip_gaji;
            $email = $gaji->email;
            $nama_pegawai = $gaji->nama_pegawai;
            $total_gaji = $gaji->total_gaji;
            $tanggal = $gaji->tgl ?? now(); // Fallback jika null

            // ❗️ Validasi email kosong
            if (empty($email)) {
                \Log::warning("Email kosong untuk pegawai: {$nama_pegawai} (ID gaji: {$id})");
                continue;
            }

            // Generate PDF
            $pdf = Pdf::loadView('pdf.slip-gaji', [
                'no_slip_gaji' => $no_slip,
                'nama_pegawai' => $nama_pegawai,
                'total_gaji' => $total_gaji,
                'tgl' => Carbon::parse($tanggal)->format('d M Y'),
            ]);

            // Data untuk email template
            $dataEmail = [
                'nama' => $nama_pegawai,
                'no_slip' => $no_slip,
            ];

            // Kirim email
            Mail::to($email)->send(new PembayaranGaji($dataEmail, $pdf->output()));

            sleep(5); // Batasi kirim beruntun

            Pengirimanemailgaji::create([
                'penggajian_id' => $id,
                'status' => 'sudah terkirim',
                'tgl_pengiriman_pesan' => now(),
            ]);
        }

        return view('autorefresh_email_gaji');
    }
}
