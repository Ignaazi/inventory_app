@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Custom Styling SweetAlert2 agar harmonis dengan tema aplikasi */
    .swal2-popup {
        border-radius: 1rem !important;
        font-family: 'Nunito', sans-serif !important;
    }
    .dark .swal2-popup {
        background-color: #0f172a !important; /* slate-900 */
        border: 1px solid #1e293b !important;
    }
    .dark .swal2-title, .dark .swal2-html-container {
        color: #f8fafc !important;
    }
    .scrollbar-thin::-webkit-scrollbar {
        height: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #334155;
    }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Banner Top Alert Status Counter --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-emerald-800 dark:text-emerald-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">MASTER DATA:</span> 
            Total {{ $lines->total() }} production lines registered in tracking database.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight">Master Data Line Productions</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Production Line Verification & Scanner Security</p>
        </div>

        {{-- TOMBOL ADD PRODUCTION LINE GRADIENT --}}
        <div class="flex items-center w-full sm:w-auto">
            <button onclick="openModal('add')" 
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-opacity uppercase tracking-wider active:scale-95 transition-all font-nunito w-full sm:w-auto text-center cursor-pointer">
                <svg class="w-3.5 h-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Production Line
            </button>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        {{-- HEADER KONTROL RESPONSIF --}}
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
            <!-- Entries Selector -->
            <div class="flex items-center gap-2 text-xs md:text-[13px] font-black text-black dark:text-slate-300 order-2 sm:order-1">
                <span>Show</span>
                <select class="rounded-md border border-gray-300 dark:border-slate-700 bg-transparent px-2 py-1 outline-none text-black dark:text-white font-black cursor-pointer font-nunito text-xs">
                    <option value="10" class="dark:bg-slate-900">10</option>
                    <option value="25" class="dark:bg-slate-900">25</option>
                    <option value="50" class="dark:bg-slate-900">50</option>
                </select>
                <span>entries</span>
            </div>

            <!-- Search & Export Grid -->
            <div class="grid grid-cols-12 gap-2 w-full sm:w-auto order-1 sm:order-2">
                {{-- LIVE SEARCH INPUT --}}
                <div class="relative col-span-8 sm:w-60 sm:block">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" id="tableSearch" placeholder="Search line, machine, NIK..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-black dark:text-white font-bold font-nunito">
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('production-lines.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-black dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
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
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1200px]" id="lineProductionTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito">
                        <th class="px-2 py-3.5 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-blue-400 bg-transparent text-blue-600 focus:ring-blue-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-2 py-3.5 w-[60px] border-l border-blue-500 dark:border-blue-900/50">NO</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">NIK</th>
                        <th class="px-4 py-3.5 w-[200px] border-l border-blue-500 dark:border-blue-900/50 text-center">Line ID</th>
                        <th class="px-4 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50 text-center">No Line</th>
                        <th class="px-4 py-3.5 border-l border-blue-500 dark:border-blue-900/50 text-left w-[250px]">Name Machine</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-blue-500 dark:border-blue-900/50">Create At</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-blue-500 dark:border-blue-900/50">Update At</th>
                        <th class="px-4 py-3.5 w-[130px] border-l border-blue-500 dark:border-blue-900/50">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold text-black dark:text-slate-200 font-nunito bg-transparent">
                    @forelse($lines as $index => $item)
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            {{ $lines->firstItem() + $index }}
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 search-nik font-mono font-extrabold text-slate-600 dark:text-slate-300">
                            {{ $item->user->nik ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 search-lineid font-extrabold tracking-wide uppercase">
                            {{ $item->line_id }}
                        </td>

                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 search-noline uppercase font-black text-blue-600 dark:text-blue-400">
                            {{ $item->no_line }}
                        </td>

                        <td class="px-4 py-3.5 text-left border-l border-gray-100 dark:border-slate-800 search-machine font-semibold uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis" title="{{ $item->name_machine }}">
                            {{ $item->name_machine }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap">
                            {{ $item->updated_at->format('d/m/Y H:i') }}
                        </td>
                        
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-center gap-1.5 w-full">
                                {{-- ACTION 1: EDIT --}}
                                <button onclick="openModal('edit', {{ json_encode($item) }})" 
                                    type="button" class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-yellow-400 text-white hover:bg-yellow-500 active:scale-90 shadow-sm transition-all cursor-pointer" title="Edit Line">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                        
                                {{-- ACTION 2: DELETE --}}
                                <form action="{{ route('prod.line.destroy', $item->id) }}" method="POST" class="inline form-delete shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-7 w-7 items-center justify-center rounded bg-red-500 text-white btn-delete hover:bg-red-600 active:scale-90 transition-all cursor-pointer" title="Delete Line">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-10 text-center text-slate-400 italic font-medium text-[13px] font-nunito">No production line data registered.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black text-black dark:text-slate-400 tracking-wide uppercase font-nunito text-center sm:text-left">
                Showing {{ $lines->firstItem() ?? 0 }} to {{ $lines->lastItem() ?? 0 }} of {{ $lines->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito text-black dark:text-white w-full sm:w-auto">
                {{ $lines->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL INPUT FORM (ADD & EDIT) - STYLED THEME --}}
<div id="modalLineProd" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-xs px-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-gray-200 dark:border-slate-800 font-nunito transition-all transform scale-100">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900">
            <h3 id="modalTitle" class="text-base md:text-lg font-black text-black dark:text-white uppercase tracking-tight">Add Production Line</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round"/></svg>
            </button>
        </div>
        <form id="lineProdForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div id="methodField"></div>
            
            <!-- Field Line ID (Read Only saat EDIT) -->
            <div id="lineIdContainer" class="hidden">
                <label class="text-xs font-black text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wider">Line ID (Scanner Key - Read Only)</label>
                <input type="text" id="line_id" class="w-full rounded-lg border border-gray-200 bg-gray-50 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-xs md:text-sm outline-none text-slate-400 font-mono font-bold" readonly>
            </div>

            <div>
                <label class="text-xs font-black text-slate-700 dark:text-slate-300 mb-1 block uppercase tracking-wider">No Line</label>
                <input type="text" name="no_line" id="no_line" placeholder="e.g., LINE 01" class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent p-2.5 text-xs md:text-sm font-bold text-black dark:text-white outline-none focus:border-blue-500 uppercase font-nunito" required>
            </div>
            
            <div>
                <label class="text-xs font-black text-slate-700 dark:text-slate-300 mb-1 block uppercase tracking-wider">Name Machine</label>
                <input type="text" name="name_machine" id="name_machine" placeholder="e.g., YAMAHA YSM20R" class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent p-2.5 text-xs md:text-sm font-bold text-black dark:text-white outline-none focus:border-blue-500 uppercase font-nunito" required>
            </div>

            <div class="pt-4 flex justify-end gap-2.5 border-t border-gray-100 dark:border-slate-800">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-xs font-black text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 uppercase cursor-pointer">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-xs font-black shadow-md hover:bg-blue-700 active:scale-95 transition-all uppercase tracking-wider cursor-pointer">Save Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    // LIVE SEARCH
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('.table-row-item');
        
        rows.forEach(function(row) {
            let nik = row.querySelector('.search-nik').textContent.toLowerCase();
            let lineid = row.querySelector('.search-lineid').textContent.toLowerCase();
            let noline = row.querySelector('.search-noline').textContent.toLowerCase();
            let machine = row.querySelector('.search-machine').textContent.toLowerCase();
            
            if (nik.includes(value) || lineid.includes(value) || noline.includes(value) || machine.includes(value)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // SELECT ALL CHECKBOX
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // EXPORT TO CSV
    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#lineProductionTable tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 1; j < cols.length - 1; j++) { // Skip checkbox & action columns
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s+)/gm, " ");
                row.push('"' + data + '"');
            }
            if(row.length > 0) csv.push(row.join(","));
        }
        let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        let downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    // MODAL CONTROL
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
            
            lineIdContainer.classList.remove('hidden');
            document.getElementById('line_id').value = data.line_id;
            document.getElementById('no_line').value = data.no_line;
            document.getElementById('name_machine').value = data.name_machine;
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Production Line';
            form.action = "{{ route('prod.line.store') }}";
            form.reset();
            methodField.innerHTML = '';
            lineIdContainer.classList.add('hidden');
        }
    }

    function closeModal() { document.getElementById('modalLineProd').classList.add('hidden'); }

    // SWEETALERT2: DELETE CONFIRMATION
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "Data line yang dihapus akan memengaruhi validasi keamanan scan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                customClass: { 
                    popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' 
                }
            }).then((result) => {
                if (result.isConfirmed) { form.submit(); }
            });
        });
    });

    // SWEETALERT2: SUBMIT CONFIRMATION
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
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Cek Lagi',
            customClass: { 
                popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' 
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });
                form.submit();
            }
        });
    });

    // FLASH NOTIFICATION
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", timer: 2500, showConfirmButton: false });
    @endif

    @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Validation Error', text: "{{ $errors->first() }}" });
    @endif
</script>
@endsection