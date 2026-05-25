@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 md:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
        
        <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Create Manual OUT</h2>
                <p class="text-xs text-slate-500">Pilih nomor request, item Nozzle, lokasi Rak asal, dan pasangkan dengan Barcode ID.</p>
            </div>
            
            <a href="{{ route('eng.out') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 px-4 py-2.5 text-xs font-bold text-white shadow-md transition-all">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
                <p class="mb-2">❌ Gagal Simpan! Silakan periksa komponen berikut:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('eng.out.store') }}" method="POST" id="form-manual-out">
            @csrf
            <input type="hidden" name="source" value="manual">
            <input type="hidden" name="no_nozzle" id="hidden-no-nozzle" value="{{ old('no_nozzle') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                
                {{-- 🌟 NEW: DROPDOWN PRODUCTION REQUEST --}}
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Request Sparepart ID (Production Request)</label>
                    <div class="relative">
                        <select name="request_sparepart_id" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Select Production Request No --</option>
                            @foreach($productionRequests as $req)
                                <option value="{{ $req->request_no }}" {{ old('request_sparepart_id') == $req->request_no ? 'selected' : '' }}>
                                    {{ $req->request_no }} | Sparepart: {{ $req->sparepart_name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    @error('request_sparepart_id')
                        <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 1. DROPDOWN PILIHAN NOZZLE --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">No Nozzle (Stock Eng)</label>
                    <div class="relative">
                        <select name="stock_eng_id" id="select-nozzle" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Select Nozzle To Out --</option>
                            @foreach($stocks as $item)
                                <option value="{{ $item->id }}" data-nozzle="{{ $item->no_nozzle }}" {{ old('stock_eng_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->no_nozzle }} | {{ $item->sap_code }} | Stok: {{ $item->qty }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 2. DROPDOWN PILIHAN RAK --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Lokasi Rak Asal</label>
                    <select name="rak_id" id="select-rak" required 
                            class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Select Rak --</option>
                        @foreach($raks as $rak)
                            <option value="{{ $rak->id }}" {{ old('rak_id') == $rak->id ? 'selected' : '' }}>{{ $rak->nama_rak }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. DROPDOWN BARCODE --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Barcode ID Label</label>
                    <select name="barcode_id" id="select-barcode" required 
                            class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Select Barcode --</option>
                        @foreach($barcodes as $barcode)
                            <option value="{{ $barcode->id }}" {{ old('barcode_id') == $barcode->id ? 'selected' : '' }}>{{ $barcode->barcode_id }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. QUANTITY --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Amount Of Outgoing Stock</label>
                    <input type="number" name="qty_out" value="{{ old('qty_out') }}" required min="1" 
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white">
                </div>

                {{-- 5. COMMENT --}}
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Comment / Note</label>
                    <textarea name="comment" rows="2" class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm">{{ old('comment') }}</textarea>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                {{-- Tombol diperbarui dengan onclick untuk memastikan trigger submit --}}
                <button type="button" onclick="document.getElementById('form-manual-out').submit();" class="bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md">
                    Submit Out
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectNozzle = document.getElementById('select-nozzle');
        const hiddenNoNozzle = document.getElementById('hidden-no-nozzle');

        selectNozzle.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            hiddenNoNozzle.value = selectedOption.getAttribute('data-nozzle') || '';
        });
    });
</script>
@endsection