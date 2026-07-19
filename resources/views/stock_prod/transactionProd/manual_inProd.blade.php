@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 md:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
        
        <!-- HEADER FORM -->
        <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Create Manual IN Production</h2>
                <p class="text-xs text-slate-500">Pilih Lini Produksi tujuan, lalu pilih Barcode ID Label asal Engineering untuk dimasukkan ke stok lini.</p>
            </div>
            
            <a href="{{ route('prod.transaction.in') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 px-4 py-2.5 text-xs font-bold text-white shadow-md transition-all">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <!-- NOTIFIKASI ERROR / SELEKSI PELACAK -->
        @if (session('error'))
            <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- MAIN FORM -->
        <form action="{{ route('prod.transaction.in.store_manual') }}" method="POST" id="form-manual-in">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                {{-- 1. DROPDOWN TARGET LINI PRODUKSI --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Target Lini Produksi <span class="text-amber-500">*</span></label>
                    <div class="relative">
                        <select name="line_id" id="select-line" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Select Target Line --</option>
                            @foreach($lines as $line)
                                <option value="{{ $line->line_id }}" {{ old('line_id') == $line->line_id ? 'selected' : '' }}>
                                    {{ $line->no_line ?? $line->line_id }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- 2. DROPDOWN BARCODE ID SELECT OPTION --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Barcode ID Label <span class="text-amber-500">* Ready to Production</span></label>
                    <div class="relative">
                        <select name="barcode_scan" id="select-barcode" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none font-mono">
                            <option value="">-- Select Barcode ID --</option>
                            @foreach($barcodes as $barcode)
                                <option value="{{ $barcode->final_content }}" {{ old('barcode_scan') == $barcode->final_content ? 'selected' : '' }}>
                                    {{ $barcode->barcode_id }} ({{ Str::limit($barcode->final_content, 25) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BUTTON ACTIONS -->
            <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md hover:opacity-90 transition-opacity">
                    Submit In Stock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection