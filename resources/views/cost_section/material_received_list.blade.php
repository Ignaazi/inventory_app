@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6">
    
    {{-- ALERT NOTIFIKASI SYSTEM --}}
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-900 text-green-800 dark:text-green-400 shadow-sm flex items-center gap-3">
        <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
        <div class="text-xs font-bold uppercase tracking-wide flex items-center gap-2">
            <span>SYSTEM NOTIFICATION:</span>
            <span class="font-bold text-slate-900 dark:text-white normal-case">{{ session('success') }}</span>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-400 shadow-sm flex items-center gap-3">
        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
        <div class="text-xs font-bold uppercase tracking-wide flex items-center gap-2">
            <span>SYSTEM ERROR:</span>
            <span class="font-bold text-slate-900 dark:text-white normal-case">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                List Material Received Tracker
            </h2>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS INDONESIA</p>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 italic">
            *Untuk membuat MR baru, silakan masuk ke halaman List PR Engineering lalu klik proses cicilan barang.
        </p>
    </div>

    {{-- CONTROLLER PANEL: SEARCH & FILTER STATUS --}}
    <div class="bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark rounded-xl shadow-sm overflow-hidden mb-6 p-4 bg-slate-50/50 dark:bg-slate-800/40">
        <div class="flex flex-col lg:flex-row gap-4 justify-between items-stretch lg:items-center">
            
            {{-- FORM PENCARIAN (SEARCH HITS KE CONTROLLER) --}}
            <form action="{{ route('costing.material.list') }}" method="GET" class="flex items-center gap-2 w-full lg:max-w-md">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari No MR, No PR, NIK, atau Nama..." 
                           class="w-full rounded-xl border border-slate-200 bg-white dark:bg-slate-800 py-2 pl-4 pr-10 text-xs font-medium text-slate-700 dark:text-slate-300 outline-none focus:border-primary">
                    @if($search)
                        <a href="{{ route('costing.material.list') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                    @endif
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold uppercase py-2 px-4 rounded-xl shadow transition-all">
                    Cari
                </button>
            </form>

            {{-- LIVE JAVASCRIPT FILTER BADGE TAB --}}
            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-inner self-start lg:self-center">
                <button type="button" onclick="filterMaterialTable('all', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white">All Data</button>
                <button type="button" onclick="filterMaterialTable('pending', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500">Pending Staff</button>
                <button type="button" onclick="filterMaterialTable('checked', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500">Pending SPV</button>
                <button type="button" onclick="filterMaterialTable('approved', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500">Approved</button>
            </div>
        </div>
    </div>

    {{-- TABEL DATA UTAMA --}}
    <div class="bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark rounded-xl shadow-sm overflow-hidden">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-xs text-left border-collapse font-sans">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200 dark:border-strokedark">
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-4">MR Number</th>
                        <th class="py-3 px-4">PR Reference</th>
                        <th class="py-3 px-4">Item Sparepart</th>
                        <th class="py-3 px-4 text-center">Qty Received</th>
                        <th class="py-3 px-4 text-center">Workflow Status</th>
                        <th class="py-3 px-4 text-center">1. Costing</th>
                        <th class="py-3 px-4 text-center">2. Eng Staff</th> 
                        <th class="py-3 px-4 text-center">3. Admin/SPV</th>
                        <th class="py-3 px-4">Created Date</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-strokedark text-slate-900 dark:text-white font-bold">
                    @forelse($materialReceived as $index => $mr)
                    <tr class="material-row-item hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors whitespace-nowrap">
                        <td class="py-3.5 px-3 text-center text-slate-500 font-normal">{{ $materialReceived->firstItem() + $index }}</td>
                        <td class="py-3.5 px-4 tracking-tight font-mono text-blue-600 dark:text-blue-400">{{ $mr->no_mr ?? 'MR-SYSTEM-GEN' }}</td>
                        <td class="py-3.5 px-4 tracking-tight font-mono text-slate-600 dark:text-slate-400">{{ $mr->purchaseRequest->no_pr ?? '-' }}</td>
                        <td class="py-3.5 px-4 font-medium max-w-xs truncate">{{ $mr->purchaseRequest->sparepart->name ?? '-' }}</td>
                        <td class="py-3.5 px-3 text-center text-slate-900 dark:text-white">{{ number_format($mr->qty_received) }} <span class="text-[10px] text-slate-400 font-normal">Pcs</span></td>
                        
                        {{-- STATUS BADGE FLOW --}}
                        <td class="py-3.5 px-4 text-center">
                            <span class="status-badge inline-flex items-center rounded-md px-2 py-0.5 text-[10px] uppercase tracking-wide border 
                                {{ $mr->status === 'approved' ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900' : 
                                  ($mr->status === 'checked' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900' : 
                                                              'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900') }}">
                                {{ $mr->status }}
                            </span>
                        </td>

                        {{-- INDIKATOR SIGNATURE BERJENJANG --}}
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center gap-1 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-900 text-[9px] py-0.5 px-2 rounded font-bold">✓ Prepared</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            {!! $mr->checked_signature ? '<span class="text-green-700 dark:text-green-400 text-[9px] bg-green-50 dark:bg-green-950/20 px-2 py-0.5 rounded border border-green-200 dark:border-green-900">✓ Checked</span>' : '<span class="text-slate-400 dark:text-slate-500 italic text-[9px] font-normal">⚪ Waiting</span>' !!}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            {!! $mr->approved_signature ? '<span class="text-emerald-700 dark:text-emerald-400 text-[9px] bg-emerald-50 dark:bg-emerald-950/20 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-900">🔒 Approved</span>' : '<span class="text-slate-400 dark:text-slate-500 italic text-[9px] font-normal">⚪ Waiting</span>' !!}
                        </td>
                        
                        {{-- DATA TANGGAL PEMBUATAN --}}
                        <td class="py-3.5 px-4 font-normal text-slate-600 dark:text-slate-400">
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $mr->created_at ? $mr->created_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $mr->created_at ? $mr->created_at->format('H:i') . ' WIB' : '' }}</div>
                        </td>
                        
                        {{-- ACTION BUTTONS --}}
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- BUTTON AJAX PREVIEW VIEW --}}
                                <button type="button" onclick="previewJsonDocument('{{ $mr->id }}')" class="w-7 h-7 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-all active:scale-90" title="Quick Preview Data">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                {{-- BUTTON REMOVE RECORD --}}
                                <button type="button" onclick="confirmDeleteMaterial('{{ $mr->id }}', '{{ $mr->no_mr }}')" class="w-7 h-7 inline-flex items-center justify-center bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow transition-all active:scale-90" title="Delete Entry Permanen">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                {{-- FORM HIDDEN SUBMIT METHOD DELETE --}}
                                <form id="delete-form-{{ $mr->id }}" action="{{ route('costing.material.delete', $mr->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-12 text-slate-400 dark:text-slate-500 italic font-medium">
                            Tidak ada arsip dokumen pengiriman material yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- BLADE PAGINATION SYSTEM LINK (PRESERVE SEARCH PARAMETER) --}}
        <div class="p-4 border-t border-slate-100 dark:border-strokedark">
            {{ $materialReceived->appends(['search' => $search])->links() }}
        </div>
    </div>
</div>

{{-- SCRIPT INTERAKSI DOKUMEN --}}
<script>
function confirmDeleteMaterial(id, mrCode) {
    Swal.fire({
        title: 'Hapus Record MR ini?',
        text: "Menghapus nomor MR " + mrCode + " akan mengembalikan sisa kuota cicilan PR semula dan menghapus seluruh file tanda tangan digital di server!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus Permanen!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

function previewJsonDocument(id) {
    // Memanggil route preview JSON bawaan controller Lu (method show)
    fetch(`/material-received/preview/${id}`)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: `<span class="text-sm font-bold font-mono text-blue-600">${data.no_mr}</span>`,
                html: `
                    <div class="text-left text-xs space-y-2 font-sans pt-3">
                        <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200">
                            <span class="font-bold text-slate-500 block uppercase text-[9px]">Nama Item:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">${data.purchase_request?.sparepart?.name || '-'}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200">
                                <span class="font-bold text-slate-500 block uppercase text-[9px]">Qty Diterima:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">${data.qty_received} Pcs</span>
                            </div>
                            <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200">
                                <span class="font-bold text-slate-500 block uppercase text-[9px]">Status Alur:</span>
                                <span class="font-bold uppercase text-blue-600">${data.status}</span>
                            </div>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded border border-slate-200">
                            <span class="font-bold text-slate-500 block uppercase text-[9px]">Catatan Remarks:</span>
                            <p class="text-slate-700 dark:text-slate-300 italic">${data.remark || 'Tidak ada catatan khusus.'}</p>
                        </div>
                    </div>
                `,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Tutup Preview'
            });
        })
        .catch(err => {
            Swal.fire('Error', 'Gagal memuat preview data dokumen.', 'error');
        });
}

function filterMaterialTable(status, btn) {
    const rows = document.querySelectorAll('.material-row-item');
    const buttons = document.querySelectorAll('.filter-btn');
    
    buttons.forEach(b => {
        b.classList.remove('bg-white', 'text-slate-900', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white');
        b.classList.add('text-slate-500');
    });
    
    btn.classList.add('bg-white', 'text-slate-900', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white');
    btn.classList.remove('text-slate-500');

    rows.forEach(row => {
        const badge = row.querySelector('.status-badge').textContent.trim().toLowerCase();
        if (status === 'all' || badge === status.toLowerCase()) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection