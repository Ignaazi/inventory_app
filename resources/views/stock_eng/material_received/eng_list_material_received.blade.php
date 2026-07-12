@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .approval-view, .approval-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  .table-row-item {
    transition: all 0.2s ease-in-out;
  }
</style>

<div class="approval-view mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 font-sans antialiased">
  
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white tracking-tight">
        List Eng Material Received
      </h2>
      <p class="text-sm font-semibold text-slate-500 dark:text-gray-400 mt-1">PT SIIX EMS INDONESIA • ENGINEERING SECTION</p>
    </div>
    <a href="{{ route('eng.material.receiving.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase py-2.5 px-4 shadow-md transition-all active:scale-95">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Confirm Material Received
    </a>
  </div>

  @if(session('success'))
      <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl font-bold text-xs uppercase tracking-wide shadow-sm">
          SYSTEM NOTIFICATION: {{ session('success') }}
      </div>
  @endif

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm sm:px-6">
    
    <div class="flex flex-col gap-4 mb-4 lg:flex-row lg:items-center lg:justify-between">
      
      <div class="relative w-full lg:w-72">
        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
        <input type="text" placeholder="Search Request..." class="w-full pl-9 pr-4 py-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl font-semibold text-xs outline-none transition-all focus:border-indigo-500 text-slate-950 dark:text-white placeholder-slate-400">
      </div>

      <div class="flex flex-wrap items-center gap-3 self-start lg:self-auto">
        <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
          <button type="button" onclick="filterMaterialTable('all', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-gray-700 dark:text-white">
            All
          </button>
          <button type="button" onclick="filterMaterialTable('submitted_by_costing', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            From Costing
          </button>
          <button type="button" onclick="filterMaterialTable('approved_by_spv', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Pending SPV
          </button>
          <button type="button" onclick="filterMaterialTable('completed', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Completed
          </button>
          <button type="button" onclick="filterMaterialTable('rejected', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Discrepancy
          </button>
        </div>
      </div>
    </div>

    <div class="w-full overflow-x-auto block align-middle">
      <table class="min-w-full text-left border-collapse mx-auto" id="material-table">
        <thead>
          <tr class="border-gray-100 border-y dark:border-gray-800 bg-gray-50/50">
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">NO</th>
            <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">Receiving Code</th>
            <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">PR Code</th>
            <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center whitespace-nowrap">Qty Received</th>
            <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center whitespace-nowrap">Status</th>
            <th class="py-2.5 px-6 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-28 whitespace-nowrap">1. Costing Sign</th>
            <th class="py-2.5 px-6 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-28 whitespace-nowrap">2. Eng Staff Check</th>
            <th class="py-2.5 px-6 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-28 whitespace-nowrap">3. Eng Spv App</th>
            <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">Created At</th>
            <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-44 whitespace-nowrap">Action Decision</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-slate-950 dark:text-white">
          @forelse($receivings as $index => $item)
          <tr class="material-row-item hover:bg-gray-50/50 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            
            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white whitespace-nowrap">
              {{ $receivings->firstItem() + $index }}
            </td>

            <td class="py-3 px-4 text-xs font-bold text-blue-600 dark:text-blue-400 font-mono tracking-wide whitespace-nowrap">
              {{ $item->receiving_code }}
            </td>
            
            <td class="py-3 px-4 text-xs font-bold text-slate-400 dark:text-slate-500 tracking-widest uppercase whitespace-nowrap">
              {{ $item->pr_code }}
            </td>

            <td class="py-3 px-4 text-xs font-bold text-center text-slate-950 dark:text-white whitespace-nowrap">
              {{ number_format($item->qty_received) }} <span class="text-[10px] text-slate-400 font-normal">Pcs</span>
            </td>
            
            <td class="py-3 px-4 text-center whitespace-nowrap">
              <span class="status-badge inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight
                @if($item->status == 'completed') bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/40
                @elseif($item->status == 'rejected') bg-rose-50 text-rose-700 border border-rose-100 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/40
                @else bg-orange-50 text-orange-700 border border-orange-100 dark:bg-orange-950/20 dark:text-orange-400 dark:border-orange-900/40 @endif">
                {{ $item->status === 'submitted_by_costing' ? 'Incoming Costing' : ($item->status === 'approved_by_spv' ? 'Pending SPV' : str_replace('_', ' ', ucfirst($item->status))) }}
              </span>
            </td>

            <td class="py-2 px-6 text-center whitespace-nowrap">
                <div class="flex items-center justify-center h-10 w-24 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-0.5 shadow-sm overflow-hidden mx-auto">
                    @if($item->costing_signature_path && file_exists(public_path($item->costing_signature_path)))
                        <img src="{{ asset($item->costing_signature_path) }}?v={{ time() }}" class="max-h-full max-w-full object-contain block mx-auto">
                    @else
                        <span class="text-slate-400 italic text-[9px] font-semibold">Empty</span>
                    @endif
                </div>
            </td>

            <td class="py-2 px-6 text-center whitespace-nowrap">
                <div class="flex items-center justify-center h-10 w-24 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-0.5 shadow-sm overflow-hidden mx-auto">
                    @if(($item->engineering_signature_path && file_exists(public_path($item->engineering_signature_path))) || ($item->eng_signature_path && file_exists(public_path($item->eng_signature_path))))
                        <img src="{{ asset($item->engineering_signature_path ?? $item->eng_signature_path) }}?v={{ time() }}" class="max-h-full max-w-full object-contain block mx-auto">
                    @else
                        <span class="text-amber-600 italic text-[9px] font-semibold">Waiting</span>
                    @endif
                </div>
            </td>

            <td class="py-2 px-6 text-center whitespace-nowrap">
                <div class="flex items-center justify-center h-10 w-24 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-0.5 shadow-sm overflow-hidden mx-auto">
                    @if(($item->engineering_spv_signature_path && file_exists(public_path($item->engineering_spv_signature_path))) || ($item->eng_spv_signature_path && file_exists(public_path($item->eng_spv_signature_path))))
                        <img src="{{ asset($item->engineering_spv_signature_path ?? $item->eng_spv_signature_path) }}?v={{ time() }}" class="max-h-full max-w-full object-contain block mx-auto">
                    @else
                        <span class="text-amber-600 italic text-[9px] font-semibold">Waiting</span>
                    @endif
                </div>
            </td>

            <td class="py-3 px-4 text-xs whitespace-nowrap">
              <div class="font-bold text-slate-800 dark:text-slate-200">
                {{ $item->created_at ? $item->created_at->format('d/m/y') : '-' }}
              </div>
              <div class="text-[10px] font-semibold text-slate-400 mt-0.5">
                {{ $item->created_at ? $item->created_at->format('H:i') : '' }} WIB
              </div>
            </td>
            
            <td class="py-3 px-4 whitespace-nowrap">
              <div class="flex items-center justify-center gap-1.5">
                @if($item->status === 'submitted_by_costing')
                    <a href="{{ route('eng.material.receiving.create', ['id' => $item->id]) }}" 
                       class="px-2.5 py-1.5 bg-gradient-to-r from-emerald-400 to-blue-500 hover:from-emerald-500 hover:to-blue-600 text-white font-black rounded-lg text-[10px] uppercase tracking-widest transition-all text-center inline-block active:scale-[0.98]">
                        ⚡ Verify Goods
                    </a>
                @elseif($item->status === 'approved_by_spv')
                    <a href="{{ route('eng.material.receiving.create', ['id' => $item->id]) }}" 
                       class="px-2.5 py-1.5 bg-gradient-to-r from-amber-400 to-purple-600 hover:from-amber-500 hover:to-purple-700 text-white font-black rounded-lg text-[10px] uppercase tracking-widest transition-all text-center inline-block active:scale-[0.98]">
                        🔑 Spv Approve
                    </a>
                @else
                    <a href="{{ route('eng.material.receiving.show', $item->id) }}" class="px-2.5 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-black rounded-lg text-[10px] uppercase tracking-widest transition-all text-center inline-block active:scale-[0.98]" title="View Document">
                        View Doc
                    </a>
                @endif

                <form id="delete-form-{{ $item->id }}" action="{{ route('eng.material.receiving.destroy', $item->id) }}" method="POST" class="hidden">
                    @csrf @method('DELETE')
                </form>
                <button type="button" onclick="confirmDeleteMaterial('{{ $item->id }}', '{{ $item->pr_code }}')" class="px-2 py-1.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-black rounded-lg text-[10px] uppercase tracking-wide transition-all active:scale-[0.98]" title="Delete Form">
                    Delete
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="p-12 text-center text-xs font-bold uppercase text-slate-400 dark:text-slate-500 tracking-widest">
              No Material Received Entries Queue Found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-1 border-t border-gray-100 pt-5 dark:border-gray-800">
      <p class="text-xs font-extrabold text-slate-950 dark:text-white">
        Showing {{ $receivings->firstItem() ?? 0 }} to {{ $receivings->lastItem() ?? 0 }} of {{ $receivings->total() ?? 0 }} entries
      </p>
      <div class="flex items-center">
        {{ $receivings->links() }}
      </div>
    </div>

  </div>
</div>

<style>
  nav[role="navigation"] svg {
    width: 16px;
    height: 16px;
    display: inline;
  }
  nav[role="navigation"] div:first-child {
    display: none;
  }
  .pagination .page-item.active .page-link {
    background-color: #3C50E0 !important;
    border-color: #3C50E0 !important;
    color: white !important;
    font-weight: bold;
    font-size: 12px;
  }
  .pagination .page-link {
    color: #0f172a !important; 
    font-weight: 700;
    font-size: 12px;
    padding: 4px 8px;
  }
</style>

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

function filterMaterialTable(status, element) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => {
      btn.classList.remove('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
      btn.classList.add('text-slate-600', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
    });

    if (element) {
      element.classList.remove('text-slate-600', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
      element.classList.add('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
    }

    const rows = document.querySelectorAll('.material-row-item');
    
    rows.forEach(row => {
      if (status === 'all') {
        row.style.display = '';
        return;
      }

      const badgeText = row.querySelector('.status-badge').textContent.trim().toLowerCase().replace(/\s+/g, '_');

      if (status === 'submitted_by_costing' && badgeText === 'incoming_costing') {
         row.style.display = '';
      } else if (status === 'approved_by_spv' && badgeText === 'pending_spv') {
         row.style.display = '';
      } else if (badgeText === status.toLowerCase()) {
         row.style.display = '';
      } else {
         row.style.display = 'none';
      }
    });
}
</script>
@endsection