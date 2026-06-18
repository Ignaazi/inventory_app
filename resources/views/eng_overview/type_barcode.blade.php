@extends('admin')

@section('content')
<!-- Inject Font Nunito & Global Table Size Overrides -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .structure-db-view, .structure-db-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  .action-btn-icon {
    transition: all 0.2s ease-in-out;
  }
  .action-btn-icon:hover {
    transform: scale(1.08);
    filter: brightness(1.05);
  }
</style>

<div class="structure-db-view mx-auto max-w-screen-2xl p-5 md:p-8 2xl:p-12">
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white tracking-tight">
        Barcode Structure Types
      </h2>
      <p class="text-sm font-semibold text-slate-500 mt-1">Track and manage your engineering component structure logs</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('barcode.parsing') }}"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-extrabold text-slate-950 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
      >
        ← Back To Customizer
      </a>
    </div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm sm:px-7">
    
    <div class="flex flex-col gap-4 mb-5 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-extrabold text-slate-950 dark:text-white tracking-tight">
          Component Structure Segment Logs 
          <span class="ml-2 inline-flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-extrabold text-indigo-600 dark:text-indigo-400">
            {{ $types->count() }} Segments
          </span>
        </h3>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <div class="inline-flex p-1.5 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
          <button type="button" onclick="filterStructureTable('all', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-gray-700 dark:text-white">
            All
          </button>
          <button type="button" onclick="filterStructureTable('alphanumeric', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Alphanumeric
          </button>
          <button type="button" onclick="filterStructureTable('numeric', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Numeric
          </button>
          <button type="button" onclick="filterStructureTable('alphabet', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Alphabet
          </button>
        </div>
      </div>
    </div>

    <!-- TABLE AREA -->
    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse" id="structure-table">
        <thead>
          <tr class="border-gray-200 border-y dark:border-gray-800 bg-gray-50/70 dark:bg-slate-800/60">
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">NO</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">CHARACTER TYPE</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">LENGTH LIMIT</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">SEGMENT VALUE</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">CREATE AT</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">UPDATE AT</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white text-center w-48">ACTION</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($types as $key => $type)
          <tr class="table-row-item hover:bg-gray-50/60 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            <td class="py-4 px-4 text-[13px] font-extrabold text-slate-950 dark:text-white">
              {{ $loop->iteration }}
            </td>
            
            <td class="py-4 px-4 whitespace-nowrap">
              <span class="type-cell inline-flex items-center justify-center rounded-full px-3.5 py-1 text-xs font-extrabold tracking-tight bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-900 uppercase">
                {{ $type->char_type }}
              </span>
            </td>
            
            <td class="py-4 px-4 text-[13px] font-bold text-slate-950 dark:text-white whitespace-nowrap">
              {{ $type->char_length }} Chars
            </td>

            <td class="py-4 px-4">
              <span class="inline-flex items-center justify-center rounded-full px-3.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/40 font-mono">
                {{ $type->char_value }}
              </span>
            </td>

            <!-- 1. CREATE AT COLUMN (DUA BARIS BERGAYA SPAREPART) -->
            <td class="py-4 px-4 text-[13px] whitespace-nowrap">
              <div class="font-bold text-slate-700 dark:text-slate-200">
                {{ \Carbon\Carbon::parse($type->created_at)->format('d/m/y') }}
              </div>
              <div class="text-[11px] font-medium text-slate-400 mt-0.5">
                {{ \Carbon\Carbon::parse($type->created_at)->format('H:i') }} WIB
              </div>
            </td>

            <!-- 2. UPDATE AT COLUMN (DUA BARIS BERGAYA SPAREPART) -->
            <td class="py-4 px-4 text-[13px] whitespace-nowrap">
              <div class="font-bold text-slate-700 dark:text-slate-200">
                {{ \Carbon\Carbon::parse($type->updated_at)->format('d/m/y') }}
              </div>
              <div class="text-[11px] font-medium text-slate-400 mt-0.5">
                {{ \Carbon\Carbon::parse($type->updated_at)->format('H:i') }} WIB
              </div>
            </td>

            <!-- ACTION SYSTEM DENGAN ICON KOTAK SERAGAM -->
            <td class="py-4 px-4">
              <div class="flex items-center justify-center gap-2">
                <!-- Preview Icon Button (Blue) -->
                <button type="button" 
                        onclick="alert('Detail Content: {{ $type->char_value }}')"
                        class="action-btn-icon flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 text-white shadow-sm"
                        title="View Details"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1.005 1.005 0 010 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </button>

                <!-- Edit Icon Button (Yellow/Amber) -->
                <a href="{{ route('barcode.parsing', ['edit_structure_id' => $type->id]) }}" 
                   class="action-btn-icon flex items-center justify-center w-8 h-8 rounded-lg bg-[#f1c40f] text-white shadow-sm"
                   title="Edit Structural Log"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                  </svg>
                </a>

                <!-- Delete Icon Button Form (Red) -->
                <form action="{{ route('barcode.type.delete', $type->id) }}" method="POST" onsubmit="return confirm('Hapus log konfigurasi komponen ini?');" class="inline m-0">
                  @csrf
                  @method('DELETE')
                  <button type="submit" 
                          class="action-btn-icon flex items-center justify-center w-8 h-8 rounded-lg bg-[#e74c3c] text-white shadow-sm"
                          title="Delete Log"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                    </svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="p-12 text-center text-xs font-bold uppercase text-slate-400 tracking-wider">
              No Component Structure Logged Yet
            </td>
          </tr>
          @endforelse <!-- FIX PENUTUP BLADE SUDAH BENAR -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // LIVE JS FILTER FOR STRUCTURE TYPES TABLE
  function filterStructureTable(criteria, element) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => {
      btn.classList.remove('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
      btn.classList.add('text-slate-500', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
    });

    if (element) {
      element.classList.remove('text-slate-500', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
      element.classList.add('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
    }

    const rows = document.querySelectorAll('.table-row-item');
    
    rows.forEach(row => {
      if (criteria === 'all') {
        row.style.display = '';
        return;
      }

      const typeText = row.querySelector('.type-cell').textContent.trim().toLowerCase();

      if (typeText === criteria) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>
@endsection