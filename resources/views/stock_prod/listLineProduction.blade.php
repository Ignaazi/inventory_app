@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">

    <!-- Alert Total Data Terdaftar -->
    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 shadow-sm transition-all dark:bg-emerald-950/20 dark:border-emerald-900/50">
        <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-sm font-medium text-emerald-800 dark:text-emerald-400">
            <span class="uppercase font-bold mr-1">MASTER DATA:</span> 
            Total {{ $lines->total() }} production lines registered in tracking database.
        </p>
    </div>

    <!-- Header & Tombol Tambah Data -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Master Data Line Productions</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Production Line Verification & Scanner Security</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button onclick="openModal('add')" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                ADD PRODUCTION LINE
            </button>
        </div>
    </div>

    <!-- Kontainer Tabel Data -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-boxdark overflow-hidden">
        
        <!-- Input Pencarian -->
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <div class="relative w-full max-w-md">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <form action="{{ url()->current() }}" method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search line id, no line, or machine..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800 border-slate-600 dark:text-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500">
                </form>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="max-w-full overflow-x-auto scrollbar-hide">
            <table class="w-full text-left border-collapse" id="lineProductionTable">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-3 py-4 text-center w-12">NO</th>
                        <th class="px-4 py-4 text-center w-36">Line ID (Scan Key)</th>
                        <th class="px-4 py-4 text-center w-28">No Line</th>
                        <th class="px-4 py-4 text-center w-40">Name Machine</th>
                        <th class="px-4 py-4 text-center w-28">Create At</th>
                        <th class="px-4 py-4 text-center w-28">Update At</th>
                        <th class="px-6 py-4 text-center w-36">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-900 dark:text-white divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($lines as $index => $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all">
                        <td class="px-3 py-4 text-center">{{ $lines->firstItem() + $index }}</td>
                        
                        <!-- Line ID (Key Scanner) -->
                        <td class="px-4 py-4 text-center font-mono font-bold text-indigo-600 dark:text-indigo-400">
                            <span class="bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-md">{{ $item->line_id }}</span>
                        </td>
                        
                        <!-- No Line -->
                        <td class="px-4 py-4 font-bold text-center uppercase">{{ $item->no_line }}</td>
                        
                        <!-- Name Machine -->
                        <td class="px-4 py-4 text-center font-medium text-slate-700 dark:text-slate-300 uppercase">{{ $item->name_machine }}</td>
                        
                        <!-- Create At -->
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-[11px] text-slate-600 dark:text-slate-300 leading-normal text-center">
                            {{ $item->created_at->format('d/m/y') }}
                            <br><span class="text-[9px] text-slate-400 font-medium">{{ $item->created_at->format('H:i') }} WIB</span>
                        </td>
                        
                        <!-- Update At -->
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-[11px] text-slate-600 dark:text-slate-300 leading-normal text-center">
                            {{ $item->updated_at->format('d/m/y') }}
                            <br><span class="text-[9px] text-slate-400 font-medium">{{ $item->updated_at->format('H:i') }} WIB</span>
                        </td>
                        
                        <!-- Action Buttons -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- ACTION 1: EDIT (YELLOW) --}}
                                <button onclick="openModal('edit', {{ json_encode($item) }})" 
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-white transition-all hover:bg-yellow-500 active:scale-90 shadow-sm" 
                                    title="Edit Line">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                        
                                {{-- ACTION 2: DELETE (RED) --}}
                                <form action="{{ route('prod.line.destroy', $item->id) }}" method="POST" class="inline form-delete">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white btn-delete" title="Delete Line">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-12 text-center text-slate-400 italic">No production line data registered.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 px-6 py-4">
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                Showing {{ $lines->firstItem() ?? 0 }} to {{ $lines->lastItem() ?? 0 }} of {{ $lines->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center gap-2">
                {{ $lines->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL INPUT FORM (ADD & EDIT) --}}
<div id="modalLineProd" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800 dark:text-white">Add Production Line</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="lineProdForm" method="POST" class="p-6">
            @csrf
            <div id="methodField"></div>
            <div class="space-y-4">
                
                <!-- Input Line ID otomatis: tersembunyi saat ADD, muncul READ ONLY saat EDIT -->
                <div id="lineIdContainer" class="hidden">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Line ID (Scanner Key - Read Only)</label>
                    <input type="text" id="line_id" class="w-full rounded-lg border border-slate-200 bg-slate-100 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none dark:text-slate-400 font-mono font-bold" readonly>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">No Line</label>
                    <input type="text" name="no_line" id="no_line" placeholder="e.g., LINE 01" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white uppercase" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Name Machine</label>
                    <input type="text" name="name_machine" id="name_machine" placeholder="e.g., YAMAHA YSM20R" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white uppercase" required>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all">Save Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(mode, data = null) {
        const modal = document.getElementById('modalLineProd');
        const form = document.getElementById('lineProdForm');
        const methodField = document.getElementById('methodField');
        const lineIdContainer = document.getElementById('lineIdContainer');
        
        modal.classList.remove('hidden');
        
        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = 'Edit Production Line';
            form.action = "/prod/list-line-production/" + data.id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            // Tampilkan field Line ID dengan status Read Only
            lineIdContainer.classList.remove('hidden');
            document.getElementById('line_id').value = data.line_id;
            
            document.getElementById('no_line').value = data.no_line;
            document.getElementById('name_machine').value = data.name_machine;
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Production Line';
            form.action = "{{ route('prod.line.store') }}";
            form.reset();
            methodField.innerHTML = '';
            
            // Sembunyikan field Line ID karena digenerate otomatis oleh backend
            lineIdContainer.classList.add('hidden');
        }
    }

    function closeModal() { document.getElementById('modalLineProd').classList.add('hidden'); }

    // SweetAlert2 Konfirmasi Hapus Data
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            let form = this.closest('.form-delete');
            Swal.fire({
                title: 'Yakin mau hapus Line?',
                text: "Data line yang dihapus akan memengaruhi validasi keamanan scan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) { form.submit(); }
            });
        });
    });

    // SweetAlert2 Konfirmasi Simpan / Update
    document.getElementById('lineProdForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let form = this;
        let method = document.getElementById('methodField').innerHTML;
        let isEdit = method.includes('PUT');

        Swal.fire({
            title: isEdit ? 'Simpan Perubahan Data Line?' : 'Tambahkan Line Baru?',
            text: "Pastikan parameter data sudah sesuai untuk kebutuhan scanner.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Cek Lagi'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });
                form.submit();
            }
        });
    });

    // Alert Flash Session Sukses
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", timer: 2500, showConfirmButton: false });
    @endif

    // Alert Flash Error Validasi
    @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Validation Error', text: "{{ $errors->first() }}" });
    @endif
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .swal2-container { z-index: 10000 !important; }
</style>
@endsection