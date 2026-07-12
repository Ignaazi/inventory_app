@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6">
    
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-900 text-green-800 dark:text-green-400 shadow-sm flex items-center gap-3">
        <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
        <div class="text-xs font-bold uppercase tracking-wide flex items-center gap-2">
            <span>SYSTEM NOTIFICATION:</span>
            <span class="font-bold text-slate-900 dark:text-white normal-case">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                List Eng Material Received
            </h2>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS INDONESIA • ENGINEERING SECTION</p>
        </div>
        <a href="{{ route('eng.material.receiving.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase py-2.5 px-4 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Confirm Material Received
        </a>
    </div>

    <div class="bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 dark:border-strokedark bg-slate-50/50 dark:bg-slate-800/40">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Recent History Received</h3>
            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-inner">
                <button type="button" onclick="filterMaterialTable('all', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white">All</button>
                <button type="button" onclick="filterMaterialTable('submitted_by_costing', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500">From Costing</button>
                <button type="button" onclick="filterMaterialTable('approved_by_spv', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500">Verified (Pending SPV)</button>
                <button type="button" onclick="filterMaterialTable('completed', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500">Completed</button>
                <button type="button" onclick="filterMaterialTable('rejected', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500">Discrepancy</button>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-xs text-left border-collapse font-sans">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200 dark:border-strokedark">
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-4">Receiving Code</th>
                        <th class="py-3 px-4">PR Code</th>
                        <th class="py-3 px-4 text-center">Qty Received</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">1. Costing Sign</th> 
                        <th class="py-3 px-4 text-center">2. Eng Staff Check</th>
                        <th class="py-3 px-4 text-center">3. Eng Spv App</th>
                        <th class="py-3 px-4 leading-normal">Created At</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-strokedark text-slate-900 dark:text-white font-bold">
                    @forelse($receivings as $index => $item)
                    <tr class="material-row-item hover:bg-slate-50/80 transition-colors whitespace-nowrap">
                        <td class="py-3.5 px-3 text-center">{{ $receivings->firstItem() + $index }}</td>
                        <td class="py-3.5 px-4 font-mono text-blue-600">{{ $item->receiving_code }}</td>
                        <td class="py-3.5 px-4 tracking-tight">{{ $item->pr_code }}</td>
                        <td class="py-3.5 px-3 text-center">{{ number_format($item->qty_received) }} <span class="text-[10px] text-slate-500 font-normal">Pcs</span></td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="status-badge inline-flex items-center rounded-md px-2 py-0.5 text-[10px] uppercase tracking-wide border 
                                {{ $item->status === 'completed' ? 'bg-green-50 text-green-700 border-green-200' : 
                                  ($item->status === 'rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                {{ $item->status === 'submitted_by_costing' ? 'INCOMING COSTING' : str_replace('_', ' ', $item->status) }}
                            </span>
                        </td>

                        {{-- 1. Costing Sign --}}
                        <td class="py-3.5 px-4 text-center">
                            {!! $item->costing_signature_path ? '<span class="text-green-600 text-[10px]">🟢 ISSUED</span>' : '<span class="text-slate-400 italic text-[10px]">⚪ EMPTY</span>' !!}
                        </td>
                        {{-- 2. Eng Staff Check --}}
                        <td class="py-3.5 px-4 text-center">
                            {!! $item->eng_signature_path ? '<span class="text-green-600 text-[10px]">🟢 VERIFIED</span>' : '<span class="text-slate-400 italic text-[10px]">⚪ WAITING</span>' !!}
                        </td>
                        {{-- 3. Eng Spv App --}}
                        <td class="py-3.5 px-4 text-center">
                            {!! $item->eng_spv_signature_path ? '<span class="text-emerald-700 text-[10px]">🟢 COMPLETED</span>' : '<span class="text-slate-400 italic text-[10px]">⚪ WAITING</span>' !!}
                        </td>
                        
                        <td class="py-3.5 px-4">
                            <div>{{ $item->created_at ? $item->created_at->format('d/m/y') : '-' }}</div>
                            <div class="text-[10px] text-slate-400 font-normal">{{ $item->created_at ? $item->created_at->format('H:i') . ' WIB' : '' }}</div>
                        </td>
                        
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                
                                @if($item->status === 'submitted_by_costing')
                                  {{-- 🌟 FIX: Diarahkan ke .create membawa ID untuk lanjut ttd staff --}}
                                       <a href="{{ route('eng.material.receiving.create', ['id' => $item->id]) }}" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[10px] font-bold tracking-wide transition-all shadow-sm active:scale-90 flex items-center gap-1" title="Verify Physical Goods">
                                        ⚡ VERIFY GOODS
                                       </a>
                                  @elseif($item->status === 'approved_by_spv')
                                    {{-- 🌟 FIX: Diarahkan ke .create membawa ID untuk lanjut ttd supervisor --}}
                                    <a href="{{ route('eng.material.receiving.create', ['id' => $item->id]) }}" class="px-2 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded text-[10px] font-bold tracking-wide transition-all shadow-sm active:scale-90 flex items-center gap-1" title="Supervisor Final Approval">
                                        🔑 SPV APPROVE
                                    </a>
                                    @else
                                        {{-- Jika status completed / rejected, baru lempar ke view preview biasa (.show) --}}
                                        <a href="{{ route('eng.material.receiving.show', $item->id) }}" class="w-7 h-7 inline-flex items-center justify-center bg-slate-600 hover:bg-slate-700 text-white rounded-lg shadow-md transition-all active:scale-90" title="View Document">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endif

                                <form id="delete-form-{{ $item->id }}" action="{{ route('eng.material.receiving.destroy', $item->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button" onclick="confirmDeleteMaterial('{{ $item->id }}', '{{ $item->pr_code }}')" class="w-7 h-7 inline-flex items-center justify-center bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow-md transition-all active:scale-90" title="Delete Form">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-10 text-slate-400 italic">No data entries.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4">
            {{ $receivings->links() }}
        </div>
    </div>
</div>

<script>
function confirmDeleteMaterial(id, prCode) {
    Swal.fire({
        title: 'Hapus Data Received?',
        text: "Data PR " + prCode + " dan semua berkas file tanda tangan digital (.png) di server akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Permanen!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
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
        const badge = row.querySelector('.status-badge').textContent.trim().toLowerCase().replace(/\s+/g, '_');
        if (status === 'all') {
            row.style.display = '';
        } else if (status === 'submitted_by_costing' && badge === 'incoming_costing') {
            row.style.display = '';
        } else if (badge === status.toLowerCase()) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection