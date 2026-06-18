@extends('admin')

@section('content')
{{-- 1. LOAD GOOGLE FONTS NUNITO --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- 2. TAMBAH KELAS FONT-NUNITO DI CONTAINER UTAMA --}}
<div class="font-nunito mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">

    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 shadow-sm transition-all dark:bg-emerald-950/20 dark:border-emerald-900/50">
        <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-400">
            <span class="uppercase font-extrabold mr-1">MASTER DATA:</span> 
            Total {{ $spareparts->total() }} sparepart items registered in engineering database.
        </p>
    </div>

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Master Data Spareparts</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Engineering Specification Database</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('list-sparepart.export') }}" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
                EXCEL EXPORT
            </a>
            
            <button onclick="openModal('add')" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                ADD SPAREPART
            </button>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-boxdark overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <div class="relative w-full max-w-md">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <form action="{{ url()->current() }}" method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search data..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800 border-slate-600 dark:text-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500 font-medium">
                </form>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto scrollbar-hide">
            <table class="w-full text-left border-collapse" id="sparepartTable">
                <thead>
                    <tr class="text-[10px] font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-3 py-4 text-center w-12">NO</th>
                        <th class="px-4 py-4 text-center w-12">No Nozzle</th>
                        <th class="px-4 py-4 text-center w-20">Image</th>
                        <th class="px-4 py-4 text-center w-24">Category</th>
                        <th class="px-3 py-4 text-center w-20">Length</th>
                        <th class="px-3 py-4 text-center w-20">Width</th>
                        <th class="px-3 py-4 text-center w-20">Thickness</th>
                        <th class="px-4 py-4 text-center w-28">Create At</th>
                        <th class="px-4 py-4 text-center w-28">Update At</th>
                        <th class="px-6 py-4 text-center w-36">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-semibold text-slate-900 dark:text-white divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($spareparts as $index => $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all">
                        <td class="px-3 py-4 text-center font-bold text-slate-500">{{ $spareparts->firstItem() + $index }}</td>
                        <td class="px-4 py-4 font-bold max-w-[200px] truncate text-center text-slate-800 dark:text-white" title="{{ $item->name }}">{{ $item->name }}</td>
                        
                        <td class="px-4 py-2">
                            <div class="flex justify-center">
                                <div class="w-14 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-[8px] text-slate-400 font-extrabold">NO PIC</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center"><span class="bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-md font-extrabold text-[10px]">{{ $item->category }}</span></td>
                        <td class="px-3 py-4 text-center font-extrabold text-indigo-600 dark:text-indigo-400">{{ $item->length ?? '-' }}</td>
                        <td class="px-3 py-4 text-center font-extrabold text-amber-600 dark:text-amber-400">{{ $item->width ?? '-' }}</td>
                        <td class="px-3 py-4 text-center font-extrabold text-emerald-600 dark:text-emerald-400">{{ $item->thickness ?? '-' }}</td>
                        
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-[11px] text-slate-600 dark:text-slate-300 leading-normal text-center">
                            {{ $item->created_at->format('d/m/y') }}
                            <br><span class="text-[9px] text-slate-400 font-medium">{{ $item->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-[11px] text-slate-600 dark:text-slate-300 leading-normal text-center">
                            {{ $item->updated_at->format('d/m/y') }}
                            <br><span class="text-[9px] text-slate-400 font-medium">{{ $item->updated_at->format('H:i') }} WIB</span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- ACTION 1: PREVIEW IMAGE (BLUE) --}}
                                <button onclick="previewImage('{{ $item->image ? asset('storage/' . $item->image) : '' }}', '{{ $item->name }}')" 
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500 text-white transition-all hover:bg-blue-600 active:scale-90 shadow-sm" 
                                    title="Preview Image">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
            
                                {{-- ACTION 2: EDIT (YELLOW) --}}
                                <button onclick="openModal('edit', {{ json_encode($item) }})" 
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-white transition-all hover:bg-yellow-500 active:scale-90 shadow-sm" 
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                        
                                {{-- ACTION 3: DELETE (RED) --}}
                                <form action="{{ route('list-sparepart.destroy', $item->id) }}" method="POST" class="inline form-delete">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white btn-delete" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="py-12 text-center text-slate-400 italic font-semibold">Data not found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 px-6 py-4">
            <p class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                Showing {{ $spareparts->firstItem() }} to {{ $spareparts->lastItem() }} of {{ $spareparts->total() }} Entries
            </p>
            <div class="flex items-center gap-2">
                {{ $spareparts->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL FORM SPAREPART --}}
<div id="modalSparepart" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800 dark:text-white">Add Sparepart</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="sparepartForm" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div id="methodField"></div>
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-3">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Sparepart Name</label>
                    <input type="text" name="name" id="name" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-semibold" required>
                </div>
                <div class="col-span-3">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Category</label>
                    <select name="category" id="category" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-semibold" required>
                        <option value="NOZZLE">NOZZLE</option>
                        <option value="FEEDER">FEEDER</option>
                        <option value="MOTOR">MOTOR</option>
                        <option value="OTHER">OTHER</option>
                    </select>
                </div>
                
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Length</label>
                    <input type="number" step="0.01" name="length" id="length" placeholder="0.00" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Width</label>
                    <input type="number" step="0.01" name="width" id="width" placeholder="0.00" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Thickness</label>
                    <input type="number" step="0.01" name="thickness" id="thickness" placeholder="0.00" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                </div>

                <div class="col-span-3 mt-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Photo Upload</label>
                    <input type="file" name="image" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-800 dark:file:text-slate-200 hover:file:bg-slate-200">
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all tracking-wide">Save Data</button>
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
                confirmButtonColor: '#3b82f6',
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
            confirmButtonColor: '#3b82f6',
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
            
            document.getElementById('name').value = data.name;
            document.getElementById('category').value = data.category;
            document.getElementById('length').value = data.length;
            document.getElementById('width').value = data.width;
            document.getElementById('thickness').value = data.thickness;
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Sparepart';
            form.action = "{{ route('list-sparepart.store') }}";
            form.reset();
            methodField.innerHTML = '';
        }
    }

    function closeModal() { document.getElementById('modalSparepart').classList.add('hidden'); }

    // Intercept Konfirmasi Delete SweetAlert2
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            let form = this.closest('.form-delete');
            Swal.fire({
                title: 'Yakin mau hapus?',
                text: "Data sparepart yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'font-nunito' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Intercept Konfirmasi Submit (Store/Update)
    document.getElementById('sparepartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let form = this;
        let method = document.getElementById('methodField').innerHTML;
        let isEdit = method.includes('PUT');

        Swal.fire({
            title: isEdit ? 'Yakin simpan perubahan?' : 'Yakin tambah data?',
            text: "Pastikan data spesifikasi dimensi sudah tepat",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Cek Lagi',
            customClass: { popup: 'font-nunito' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() },
                    customClass: { popup: 'font-nunito' }
                });
                form.submit();
            }
        });
    });

    // Flash Session Popups Bawaan
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'font-nunito' }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ $errors->first() }}",
            customClass: { popup: 'font-nunito' }
        });
    @endif
</script>

<style>
    /* 3. STYLE CONFIG GLOBAL UNTUK NUNITO FONT */
    .font-nunito { font-family: 'Nunito', sans-serif !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .swal2-container { z-index: 10000 !important; }
    
    /* Perfect Center Alignment Table & Gap Fix */
    #sparepartTable th, #sparepartTable td {
        vertical-align: middle !important;
        text-align: center !important;
    }
</style>
@endsection