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
    public function store(Request $request)
    {
        $request->validate([
            'no_line' => 'required',
            'name_machine' => 'required',
        ]);
        $lastLine = ListLineProduction::orderBy('id', 'desc')->first();

        if (!$lastLine) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastLine->line_id);
            $nextNumber = $lastNumber + 1;
        }
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $autoLineId = 'SIIXSMTLINE' . $formattedNumber;
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