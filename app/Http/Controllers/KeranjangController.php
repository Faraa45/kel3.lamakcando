<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Menu; //untuk akses kelas model barang

class KeranjangController extends Controller
{
    public function daftarmenu()
    {
        // ambil data barang
        $menu = Menu::all();
        // kirim ke halaman view
        return view('galeri',
                        [ 
                            'menu'=>$menu,
                        ]
                    ); 
    }

