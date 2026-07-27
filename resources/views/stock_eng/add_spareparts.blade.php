@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 md:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
        
        <!-- HEADER FORM -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-base md:text-lg font-bold text-slate-950 dark:text-white">Add New Sparepart</h2>
                <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">Register a new engineering sparepart item, specifications, and physical image.</p>
            </div>
            
            {{-- Tombol Kembali - BIRU GRADIENT --}}
            <a href="{{ route('list-sparepart.index') }}" 
               class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95 tracking-wide uppercase no-underline">
                <i class="fas fa-arrow-left text-[10px]"></i> Kembali
            </a>
        </div>

        <!-- NOTIFIKASI ERROR VALIDASI -->
        @if ($errors->any())
            <div class="mb-5 p-4 text-xs md:text-sm text-red-800 rounded-lg bg-red-50 font-bold border border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM TAMBAH SPAREPART -->
        <form action="{{ route('list-sparepart.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-5">
                
                <!-- INPUT SPAREPART ID (Dahulu Name) -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Sparepart ID</label>
                    <input type="text" name="sparepart_id" value="{{ old('sparepart_id') }}" required placeholder="Enter sparepart id or name" 
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- DROPDOWN KATEGORI (HANYA NOZZLE) -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Category</label>
                    <div class="relative">
                        <select name="category" required class="w-full bg-white dark:bg-slate-950 border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="NOZZLE" selected>NOZZLE</option>
                        </select>
                    </div>
                </div>

                <!-- INPUT SAP CODE -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">SAP Code</label>
                    <input type="text" name="sap_code" value="{{ old('sap_code') }}" placeholder="Enter SAP code" 
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- INPUT PART NUMBER -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Part Number</label>
                    <input type="text" name="part_number" value="{{ old('part_number') }}" placeholder="Enter part number" 
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- INPUT DIMENSI (PANJANG, LEBAR, TEBAL) -->
                <div class="grid grid-cols-3 gap-3 md:col-span-2">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Length (mm)</label>
                        <input type="number" step="0.01" name="length" value="{{ old('length') }}" required placeholder="0.00" 
                               class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Width (mm)</label>
                        <input type="number" step="0.01" name="width" value="{{ old('width') }}" required placeholder="0.00" 
                               class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Thickness (mm)</label>
                        <input type="number" step="0.01" name="thickness" value="{{ old('thickness') }}" required placeholder="0.00" 
                               class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <!-- COMPONENT SPAREPART IMAGE -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300 flex items-center gap-1.5">
                        <i class="fas fa-image text-blue-500"></i> Sparepart Photo
                    </label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full">
                        <!-- Preview Box Image -->
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-gray-200 dark:border-gray-700 flex-shrink-0 flex items-center justify-center shadow-sm relative">
                            <img id="sparepart-preview" 
                                 src="https://cdn-icons-png.flaticon.com/512/4115/4115624.png" 
                                 alt="Sparepart Preview" class="max-w-full max-h-full object-contain p-2 hidden">
                            
                            <div id="sparepart-placeholder" class="text-[10px] text-gray-400 text-center font-medium p-2">
                                <i class="fas fa-box-open text-xl block mb-1 text-slate-400"></i> No Image
                            </div>
                        </div>
                        
                        <!-- Input File Custom Outline -->
                        <div class="w-full flex-1 flex flex-col gap-1.5">
                            <input type="file" name="image" id="sparepart-input" accept="image/*"
                                   class="block w-full text-[11px] text-slate-500 bg-white dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer focus:outline-none
                                          file:mr-3 file:py-2.5 file:px-4 file:rounded-l-md file:border-0 file:text-[11px] file:font-bold 
                                          file:bg-slate-100 file:text-slate-800 dark:file:bg-slate-800 dark:file:text-white 
                                          file:shadow-[inset_0_1px_0_rgba(255,255,255,0.2)]
                                          file:border-r file:border-gray-300 dark:file:border-gray-700
                                          hover:file:bg-slate-200 dark:hover:file:bg-slate-700 transition-all" />
                            
                            <!-- Tombol Remove Photo -->
                            <button type="button" id="btn-remove-sparepart" class="hidden text-left text-[11px] font-bold text-red-600 hover:text-red-700 dark:text-red-400 w-max flex items-center gap-1 transition-all">
                                <i class="fas fa-trash-alt text-[10px]"></i> Cancel Selection
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BUTTON ACTIONS -->
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                {{-- Tombol Save Sparepart - BIRU GRADIENT --}}
                <button type="submit" class="w-full md:w-auto md:px-8 py-2.5 bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 text-white rounded-lg text-xs font-bold shadow-md hover:opacity-90 transition-all active:scale-95 tracking-wide uppercase">
                    Save Sparepart
                </button>
            </div>
        </form>
    </div>
</div>

<!-- LIVE PREVIEW JAVASCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sparepartInput = document.getElementById('sparepart-input');
        const sparepartPreview = document.getElementById('sparepart-preview');
        const sparepartPlaceholder = document.getElementById('sparepart-placeholder');
        const btnRemoveSparepart = document.getElementById('btn-remove-sparepart');

        sparepartInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    sparepartPlaceholder.classList.add('hidden');
                    sparepartPreview.src = e.target.result;
                    sparepartPreview.classList.remove('hidden');
                    btnRemoveSparepart.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        btnRemoveSparepart.addEventListener('click', function() {
            sparepartInput.value = ""; 
            sparepartPreview.src = ""; 
            sparepartPreview.classList.add('hidden'); 
            sparepartPlaceholder.classList.remove('hidden'); 
            btnRemoveSparepart.classList.add('hidden'); 
        });
    });
</script>
@endsection