<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\ListSparepartEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk hapus foto lama jika perlu

class ListSparepartEngController extends Controller
{
    public function index()
    {
        $spareparts = ListSparepartEng::latest()->paginate(10);
        return view('stock_eng.list_sparepart', compact('spareparts'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'qty' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        // Ambil semua input dulu
        $data = $request->all();
        
        // Jika ada file, timpa isi $data['image'] dengan path baru
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }

        // Simpan menggunakan $data, BUKAN $request->all()
        ListSparepartEng::create($data);
        
        return back()->with('success', 'Sparepart added successfully');
    }
    
    public function update(Request $request, $id) {
        $sparepart = ListSparepartEng::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'qty' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Opsional: Hapus foto lama jika ingin hemat storage
            if($sparepart->image) {
                Storage::disk('public')->delete($sparepart->image);
            }
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }
        
        // Update menggunakan $data
        $sparepart->update($data);
        
        return back()->with('success', 'Sparepart updated successfully');
    }
    
    public function destroy($id) {
        $sparepart = ListSparepartEng::findOrFail($id);
        
        // Hapus foto dari folder storage sebelum data di DB dihapus
        if($sparepart->image) {
            Storage::disk('public')->delete($sparepart->image);
        }

        $sparepart->delete();
        return back()->with('success', 'Sparepart deleted successfully');
    }
}