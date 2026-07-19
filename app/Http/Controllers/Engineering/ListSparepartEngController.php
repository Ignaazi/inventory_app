<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\ListSparepartEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListSparepartEngController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        $query = ListSparepartEng::latest();

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('sap_code', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('part_number', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $spareparts = $query->paginate(10)->withQueryString();

        // Mengarah ke file view blade yang sesuai
        return view('stock_eng.list_sparepart', compact('spareparts'));
    }

    public function store(Request $request)
    {
        // 1. Validasi dasar tipe data inputan
        $validated = $request->validate([
            'sap_code'    => 'nullable|string|max:100',
            'part_number' => 'nullable|string|max:100',
            'name'        => 'required|string|max:255',
            'category'    => 'required|string',
            'length'      => 'required|numeric',
            'width'       => 'required|numeric',
            'thickness'   => 'required|numeric',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. VALIDASI GANDA (Composite Unique Check)
        // Mengecek apakah kombinasi 4 kolom ini sudah terdaftar di database
        $isDuplicate = ListSparepartEng::where('name', $request->name)
            ->where('sap_code', $request->sap_code)
            ->where('part_number', $request->part_number)
            ->where('category', $request->category)
            ->exists();

        if ($isDuplicate) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'Data Gagal Disimpan! Kombinasi Nama, SAP Code, Part Number, dan Kategori tersebut sudah terdaftar di sistem.']);
        }

        // 3. Proses upload gambar jika ada
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('spareparts', 'public');
        }

        ListSparepartEng::create($validated);

        return redirect()->back()->with('success', 'Sparepart baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $sparepart = ListSparepartEng::findOrFail($id);

        // 1. Validasi dasar tipe data inputan
        $validated = $request->validate([
            'sap_code'    => 'nullable|string|max:100',
            'part_number' => 'nullable|string|max:100',
            'name'        => 'required|string|max:255',
            'category'    => 'required|string',
            'length'      => 'required|numeric',
            'width'       => 'required|numeric',
            'thickness'   => 'required|numeric',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. VALIDASI GANDA SAAT UPDATE (Abaikan ID milik data ini sendiri)
        $isDuplicate = ListSparepartEng::where('id', '!=', $id) // Mencegah bentrok dengan dirinya sendiri saat disimpan tanpa ubah data
            ->where('name', $request->name)
            ->where('sap_code', $request->sap_code)
            ->where('part_number', $request->part_number)
            ->where('category', $request->category)
            ->exists();

        if ($isDuplicate) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'Perubahan Gagal Disimpan! Kombinasi Nama, SAP Code, Part Number, dan Kategori ini sudah digunakan oleh item lain.']);
        }

        // 3. Proses upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($sparepart->image) {
                Storage::disk('public')->delete($sparepart->image);
            }
            $validated['image'] = $request->file('image')->store('spareparts', 'public');
        }

        $sparepart->update($validated);

        return redirect()->back()->with('success', 'Data sparepart berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sparepart = ListSparepartEng::findOrFail($id);
        
        // Hapus gambar dari storage disk jika ada sebelum menghapus data
        if ($sparepart->image) {
            Storage::disk('public')->delete($sparepart->image);
        }

        $sparepart->delete();

        return redirect()->back()->with('success', 'Sparepart berhasil dihapus!');
    }
}