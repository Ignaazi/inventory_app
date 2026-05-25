@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6">
    
    <!-- Notifikasi Alert -->
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
                Approval History
            </h2>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS KARAWANG</p>
        </div>
    </div>

    <div class="bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark rounded-xl shadow-sm overflow-hidden">
        
        <!-- Toolbar Filter -->
        <div class="p-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 dark:border-strokedark bg-slate-50/50 dark:bg-slate-800/40">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Processed Requests History</h3>
            
            <div class="flex items-center gap-3 self-end sm:self-auto">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-inner">
                    @foreach(['all' => 'All', 'checked' => 'Checked', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
                        <button type="button" onclick="filterTable('{{ $key }}', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 {{ $key == 'all' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-xs text-left border-collapse font-sans" id="history-table">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200 dark:border-strokedark">
                        <th class="py-3 px-3 text-center">No</th>
                        <th class="py-3 px-4">Req No</th>
                        <th class="py-3 px-4">Sparepart</th>
                        <th class="py-3 px-3 text-center">Qty</th>
                        <th class="py-3 px-4">Line</th>
                        <th class="py-3 px-4">Requestor</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Sign Eng</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-strokedark text-slate-900 dark:text-white font-bold">
                    @forelse($history as $index => $log)
                    <tr class="table-row-item hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors whitespace-nowrap">
                        <td class="py-3.5 px-3 text-center">{{ $history->firstItem() + $index }}</td>
                        <td class="py-3.5 px-4 tracking-tight">{{ $log->request_no }}</td>
                        <td class="py-3.5 px-4">{{ $log->sparepart_name }}</td>
                        <td class="py-3.5 px-3 text-center">{{ $log->qty_req }} <span class="text-[10px] text-slate-500">Pcs</span></td>
                        <td class="py-3.5 px-4 uppercase">{{ $log->line_machine }}</td>
                        <td class="py-3.5 px-4">{{ $log->requestor }}</td>
                        
                        <td class="py-3.5 px-4 text-center status-container">
                            @php $status = strtolower($log->status); @endphp
                            <span class="status-cell inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border 
                                {{ $status == 'approved' ? 'bg-green-50 text-green-700 border-green-200' : 
                                  ($status == 'checked' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-red-50 text-red-700 border-red-200') }}">
                                {{ $log->status }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                @if($log->staff_signature) <div class="w-6 h-6 border rounded overflow-hidden p-0.5"><img src="{{ $log->staff_signature }}" class="w-full h-full object-contain"></div> @endif
                                @if($log->spv_signature) <div class="w-6 h-6 border rounded overflow-hidden p-0.5"><img src="{{ $log->spv_signature }}" class="w-full h-full object-contain"></div> @endif
                            </div>
                        </td>
                        
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('prod.request.preview', $log->id) }}" class="w-7 h-7 flex items-center justify-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <form action="{{ route('approval.history.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Hapus record ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 flex items-center justify-center bg-red-600 text-white rounded-lg hover:bg-red-700 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-12 text-slate-400">Belum ada riwayat approval.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($history->hasPages())
        <div class="p-4 border-t border-slate-200">{{ $history->links() }}</div>
        @endif
    </div>
</div>

<script>
  function filterTable(criteria, element) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('bg-white', 'text-slate-900', 'shadow-sm'));
    element.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
    document.querySelectorAll('.table-row-item').forEach(row => {
      const status = row.querySelector('.status-cell').textContent.toLowerCase();
      row.style.display = (criteria === 'all' || status.includes(criteria)) ? '' : 'none';
    });
  }
</script>
@endsection