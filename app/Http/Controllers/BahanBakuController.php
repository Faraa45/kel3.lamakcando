<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    /**
     * Tampilkan daftar semua bahan baku.
     */
    public function index()
    {
        $data = BahanBaku::all();
        return view('bahan_baku.index', compact('data'));
    }

    /**
     * Tampilkan form untuk menambahkan bahan baku baru.
     */
    public function create()
    {
        $kode = BahanBaku::getKodeBahan(); // otomatis generate kode
        return view('bahan_baku.create', compact('kode'));
    }

    /**
     * Simpan data bahan baku baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_bahan' => 'required|unique:bahan_baku,kode_bahan',
            'nama_bahan' => 'required',
            'satuan' => 'required',
            'stok' => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        BahanBaku::create($request->all());

        return redirect()->route('bahan-baku.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail bahan baku tertentu.
     */
    public function show(BahanBaku $bahan_baku)
    {
        return view('bahan_baku.show', compact('bahan_baku'));
    }

    /**
     * Tampilkan form untuk edit data bahan baku.
     */
    public function edit(BahanBaku $bahan_baku)
    {
        return view('bahan_baku.edit', compact('bahan_baku'));
    }

    /**
     * Update data bahan baku di database.
     */
    public function update(Request $request, BahanBaku $bahan_baku)
    {
        $request->validate([
            'nama_bahan' => 'required',
            'satuan' => 'required',
            'stok' => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $bahan_baku->update($request->all());

        return redirect()->route('bahan-baku.index')->with('success', 'Data bahan baku berhasil diperbarui.');
    }

    /**
     * Hapus data bahan baku dari database.
     */
    public function destroy(BahanBaku $bahan_baku)
    {
        $bahan_baku->delete();
        return redirect()->route('bahan-baku.index')->with('success', 'Data bahan baku berhasil dihapus.');
    }
}
