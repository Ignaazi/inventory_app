@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

<style>
    .font-nunito { font-family: 'Nunito', sans-serif !important; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">
    
    <div class="max-w-3xl mx-auto">
        
        {{-- Flash Notification Error Feedback --}}
        @if(session('error'))
            <div class="mb-4 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-950/30 dark:border-rose-900/50 px-4 py-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <p class="text-sm font-bold text-rose-800 dark:text-rose-400">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Main Form Card Container --}}
        <div class="w-full overflow-hidden rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-md">
            
            <div class="border-b border-gray-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between bg-transparent">
                <div>
                    <h3 class="text-base md:text-lg font-black text-slate-950 dark:text-white uppercase tracking-tight">
                        <i class="fa-solid fa-keyboard text-blue-500 mr-1.5"></i> Manual Out Log Creation
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">Kurangi kuantitas stok lantai produksi secara manual dengan validasi barcode</p>
                </div>
                <a href="{{ url('/prod/transaction/out') }}" class="text-xs font-black text-slate-400 hover:text-slate-600 dark:hover:text-white uppercase no-underline transition-colors">
                    Cancel
                </a>
            </div>

            <div class="p-6">
                {{-- Form wajib menggunakan enctype untuk handling berkas foto/file gambar --}}
                <form action="{{ url('/prod/transaction/out/store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="process_type" value="manual">

                    {{-- 1. PILIH ITEM / BARCODE STOK PROD YANG MAU DIKURANGI --}}
                    <div>
                        <label for="stock_prods_id" class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Select Production Component Stock / Barcode <span class="text-rose-500">*</span>
                        </label>
                        <select name="stock_prods_id" id="stock_prods_id" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm font-bold text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm" required>
                            <option value="" selected disabled>-- Pilih Item Komponen Aktif di Lini --</option>
                            @foreach($activeStocks as $stock)
                                <option value="{{ $stock->id }}" {{ old('stock_prods_id') == $stock->id ? 'selected' : '' }}>
                                    Lini: {{ $stock->line_name ?? $stock->line_id }} | Barcode: {{ $stock->barcode_code ?? 'No Barcode' }} | Item: {{ $stock->item_code ?? '120' }} (Stok: {{ $stock->qty }} Pcs)
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 font-bold mt-1">Sistem otomatis memotong nilai stok (-1 Qty) pada ID stock_prods terpilih setelah log berhasil disimpan.</p>
                    </div>

                    {{-- 2. INPUT NIK KARYAWAN PIC --}}
                    <div>
                        <label for="nik_karyawan" class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            NIK Karyawan / PIC Lapangan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nik_karyawan" 
                               id="nik_karyawan" 
                               value="{{ old('nik_karyawan') }}"
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm font-bold text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm" 
                               placeholder="Masukkan NIK Operator yang bertanggung jawab..." 
                               required>
                    </div>

                    {{-- 3. KATEGORI OUT (OUT CATEGORY ENUM) --}}
                    <div>
                        <label for="out_category" class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Out Category / Kategori Pengeluaran <span class="text-rose-500">*</span>
                        </label>
                        <select name="out_category" id="out_category" onchange="evaluatePhotoRequirement(this.value)" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm font-bold text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm" required>
                            <option value="" selected disabled>-- Pilih Kategori Kondisi --</option>
                            <option value="broken" {{ old('out_category') == 'broken' ? 'selected' : '' }}>Broken (Barang Rusak / Patah / Malfungsi fisik)</option>
                            <option value="lost" {{ old('out_category') == 'lost' ? 'selected' : '' }}>Lost (Menghilangkan / Barang Hilang dari Lantai Produksi)</option>
                        </select>
                    </div>

                    {{-- 4. UPLOAD FOTO BUKTI FISIK (KONDISIONAL VIA JS) --}}
                    <div id="photo-upload-wrapper" class="p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 transition-all duration-300">
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                            Upload Photo Proof / Foto Bukti Kondisi Fisik <span id="star-required" class="text-rose-500 hidden">*</span>
                        </label>
                        <p id="photo-instruction-text" class="text-[11px] font-bold text-slate-400 dark:text-slate-500 mb-3">
                            Silakan tentukan kategori pengeluaran terlebih dahulu untuk memvalidasi lampiran berkas.
                        </p>
                        
                        <input type="file" 
                               name="photo_path" 
                               id="photo_path" 
                               accept="image/*" 
                               class="block w-full text-xs font-bold text-slate-500 dark:text-slate-400
                                      file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                                      file:text-xs file:font-black file:uppercase file:tracking-wide
                                      file:bg-blue-50 file:text-blue-700 file:cursor-pointer
                                      dark:file:bg-slate-800 dark:file:text-blue-400
                                      hover:file:bg-blue-100 transition-all">
                    </div>

                    {{-- 5. REMARK / CATATAN TAMBAHAN KRONOLOGI --}}
                    <div>
                        <label for="remark" class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Remark / Keterangan Kronologi
                        </label>
                        <textarea name="remark" id="remark" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm font-bold text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm" placeholder="Tulis catatan detail alasan kerusakan atau hilangnya komponen barang..."></option>{{ old('remark') }}</textarea>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center h-11 rounded-xl bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 text-xs font-black text-white tracking-wider uppercase shadow-md hover:opacity-95 transition-all active:scale-[0.98]">
                            <i class="fa-solid fa-cloud-arrow-up mr-2 text-sm"></i> Save Manual Out Transaction
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

{{-- CLIENT-SIDE DYNAMIC VALIDATION ENGINE --}}
<script>
    function evaluatePhotoRequirement(selectedValue) {
        const wrapper = document.getElementById('photo-upload-wrapper');
        const star = document.getElementById('star-required');
        const text = document.getElementById('photo-instruction-text');
        const photoInput = document.getElementById('photo_path');

        if (selectedValue === 'lost') {
            // Jika HILANG (lost): Foto bersifat opsional/tidak wajib karena fisik barang tidak ada
            wrapper.classList.remove('border-amber-400', 'bg-amber-50/20', 'dark:border-amber-900/40');
            star.classList.add('hidden');
            photoInput.required = false;
            text.innerHTML = '<span class="text-emerald-600 dark:text-emerald-400 font-extrabold font-mono uppercase">[FOTO OPSIONAL] Komponen dilaporkan hilang. Data bisa langsung disimpan tanpa lampiran bukti foto fisik.</span>';
        } else if (selectedValue === 'broken') {
            // Jika RUSAK (broken): Foto mutlak WAJIB dilampirkan ke sistem
            wrapper.classList.add('border-amber-400', 'bg-amber-50/20', 'dark:border-amber-900/40');
            star.classList.remove('hidden');
            photoInput.required = true;
            text.innerHTML = '<span class="text-rose-600 dark:text-rose-400 font-extrabold font-mono uppercase">[FOTO WAJIB] Komponen dalam kondisi rusak fisik. Anda wajib mengambil foto bukti fisik barang!</span>';
        }
    }

    // Eksekusi ulang saat halaman reload/validation error agar form state tidak ter-reset gantung
    document.addEventListener("DOMContentLoaded", function() {
        const currentCategory = document.getElementById('out_category').value;
        if(currentCategory) {
            evaluatePhotoRequirement(currentCategory);
        }
    });
</script>
@endsection