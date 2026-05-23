<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\ListSparepartEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\SparepartExport;
use Maatwebsite\Excel\Facades\Excel;

class ListSparepartEngController extends Controller
{
    /**
     * 1. Menampilkan Halaman List Master Data Sparepart
     */
    public function index(Request $request)
    {
        $query = ListSparepartEng::latest();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('category', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $spareparts = $query->paginate(10)->withQueryString();

        return view('stock_eng.list_sparepart', compact('spareparts'));
    }

    /**
     * 2. TAMBAHAN: Method Preview / Show Data (Untuk Icon Mata 👁️)
     * Mengembalikan data berbentuk JSON agar mudah ditampilkan di Modal Bootstrap/Tailwind 
     * tanpa perlu pindah halaman baru (mengikuti gaya SPA).
     */
    public function show($id)
    {
        $sparepart = ListSparepartEng::findOrFail($id);
        
        // Mengembalikan response JSON untuk ditangkap oleh AJAX/JavaScript di halaman View
        return response()->json([
            'status' => 'success',
            'data'   => $sparepart
        ]);
    }

    /**
     * 3. Menyimpan Data Sparepart Baru
     */
    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'thickness' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }

        ListSparepartEng::create($data);
        
        return back()->with('success', 'Master Sparepart added successfully, coy!');
    }
    
    /**
     * 4. Memperbarui Data Sparepart (Update Action ✏️)
     */
    public function update(Request $request, $id) 
    {
        $sparepart = ListSparepartEng::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'thickness' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada di storage public
            if($sparepart->image) {
                Storage::disk('public')->delete($sparepart->image);
            }
            // Simpan gambar baru
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }
        
        $sparepart->update($data);
        
        return back()->with('success', 'Master Sparepart updated successfully, coy!');
    }
    
    /**
     * 5. Menghapus Data Sparepart (Delete Action 🗑️)
     */
    public function destroy($id) 
    {
        $sparepart = ListSparepartEng::findOrFail($id);
        
        // Pastikan file gambar ikut terhapus dari storage biar gak menumpuk sampah
        if($sparepart->image) {
            Storage::disk('public')->delete($sparepart->image);
        }

        $sparepart->delete();
        return back()->with('success', 'Master Sparepart deleted successfully, coy!');
    }

    /**
     * 6. Export Excel Data
     */
    public function export() 
    {
        return Excel::download(new SparepartExport, 'master_spareparts_' . date('d_m_y') . '.xlsx');
    }
}