@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-popup {
        border-radius: 1rem !important;
        font-family: 'Nunito', sans-serif !important;
    }
    .dark .swal2-popup {
        background-color: #0f172a !important; 
        border: 1px solid #1e293b !important; 
    }
    .dark .swal2-title, .dark .swal2-html-container {
        color: #f8fafc !important; 
    }
</style>

{{-- MAIN CONTAINER - UKURAN & PADDING SAMA PERSIS DENGAN CONOH (p-1 md:p-2) --}}
<div class="font-nunito w-full p-1 md:p-2 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Banner Top Alert Status Counter --}}
    <div class="mb-3 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 px-3 py-2.5 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-emerald-800 dark:text-emerald-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">MASTER DATA:</span> 
            Total {{ $spareparts->total() }} sparepart items registered in engineering database.
        </p>
    </div>

    {{-- Header Section - Ukuran & Posisi Mengikuti Contoh --}}
    <div class="mb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 font-nunito px-1 pt-1">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">Master Data Spareparts</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Engineering Specification Database</p>
        </div>

        {{-- TOMBOL ADD NEW --}}
        <div class="flex items-center w-full sm:w-auto">
            <a href="{{ route('list-sparepart.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-opacity uppercase tracking-wider active:scale-95 transition-all font-nunito w-full sm:w-auto text-center cursor-pointer no-underline">
                <svg class="w-3.5 h-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Sparepart
            </a>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-3 shadow-sm">
        
        {{-- HEADER KONTROL RESPONSIF (SHOW ENTRIES AKTIF & SEARCH AKTIF) --}}
        <div class="mb-3 flex flex-col gap-3 px-3 sm:flex-row sm:items-center sm:justify-between font-nunito">
            
            <!-- Entries Controller (AKTIF via Submit Form) -->
            <div class="flex flex-wrap items-center gap-3 text-xs md:text-[13px] font-black text-slate-950 dark:text-slate-300 order-2 sm:order-1">
                <div class="flex items-center gap-1.5">
                    <span>Show</span>
                    <form action="{{ url()->current() }}" method="GET" id="entriesForm">
                        <select name="per_page" onchange="this.form.submit()" class="rounded-md border border-gray-300 dark:border-slate-700 bg-transparent px-2 py-1 outline-none text-slate-950 dark:text-white font-black cursor-pointer font-nunito text-xs">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }} class="dark:bg-slate-900">10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }} class="dark:bg-slate-900">25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }} class="dark:bg-slate-900">50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }} class="dark:bg-slate-900">100</option>
                        </select>
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                    </form>
                    <span>entries</span>
                </div>
            </div>

            <!-- Search & Export Grid (AKTIF via GET Request) -->
            <div class="grid grid-cols-12 gap-2 w-full sm:w-auto order-1 sm:order-2">
                {{-- LIVE SEARCH INPUT --}}
                <div class="relative col-span-8 sm:w-60 sm:block">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <form action="{{ url()->current() }}" method="GET" class="w-full">
                        @if(request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" id="tableSearch" placeholder="Search..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-slate-950 dark:text-white font-bold font-nunito">
                    </form>
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('spareparts-data.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
                    <span class="hidden sm:inline">Export CSV</span>
                    <span class="sm:hidden">CSV</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- AREA SCROLL HORIZONTAL --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1200px]" id="sparepartTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito table-header-row">
                        <th class="px-2 py-3.5 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-blue-400 bg-transparent text-blue-600 focus:ring-blue-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-2 py-3.5 w-[60px] border-l border-blue-500 bg-blue-700/30">NO</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-blue-500 bg-blue-700/30">SAP Code</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 bg-blue-700/30">Part Number</th>
                        <th class="px-4 py-3.5 border-l border-blue-500 bg-blue-700/30 text-center w-[200px]">Sparepart ID</th>
                        <th class="px-2 py-3.5 w-[90px] border-l border-blue-500 bg-blue-700/30">Image</th>
                        <th class="px-2 py-3.5 w-[120px] border-l border-blue-500 bg-blue-700/30">Category</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500 bg-blue-700/30">Length</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500 bg-blue-700/30">Width</th>
                        <th class="px-2 py-3.5 w-[95px] border-l border-blue-500 bg-blue-700/30">Thickness</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 bg-blue-700/30">Created At</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 bg-blue-700/30">Updated At</th>
                        <th class="px-4 py-3.5 w-[150px] border-l border-blue-500 bg-blue-700/30">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($spareparts as $index => $item)
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-slate-500">
                            {{ $spareparts->firstItem() + $index }}
                        </td>
                        
                        <td class="px-3 py-3.5 font-mono border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ $item->sap_code ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ $item->part_number ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide whitespace-normal break-words leading-normal" title="{{ $item->sparepart_id }}">
                            {{ $item->sparepart_id }}
                        </td>

                        <td class="px-2 py-2 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex justify-center items-center">
                                <div class="w-14 h-8 rounded bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm cursor-zoom-in" 
                                     onclick="previewImage('{{ $item->image ? asset('storage/' . $item->image) : '' }}', '{{ $item->sparepart_id }}')">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-[9px] text-slate-400 font-bold uppercase">NO PIC</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex justify-center items-center">
                                <span class="bg-slate-100 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">{{ $item->category }}</span>
                            </div>
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">{{ $item->length ?? '-' }}</td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">{{ $item->width ?? '-' }}</td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">{{ $item->thickness ?? '-' }}</td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-slate-600 dark:text-slate-400">
                            {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-slate-600 dark:text-slate-400">
                            {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-center gap-1.5 w-full">
                                <button onclick="previewImage('{{ $item->image ? asset('storage/' . $item->image) : '' }}', '{{ $item->sparepart_id }}')" 
                                    type="button" class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-blue-500 text-white hover:bg-blue-600 active:scale-90 shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </button>
            
                                <button onclick="openModal('edit', {{ json_encode($item) }})" 
                                   class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-yellow-400 text-white hover:bg-yellow-500 active:scale-90 shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                        
                                <form action="{{ route('list-sparepart.destroy', $item->id) }}" method="POST" class="inline form-delete shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-7 w-7 items-center justify-center rounded bg-red-500 text-white btn-delete hover:bg-red-600 active:scale-90 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="13" class="py-10 text-center text-slate-400 italic font-medium text-[13px] font-nunito">No entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF DENGAN KOTAK PRESISI 34x34px WARNA BIRU --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-3.5 font-nunito">
            <p class="text-[11px] font-black tracking-wide uppercase font-nunito text-center sm:text-left text-slate-900 dark:text-slate-300">
                Showing {{ $spareparts->firstItem() ?? 0 }} to {{ $spareparts->lastItem() ?? 0 }} of {{ $spareparts->total() }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito w-full sm:w-auto custom-pagination">
                {{ $spareparts->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT DATA --}}
<div id="modalSparepart" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800 transition-all transform scale-100">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900">
            <h3 id="modalTitle" class="text-lg font-extrabold text-black dark:text-white tracking-tight">Edit Sparepart</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <form id="sparepartForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div id="methodField"></div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">SAP Code</label>
                    <input type="text" name="sap_code" id="sap_code" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-semibold">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Part Number</label>
                    <input type="text" name="part_number" id="part_number" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-3">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Sparepart ID / Name</label>
                    <input type="text" name="sparepart_id" id="sparepart_id" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-semibold" required>
                </div>
                <div class="col-span-3">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Category</label>
                    <select name="category" id="category" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-semibold" required>
                        <option value="NOZZLE">NOZZLE</option>
                        <option value="FEEDER">FEEDER</option>
                        <option value="MOTOR">MOTOR</option>
                        <option value="OTHER">OTHER</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Length (mm)</label>
                    <input type="number" step="0.01" name="length" id="length" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Width (mm)</label>
                    <input type="number" step="0.01" name="width" id="width" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Thickness (mm)</label>
                    <input type="number" step="0.01" name="thickness" id="thickness" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-bold" required>
                </div>
                <div class="col-span-3">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Photo Upload</label>
                    <input type="file" name="image" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 dark:hover:file:bg-slate-700 transition-all cursor-pointer">
                </div>
            </div>
            
            <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Cancel</button>
                <button type="submit" class="bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg hover:opacity-90 transition-all active:scale-95 tracking-wide">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(imageUrl, itemName) {
        if (!imageUrl) {
            Swal.fire({
                icon: 'info',
                title: 'No Image Available',
                text: 'Sparepart ini belum memiliki foto spesifikasi.',
                confirmButtonColor: '#2563eb',
                customClass: { popup: 'font-nunito' }
            });
            return;
        }
        Swal.fire({
            title: itemName,
            text: 'Specification Image Preview',
            imageUrl: imageUrl,
            imageWidth: 450,
            imageHeight: 300,
            imageAlt: itemName,
            animation: true,
            showCloseButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Close Preview',
            customClass: { popup: 'font-nunito' }
        });
    }

    function openModal(mode, data = null) {
        const modal = document.getElementById('modalSparepart');
        const form = document.getElementById('sparepartForm');
        const methodField = document.getElementById('methodField');
        
        modal.classList.remove('hidden');
        
        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = 'Edit Sparepart Data';
            form.action = "/eng/list-sparepart/" + data.id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            document.getElementById('sap_code').value = data.sap_code ?? '';
            document.getElementById('part_number').value = data.part_number ?? '';
            document.getElementById('sparepart_id').value = data.sparepart_id;
            document.getElementById('category').value = data.category;
            document.getElementById('length').value = data.length;
            document.getElementById('width').value = data.width;
            document.getElementById('thickness').value = data.thickness;
        }
    }

    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    function closeModal() { document.getElementById('modalSparepart').classList.add('hidden'); }

    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#sparepartTable tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 1; j < cols.length; j++) {
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s+)/gm, " ");
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }
        let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        let downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    // Intercept Delete
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            let form = this.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this sparepart entry!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                customClass: { popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' }
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });

    // Intercept Edit Submit
    document.getElementById('sparepartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: 'Yakin simpan perubahan?',
            text: "Pastikan data spesifikasi dimensi sudah tepat",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Proses!',
            customClass: { popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' }
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false, customClass: { popup: 'font-nunito' } });
    @endif
</script>

<style>
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container, #sparepartTable { 
        font-family: 'Nunito', sans-serif !important; 
    }

    .table-body-data tr td, 
    .table-body-data tr td div {
        color: #000000 !important;
    }

    .dark .table-body-data tr td, 
    .dark .table-body-data tr td div {
        color: #f1f5f9 !important;
    }

    .table-header-row th {
        color: #ffffff !important;
    }
    
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #2563eb; border-radius: 3px; }
    
    #sparepartTable td, #sparepartTable th {
        vertical-align: middle !important;
    }

    /* ========================================================= */
    /* FIX PAGINATION: SIMPLE, SINGLE BOX, EQUAL SIZE (34x34px) - TEMA BIRU */
    /* ========================================================= */
    .custom-pagination nav {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: none !important;
    }
    
    /* Sembunyikan elemen pembungkus bawaan Laravel */
    .custom-pagination nav div:first-child,
    .custom-pagination nav p { 
        display: none !important; 
    }

    /* RESET SEMUA PEMBUNGKUS LUAR */
    .custom-pagination nav span.relative.z-0,
    .custom-pagination nav span[aria-disabled="true"],
    .custom-pagination nav span[aria-current="page"] {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        display: inline-flex !important;
    }

    /* UKURAN UTAMA TOMBOL (PRESISI 34px x 34px SAMA RATA) */
    .custom-pagination nav a, 
    .custom-pagination nav span[aria-current="page"] > span,
    .custom-pagination nav span[aria-disabled="true"] > span {
        width: 34px !important;
        height: 34px !important;
        min-width: 34px !important;
        min-height: 34px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        padding: 0 !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease-in-out !important;
    }

    /* 1. TOMBOL BIASA & PANAH INAKTIF (TEMA BIRU) */
    .custom-pagination nav a {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #dbeafe !important;
    }

    .custom-pagination nav a:hover {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
        transform: translateY(-1px);
    }

    /* 2. TOMBOL AKTIF (HALAMAN SAAT INI - BIRU SOLID) */
    .custom-pagination nav span[aria-current="page"] > span {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: #ffffff !important;
        border: 1px solid #2563eb !important;
        box-shadow: 0 2px 5px rgba(37, 99, 235, 0.3) !important;
    }

    /* 3. TOMBOL PANAH MATI / DISABLED */
    .custom-pagination nav span[aria-disabled="true"] > span {
        background-color: #f8fafc !important;
        color: #cbd5e1 !important;
        border: 1px solid #e2e8f0 !important;
        cursor: not-allowed !important;
        opacity: 0.7;
    }

    /* DARK MODE STYLING PAGINATION */
    .dark .custom-pagination nav a {
        background-color: #1e293b !important;
        color: #93c5fd !important;
        border-color: #1e3a8a !important;
    }

    .dark .custom-pagination nav a:hover {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }

    .dark .custom-pagination nav span[aria-disabled="true"] > span {
        background-color: #0f172a !important;
        color: #475569 !important;
        border-color: #1e293b !important;
    }

    /* Rapikan Ikon Panah SVG */
    .custom-pagination nav svg { 
        width: 14px !important; 
        height: 14px !important; 
        display: block !important; 
        margin: auto !important;
    }
</style>
@endsection