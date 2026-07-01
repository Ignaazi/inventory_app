@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 md:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
        
        <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Create Manual RETURN</h2>
                <p class="text-xs text-slate-500">Pilih nomor request, item Nozzle, lokasi Rak tujuan, dan pasangkan dengan Barcode ID untuk melakukan pengembalian stock.</p>
            </div>
            
            {{-- Kembali ke rute index return milik stock_eng --}}
            <a href="{{ route('stock_eng.transaction.return') }}" 
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

        {{-- Route disesuaikan ke simpan return (pastikan rute store ini sesuai di web.php Anda) --}}
        <form action="{{ route('stock_eng.transaction.return.store') }}" method="POST" id="form-manual-return">
            @csrf
            <input type="hidden" name="source" value="manual">
            <input type="hidden" name="no_nozzle" id="hidden-no-nozzle" value="{{ old('no_nozzle') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                
                {{-- 1. NIK KARYAWAN --}}
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">NIK Karyawan Pengembali</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" required placeholder="Masukkan NIK Karyawan"
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @error('nik')
                        <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 2. DROPDOWN PRODUCTION REQUEST --}}
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Request Sparepart ID (Production Request)</label>
                    <div class="relative">
                        <select name="request_sparepart_id" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Select Production Request No --</option>
                            @if(isset($productionRequests))
                                @foreach($productionRequests as $req)
                                    <option value="{{ $req->request_no }}" {{ old('request_sparepart_id') == $req->request_no ? 'selected' : '' }}>
                                        {{ $req->request_no }} | Sparepart: {{ $req->sparepart_name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    @error('request_sparepart_id')
                        <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 3. DROPDOWN PILIHAN NOZZLE --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">No Nozzle (Stock Eng)</label>
                    <div class="relative">
                        <select name="stock_eng_id" id="select-nozzle" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Select Nozzle To Return --</option>
                            @if(isset($stocks))
                                @foreach($stocks as $item)
                                    <option value="{{ $item->id }}" data-nozzle="{{ $item->no_nozzle }}" {{ old('stock_eng_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->no_nozzle }} | {{ $item->sap_code }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- 4. DROPDOWN PILIHAN RAK TUJUAN --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Lokasi Rak Tujuan</label>
                    <div class="relative">
                        <select name="rak_id" id="select-rak" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Select Rak --</option>
                            @if(isset($raks))
                                @foreach($raks as $rak)
                                    <option value="{{ $rak->id }}" {{ old('rak_id') == $rak->id ? 'selected' : '' }}>{{ $rak->nama_rak }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- 5. DROPDOWN BARCODE --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Barcode ID Label</label>
                    <div class="relative">
                        <select name="barcode_id" id="select-barcode" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Select Barcode --</option>
                            @if(isset($barcodes))
                                @foreach($barcodes as $barcode)
                                    <option value="{{ $barcode->id }}" {{ old('barcode_id') == $barcode->id ? 'selected' : '' }}>{{ $barcode->barcode_id }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- 6. QUANTITY RETURN --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Amount Of Returning Stock (Qty)</label>
                    <input type="number" name="qty_return" value="{{ old('qty_return') }}" required min="1" 
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>

                {{-- 7. COMMENT --}}
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Comment / Note / Alasan Return</label>
                    <textarea name="comment" rows="2" placeholder="Berikan catatan alasan return sparepart..."
                              class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('comment') }}</textarea>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="button" onclick="document.getElementById('form-manual-return').submit();" class="bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md hover:brightness-105 transition-all">
                    Submit Return
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectNozzle = document.getElementById('select-nozzle');
        const hiddenNoNozzle = document.getElementById('hidden-no-nozzle');

        if (selectNozzle) {
            selectNozzle.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                hiddenNoNozzle.value = selectedOption.getAttribute('data-nozzle') || '';
            });
        }
    });
</script>
@endsection