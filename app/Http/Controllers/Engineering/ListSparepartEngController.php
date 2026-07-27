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
                $q->where('sparepart_id', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('sap_code', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('part_number', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $spareparts = $query->paginate(10)->withQueryString();

        return view('stock_eng.list_sparepart', compact('spareparts'));
    }

    /**
     * Menampilkan halaman form tambah sparepart (add_spareparts.blade.php)
     */
    public function create()
    {
        return view('stock_eng.add_spareparts');
    }

    public function store(Request $request)
    {
        // 1. Validasi dasar tipe data inputan (Menerima sparepart_id dari form)
        $validated = $request->validate([
            'sap_code'     => 'nullable|string|max:100',
            'part_number'  => 'nullable|string|max:100',
            'sparepart_id' => 'required|string|max:255',
            'category'     => 'required|string',
            'length'       => 'required|numeric',
            'width'        => 'required|numeric',
            'thickness'    => 'required|numeric',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. VALIDASI GANDA (Composite Unique Check berdasarkan data baru)
        $isDuplicate = ListSparepartEng::where('sparepart_id', $request->sparepart_id)
            ->where('sap_code', $request->sap_code)
            ->where('part_number', $request->part_number)
            ->where('category', $request->category)
            ->exists();

        if ($isDuplicate) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['sparepart_id' => 'Data Gagal Disimpan! Kombinasi Sparepart ID, SAP Code, Part Number, dan Kategori tersebut sudah terdaftar di sistem.']);
        }

        // 3. Proses upload gambar jika ada
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('spareparts', 'public');
        }

        ListSparepartEng::create($validated);

        return redirect()->route('list-sparepart.index')->with('success', 'Sparepart baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $sparepart = ListSparepartEng::findOrFail($id);

        // 1. Validasi dasar tipe data inputan
        $validated = $request->validate([
            'sap_code'     => 'nullable|string|max:100',
            'part_number'  => 'nullable|string|max:100',
            'sparepart_id' => 'required|string|max:255',
            'category'     => 'required|string',
            'length'       => 'required|numeric',
            'width'       => 'required|numeric',
            'thickness'    => 'required|numeric',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. VALIDASI GANDA SAAT UPDATE (Abaikan ID milik data ini sendiri)
        $isDuplicate = ListSparepartEng::where('id', '!=', $id)
            ->where('sparepart_id', $request->sparepart_id)
            ->where('sap_code', $request->sap_code)
            ->where('part_number', $request->part_number)
            ->where('category', $request->category)
            ->exists();

        if ($isDuplicate) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['sparepart_id' => 'Perubahan Gagal Disimpan! Kombinasi Sparepart ID, SAP Code, Part Number, dan Kategori ini sudah digunakan oleh item lain.']);
        }

        // 3. Proses upload gambar baru
        if ($request->hasFile('image')) {
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
        
        if ($sparepart->image) {
            Storage::disk('public')->delete($sparepart->image);
        }

        $sparepart->delete();

        return redirect()->back()->with('success', 'Sparepart berhasil dihapus!');
    }
}