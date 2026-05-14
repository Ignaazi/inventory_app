@extends('admin')

@section('content')
<div class="mx-auto max-w-2xl p-4 md:p-10 text-center">
    <a href="{{ route('eng.in') }}" class="text-sm text-slate-400 mb-4 inline-block hover:text-indigo-600">← Kembali ke History</a>
    
    <form action="{{ route('eng.in.store') }}" method="POST" class="bg-white dark:bg-boxdark p-8 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700">
        @csrf
        <!-- PENYEBABNYA DISINI: Tambahkan input hidden source -->
        <input type="hidden" name="source" value="manual">

        <h2 class="text-2xl font-bold mb-6">Pilih Manual</h2>

        <div class="mb-6 text-left">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Pilih Item Nozzle</label>
            <select name="stock_eng_id" id="select-manual" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Cari Nozzle / Part No --</option>
                @foreach($stocks as $item)
                    <option value="{{ $item->id }}">{{ $item->no_nozzle }} | {{ $item->sap_code }} (Stok: {{ $item->qty }})</option>
                @endforeach
            </select>
            @error('stock_eng_id')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-8">
            <label class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-2 block">Jumlah Stok Masuk</label>
            <input type="number" name="qty_in" required min="1" class="w-full text-center text-6xl font-black bg-slate-50 dark:bg-slate-800 rounded-3xl p-6 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @error('qty_in')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6 text-left">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Keterangan (Opsional)</label>
            <input type="text" name="remark" placeholder="Contoh: Barang masuk dari Supplier A" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>

        <button type="submit" class="w-full bg-slate-800 text-white py-6 rounded-3xl text-xl font-black shadow-xl hover:bg-slate-700 transition-all">
            UPDATE STOK MANUAL
        </button>
    </form>
</div>
@endsection