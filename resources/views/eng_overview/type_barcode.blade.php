@extends('admin')

@section('content')
<div class="-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-screen">
    <div class="p-10">
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black uppercase text-slate-800 dark:text-white">Barcode Structure Types</h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Engineering / Component Structure Logs</p>
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
                <h2 class="text-sm font-black uppercase text-slate-800 dark:text-white">Component Structure Segment Logs</h2>
                <span class="bg-slate-100 dark:bg-meta-4 text-slate-600 dark:text-slate-300 text-[10px] font-black px-3 py-1.5 rounded-lg border border-slate-200 dark:border-strokedark">
                    TOTAL: {{ $types->count() }} SEGMENTS
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-meta-4 border-b border-slate-100 dark:border-strokedark">
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-16">ID</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-44">Character Type</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-36">Length Limit</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500">Segment Value</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-44">Captured At</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-24 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-strokedark">
                        @forelse($types as $type)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/20 transition-all">
                            <td class="p-4 text-xs font-black text-slate-400">#{{ $type->id }}</td>
                            <td class="p-4">
                                <span class="bg-slate-800 text-white dark:bg-white dark:text-slate-800 text-[9px] font-black px-2.5 py-1.5 rounded-lg uppercase tracking-wider">
                                    {{ $type->char_type }}
                                </span>
                            </td>
                            <td class="p-4 text-xs font-black text-slate-600 dark:text-slate-300">
                                {{ $type->char_length }} Chars
                            </td>
                            <td class="p-4">
                                <div class="font-mono font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 px-3 py-1.5 rounded-lg border border-emerald-100 dark:border-emerald-900/40 inline-block text-xs">
                                    {{ $type->char_value }}
                                </div>
                            </td>
                            <td class="p-4 text-xs text-slate-400 font-bold">
                                {{ \Carbon\Carbon::parse($type->created_at)->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('barcode.type.delete', $type->id) }}" method="POST" onsubmit="return confirm('Hapus log konfigurasi komponen ini?');">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                    </svg>
                                    <p class="text-xs font-black uppercase text-slate-400 tracking-widest">No Component Structure Logged Yet</p>
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