@extends('admin')

@section('content')
<div class="-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-screen">
    <div class="p-10">
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black uppercase text-slate-800 dark:text-white">Database Barcode</h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Engineering / Registered Barcode Logs</p>
            </div>
            <a href="{{ route('barcode.parsing') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-black px-5 py-3 rounded-xl transition-all uppercase text-xs tracking-widest">
                ← Back To Customizer
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-xs uppercase tracking-wide">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-strokedark flex items-center justify-between">
                <h2 class="text-sm font-black uppercase text-slate-800 dark:text-white">All Registered Composite Data</h2>
                <span class="bg-slate-100 dark:bg-meta-4 text-slate-600 dark:text-slate-300 text-[10px] font-black px-3 py-1.5 rounded-lg border border-slate-200 dark:border-strokedark">
                    TOTAL: {{ $barcodes->count() }} DATA
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-meta-4 border-b border-slate-100 dark:border-strokedark">
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-16">ID</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-44">Barcode Type</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-32">Dimension Size</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500">Final Composite Content</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-44">Saved At</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-24 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-strokedark">
                        @forelse($barcodes as $barcode)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/20 transition-all">
                            <td class="p-4 text-xs font-black text-slate-400">#{{ $barcode->id }}</td>
                            <td class="p-4">
                                <span class="bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-[10px] font-black px-2.5 py-1.5 rounded-lg border border-indigo-100 dark:border-indigo-800 uppercase tracking-wide">
                                    {{ $barcode->barcode_type }}
                                </span>
                            </td>
                            <td class="p-4 text-xs font-black text-slate-600 dark:text-slate-300">
                                {{ $barcode->barcode_size }}mm × {{ $barcode->barcode_size }}mm
                            </td>
                            <td class="p-4">
                                <div class="bg-slate-50 dark:bg-slate-800/60 font-mono font-black text-xs text-indigo-600 dark:text-indigo-400 px-3 py-2 rounded-xl border border-slate-100 dark:border-strokedark break-all inline-block max-w-lg shadow-sm">
                                    {{ $barcode->final_content }}
                                </div>
                            </td>
                            <td class="p-4 text-xs text-slate-400 font-bold">
                                {{ \Carbon\Carbon::parse($barcode->created_at)->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('barcode.db.delete', $barcode->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data barcode ini dari database?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-black px-3 py-2 rounded-lg transition-all text-[10px] uppercase tracking-wider border border-rose-100">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                                    </svg>
                                    <p class="text-xs font-black uppercase text-slate-400 tracking-widest">No Barcode Registered in Database</p>
                                    <p class="text-[10px] font-bold text-slate-300 uppercase mt-1">Generate a composite string from customizer first</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection