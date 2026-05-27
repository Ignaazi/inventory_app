@extends('admin')

@section('content')
<div x-data="{ 
    showPreview: false,
    showEdit: false,
    selectedPr: {},
    editForm: { id: '', pr_code: '', type_product: '', product: '', qty: '', priority: '', status: '' },
    
    initPreview(id) {
        fetch(`/eng/purchase-request/${id}/preview`)
            .then(res => res.json())
            .then(data => {
                this.selectedPr = data;
                this.showPreview = true;
            })
            .catch(err => alert('Gagal mengambil data preview!'));
    },
    initEdit(id) {
        fetch(`/eng/purchase-request/${id}/preview`)
            .then(res => res.json())
            .then(data => {
                this.editForm = { ...data };
                this.showEdit = true;
            })
            .catch(err => alert('Gagal memuat data edit!'));
    }
}" class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 font-sans text-black">
  
  {{-- HEADER SECTION & NAV BUTTONS --}}
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-950 dark:text-white uppercase tracking-tight font-sans">
        Purchase Request History
      </h2>
      <p class="text-xs font-medium text-slate-600 dark:text-gray-400 font-sans">Track, audit, and manage all active engineering machine nozzle requests</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('eng.pr.index') }}"
        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 px-3 py-2 text-xs font-bold text-white shadow-md hover:opacity-90 transition-opacity uppercase tracking-wider font-sans"
      >
        <svg class="w-3.5 h-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        New Request
      </a>
    </div>
  </div>

  {{-- FLASH MESSAGES NOTIFICATION LOGS --}}
  @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 dark:bg-emerald-950/20 dark:border-emerald-900 dark:text-emerald-400 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm font-sans">
      💡 {{ session('success') }}
    </div>
  @endif

  {{-- CONTAINER DATA TABLE --}}
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-slate-900 sm:px-6">
    
    {{-- TABLE FILTER CONTROL --}}
    <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-base font-bold text-slate-950 dark:text-white uppercase tracking-tight font-sans">
          Recent History PR
        </h3>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <div class="inline-flex items-center gap-2 p-1 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm font-sans">
          <button type="button" onclick="filterTable('all', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white">
            All
          </button>
          <button type="button" onclick="filterTable('waiting', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Waiting Approved
          </button>
          <button type="button" onclick="filterTable('approved', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Approved
          </button>
          <button type="button" onclick="filterTable('urgent', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Premium / Urgent
          </button>
        </div>
      </div>
    </div>

    {{-- MAIN TABLE LOG PR --}}
    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse font-sans" id="history-table">
        <thead>
          <tr class="border-gray-100 border-y dark:border-gray-800 bg-gray-50/50 dark:bg-slate-800/40">
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">NO</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">PR CODE REFERENCE</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">PRODUCT NAME</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">CATEGORY</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-[70px]">QTY</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">PRIORITY</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">STATUS</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">REQUEST DATE</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">ACTIONS</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-semibold text-black">
          @forelse($historyPr as $key => $pr)
          <tr class="table-row-item hover:bg-gray-50/50 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            {{-- 1. NO --}}
            <td class="py-3 px-3 text-xs font-semibold text-black dark:text-white">
              {{ $historyPr->firstItem() + $key }}
            </td>
            {{-- 2. PR CODE REFERENCE --}}
            <td class="py-3 px-3 text-xs font-semibold text-black dark:text-white font-mono text-center">
              {{ $pr->pr_code }}
            </td>
            {{-- 3. PRODUCT NAME --}}
            <td class="py-3 px-3 text-xs font-semibold text-black dark:text-white">
              {{ $pr->type_product }}
            </td>
            {{-- 4. CATEGORY --}}
            <td class="py-3 px-3 text-xs font-semibold text-black dark:text-white">
              {{ $pr->product }}
            </td>
            {{-- 🌟 TAMBAHAN: 5. QTY DI TABEL UTAMA --}}
            <td class="py-3 px-3 text-xs font-bold text-slate-900 dark:text-white text-center font-mono">
              {{ $pr->qty ?? 1 }}
            </td>
            {{-- 6. PRIORITY --}}
            <td class="py-3 px-3 text-center">
              <span class="priority-cell inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase
                @if(strtolower($pr->priority) == 'urgent') bg-rose-50 text-rose-600 border border-rose-100 
                @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                {{ $pr->priority }}
              </span>
            </td>
            {{-- 7. STATUS --}}
            <td class="py-3 px-3 text-center">
              <span class="status-cell inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-tight uppercase
                @if(in_array(strtolower($pr->status), ['draft', 'waiting', 'waiting approval'])) 
                  bg-blue-50 text-blue-700 border border-blue-100
                @elseif(strtolower($pr->status) == 'rejected')
                  bg-red-50 text-red-700 border border-red-100
                @else 
                  bg-emerald-50 text-emerald-700 border border-emerald-100 
                @endif">
                @if(in_array(strtolower($pr->status), ['draft', 'waiting', 'waiting approval']))
                  Waiting Approved
                @elseif(strtolower($pr->status) == 'rejected')
                  Rejected
                @else
                  Approved
                @endif
              </span>
            </td>
            {{-- 8. REQUEST DATE --}}
            <td class="py-2 px-3 text-center">
              <div class="text-[11px] font-semibold text-black dark:text-white tracking-tight">
                {{ $pr->request_date ? \Carbon\Carbon::parse($pr->request_date)->format('d/m/y') : '-' }}
              </div>
              <div class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 leading-none mt-0.5">
                {{ $pr->request_date ? \Carbon\Carbon::parse($pr->request_date)->format('H:i') : '' }}
              </div>
            </td>
            {{-- 9. ACTIONS CONTROLS --}}
            <td class="py-3 px-3 text-center">
              <div class="flex items-center justify-center gap-2">
                
                {{-- PREVIEW BUTTON --}}
                <button type="button" @click="initPreview({{ $pr->id }})" class="inline-flex items-center justify-center rounded-md bg-blue-600 w-8 h-8 text-white shadow-sm hover:bg-blue-700 transition-colors" title="Preview Detail">
                  <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  </svg>
                </button>

                {{-- EDIT BUTTON --}}
                <button type="button" @click="initEdit({{ $pr->id }})" class="inline-flex items-center justify-center rounded-md bg-amber-500 w-8 h-8 text-white shadow-sm hover:bg-amber-600 transition-colors" title="Edit Data">
                  <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                  </svg>
                </button>

                {{-- DELETE BUTTON --}}
                <form action="{{ route('purchase.request.delete', $pr->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data PR ini, Bro?')" class="inline-flex">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="inline-flex items-center justify-center rounded-md bg-red-600 w-8 h-8 text-white shadow-sm hover:bg-red-700 transition-colors" title="Delete">
                    <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 6m-4.74 0L9 9m4.74-3.42c.38-.003.76-.005 1.15-.01 1.201-.015 2.203-.878 2.203-2.051V4.077c0-1.164-.982-2.131-2.149-2.142A45.13 45.13 0 0 0 12 1.75c-1.543 0-3.03.154-4.471.454-1.167.011-2.149.978-2.149 2.142v.333c0 1.173.999 2.036 2.203 2.051.389.005.77.007 1.15.01m7.42 0a48.58 48.58 0 0 0-12 0m12 0v11.5c0 1.171-.949 2.133-2.052 2.133H8.054c-1.103 0-2.052-.962-2.052-2.133V5.503z" />
                    </svg>
                  </button>
                </form>

              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-8 text-xs font-bold text-slate-400 uppercase tracking-wider">
              No purchase request history logs found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- PAGINATION INTERFACE --}}
    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-2 border-t border-gray-100 pt-4 dark:border-gray-800">
      <p class="text-xs font-bold text-slate-950 dark:text-white font-sans">
        Showing {{ $historyPr->firstItem() ?? 0 }} to {{ $historyPr->lastItem() ?? 0 }} of {{ $historyPr->total() ?? 0 }} entries
      </p>
      <div class="flex items-center">
        {{ $historyPr->links() }}
      </div>
    </div>
  </div>

  {{-- 🔍 MODAL PREVIEW DETAIL --}}
  <div x-show="showPreview" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition style="display: none;">
    <div @click.away="showPreview = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200">
        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
            <h3 class="text-sm font-bold text-black font-mono" x-text="'Detail: ' + selectedPr.pr_code"></h3>
            <button @click="showPreview = false" class="text-gray-400 hover:text-black font-bold text-lg">&times;</button>
        </div>
        <div class="mt-4 space-y-3 text-xs font-sans text-black">
            <div class="grid grid-cols-3 py-1 border-b border-gray-100">
                <span class="text-gray-500">Product Name</span>
                <span class="col-span-2 font-bold text-black" x-text="selectedPr.type_product"></span>
            </div>
            <div class="grid grid-cols-3 py-1 border-b border-gray-100">
                <span class="text-gray-500">Category</span>
                <span class="col-span-2 font-semibold text-blue-600" x-text="selectedPr.product"></span>
            </div>
            {{-- 🌟 TAMBAHAN: QTY DI MODAL PREVIEW --}}
            <div class="grid grid-cols-3 py-1 border-b border-gray-100">
                <span class="text-gray-500">Quantity (QTY)</span>
                <span class="col-span-2 font-bold text-slate-950 font-mono" x-text="(selectedPr.qty ?? 1) + ' Pcs'"></span>
            </div>
            <div class="grid grid-cols-3 py-1 border-b border-gray-100">
                <span class="text-gray-500">Priority</span>
                <span class="col-span-2 uppercase font-extrabold text-red-600" x-text="selectedPr.priority"></span>
            </div>
            <div class="grid grid-cols-3 py-1 border-b border-gray-100">
                <span class="text-gray-500">Status</span>
                <span class="col-span-2 uppercase font-bold text-blue-600" x-text="['waiting', 'waiting approval', 'draft'].includes(String(selectedPr.status).toLowerCase()) ? 'Waiting Approved' : (String(selectedPr.status).toLowerCase() == 'rejected' ? 'Rejected' : 'Approved')"></span>
            </div>
            <div class="grid grid-cols-3 py-1">
                <span class="text-gray-500">Date Logged</span>
                <span class="col-span-2 font-mono text-black" x-text="selectedPr.request_date"></span>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button @click="showPreview = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-black font-bold rounded-lg transition-all text-xs">
                Close Preview
            </button>
        </div>
    </div>
  </div>

  {{-- 📝 MODAL EDIT --}}
  <div x-show="showEdit" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition style="display: none;">
    <div @click.away="showEdit = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200">
        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
            <h3 class="text-sm font-bold text-black font-sans">Update Purchase Request</h3>
            <button @click="showEdit = false" class="text-gray-400 hover:text-black font-bold text-lg">&times;</button>
        </div>
        
        <form :action="'/eng/purchase-request/' + editForm.id + '/update'" method="POST" class="mt-4 space-y-4 text-xs font-sans text-black">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">PR Code Reference</label>
                <input type="text" name="pr_code" x-model="editForm.pr_code" readonly class="w-full bg-gray-50 text-gray-600 border border-gray-200 rounded-lg p-2 font-mono focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Product Name</label>
                <input type="text" name="type_product" x-model="editForm.type_product" required class="w-full bg-white text-black border border-gray-200 rounded-lg p-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Category</label>
                <input type="text" name="product" x-model="editForm.product" required class="w-full bg-white text-black border border-gray-200 rounded-lg p-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
            </div>

            {{-- UPDATE GRID: MENAMBAHKAN INPUT FIELD QTY DI MODAL EDIT --}}
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Quantity</label>
                    <input type="number" name="qty" min="1" x-model="editForm.qty" required class="w-full bg-white text-black border border-gray-200 rounded-lg p-2 font-bold font-mono text-center focus:ring-1 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Priority</label>
                    <select name="priority" x-model="editForm.priority" class="w-full bg-white text-black border border-gray-200 rounded-lg p-2 focus:outline-none">
                        <option value="normal">NORMAL</option>
                        <option value="urgent">URGENT</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Status</label>
                    <select name="status" x-model="editForm.status" class="w-full bg-white text-black border border-gray-200 rounded-lg p-2 focus:outline-none">
                        <option value="waiting approval">Waiting Approved</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" @click="showEdit = false" class="px-4 py-2 bg-gray-100 text-black font-bold rounded-lg hover:bg-gray-200 text-xs">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm text-xs transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
  </div>

</div>

{{-- CSS OVERRIDES PAGINATION --}}
<style>
  nav[role="navigation"] svg { width: 16px; height: 16px; display: inline; }
  nav[role="navigation"] div:first-child { display: none; }
  .pagination .page-item.active .page-link {
    background-color: #E11D48 !important;
    border-color: #E11D48 !important;
    color: white !important;
    font-weight: bold;
    font-size: 12px;
  }
  .pagination .page-link {
    color: inherit !important;
    font-weight: 700;
    font-size: 12px;
    padding: 4px 8px;
  }
  
  /* Menghilangkan spin button input number di modal edit */
  input[type=number]::-webkit-inner-spin-button, 
  input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
  }
  input[type=number] { -moz-appearance: textfield; }
</style>

{{-- JAVASCRIPT REALTIME ROW FILTER --}}
<script>
  function filterTable(criteria, element) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => {
      btn.classList.remove('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
      btn.classList.add('text-slate-600', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
    });

    if (element) {
      element.classList.remove('text-slate-600', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
      element.classList.add('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
    }

    const rows = document.querySelectorAll('.table-row-item');
    
    rows.forEach(row => {
      if (criteria === 'all') {
        row.style.display = '';
        return;
      }

      const statusText = row.querySelector('.status-cell').textContent.trim().toLowerCase();
      const priorityText = row.querySelector('.priority-cell').textContent.trim().toLowerCase();

      if (criteria === 'waiting') {
        if (statusText.includes('waiting')) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      } else if (criteria === 'approved') {
        if (statusText === 'approved' || (!statusText.includes('waiting') && statusText.includes('approve'))) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      } else if (criteria === 'urgent') {
        if (priorityText === 'urgent') {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    });
  }
</script>
@endsection