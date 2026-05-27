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
    public function show($id)
    {
        $sparepart = ListSparepartEng::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data'   => $sparepart
        ]);
    }
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
            if($sparepart->image) {
                Storage::disk('public')->delete($sparepart->image);
            }
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }    
        $sparepart->update($data);    
        return back()->with('success', 'Master Sparepart updated successfully, coy!');
           }   
    public function destroy($id) 
     {
        $sparepart = ListSparepartEng::findOrFail($id);
        
        if($sparepart->image) {
            Storage::disk('public')->delete($sparepart->image);
        }

        $sparepart->delete();
        return back()->with('success', 'Master Sparepart deleted successfully, coy!');
      }
    public function export() 
    {
        return Excel::download(new SparepartExport, 'master_spareparts_' . date('d_m_y') . '.xlsx');
    }
}