<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\ListLineProduction;
use Illuminate\Http\Request;

class ListLineProductionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $lines = ListLineProduction::when($search, function($query, $search) {
            return $query->where('line_id', 'LIKE', "%{$search}%")
                         ->orWhere('no_line', 'LIKE', "%{$search}%")
                         ->orWhere('name_machine', 'LIKE', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return view('stock_prod.listLineProduction', compact('lines'));
    }

    // 🚀 UPDATE DI SINI: Auto Generate Line ID
    public function store(Request $request)
    {
        // Validasi hanya untuk no_line dan name_machine, karena line_id di-generate sistem
        $request->validate([
            'no_line' => 'required',
            'name_machine' => 'required',
        ]);

        // 1. Ambil data terakhir berdasarkan ID terbesar
        $lastLine = ListLineProduction::orderBy('id', 'desc')->first();

        if (!$lastLine) {
            // Jika database masih kosong, mulai dari 1
            $nextNumber = 1;
        } else {
            // Ambil angka dari line_id terakhir (misal dari SIIXSMTLINE005 diambil angka 5)
            // preg_replace akan menghapus semua karakter selain angka
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastLine->line_id);
            $nextNumber = $lastNumber + 1;
        }

        // 2. Format angka menjadi minimal 3 digit (001, 002), dan otomatis bertambah jadi 4 digit (1000), dst jika sudah jutaan
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        // 3. Gabungkan prefix string sesuai keinginan kamu
        $autoLineId = 'SIIXSMTLINE' . $formattedNumber;

        // 4. Simpan ke database
        ListLineProduction::create([
            'line_id' => $autoLineId,
            'no_line' => $request->no_line,
            'name_machine' => $request->name_machine,
        ]);

        return redirect()->back()->with('success', 'Production Line ' . $autoLineId . ' successfully registered!');
    }

    public function update(Request $request, $id)
    {
        $line = ListLineProduction::findOrFail($id);

        // Saat update, line_id dikunci (readonly) agar tidak merubah kode scan yang sudah terdaftar
        $request->validate([
            'no_line' => 'required',
            'name_machine' => 'required',
        ]);

        $line->update([
            'no_line' => $request->no_line,
            'name_machine' => $request->name_machine,
        ]);

        return redirect()->back()->with('success', 'Production Line updated successfully!');
    }

    public function destroy($id)
    {
        $line = ListLineProduction::findOrFail($id);
        $line->delete();

        return redirect()->back()->with('success', 'Production Line deleted successfully!');
    }
}