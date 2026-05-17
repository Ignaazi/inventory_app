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
        $query = ListSparepartEng::latest();

        // Fitur pencarian nama & kategori di web
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

    public function store(Request $request) {
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
        
        return back()->with('success', 'Sparepart added successfully');
    }
    
    public function update(Request $request, $id) {
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
            if($sparepart->image) {
                Storage::disk('public')->delete($sparepart->image);
            }
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }
        
        $sparepart->update($data);
        
        return back()->with('success', 'Sparepart updated successfully');
    }
    
    public function destroy($id) {
        $sparepart = ListSparepartEng::findOrFail($id);
        
        if($sparepart->image) {
            Storage::disk('public')->delete($sparepart->image);
        }

        $sparepart->delete();
        return back()->with('success', 'Sparepart deleted successfully');
    }
}