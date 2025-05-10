<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index()
    {
        $absensi = Absensi::with('pegawai')->latest()->get();
        return view('absensi.index', compact('absensi'));
    }

    public function create()
    {
        $pegawai = Pegawai::all();
        return view('absensi.create', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required',
            'no_absensi' => 'required',
            'status' => 'required',
            'tgl' => 'required|date',
        ]);

        Absensi::create($request->all());
        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil ditambahkan.');
    }
}

