<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaterialReceivedController extends Controller
{
    /**
     * Menampilkan daftar material yang sudah tiba.
     */
    public function index()
    {
        // Pastikan file view ada di resources/views/cost_section/material_received.blade.php
        return view('cost_section.material_received');
    }

    /**
     * Fungsi opsional jika nanti ada proses update status 
     * saat Engineering mengambil barang tersebut.
     */
    public function update(Request $request, $id)
    {
        // Logika update status di sini
        return back()->with('success', 'Material Status Updated.');
    }
}