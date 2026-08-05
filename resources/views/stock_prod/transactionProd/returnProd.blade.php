@extends('admin')

@section('content')
<div class="p-6">
    <!-- HEADER PAGE -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Transaction Return - Production</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Riwayat dan pencatatan pengembalian sparepart/nozzle dari produksi ke stock.</p>
        </div>
        
        <!-- ACTION BUTTONS -->
        <div class="flex items-center gap-3">
            <a href="{{ Route::has('prod.transaction.return.manual') ? route('prod.transaction.return.manual') : '#' }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Manual Return
            </a>
            <a href="{{ Route::has('prod.transaction.return.scan') ? route('prod.transaction.return.scan') : '#' }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Scan Return
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-r shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-r shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 mb-6 shadow-sm">
        <form method="GET" action="{{ route('prod.transaction.return') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Return / Sparepart..." class="w-full px-3 py-2 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 text-xs border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-semibold rounded-lg transition-all duration-200">
                    Filter Data
                </button>
                <a href="{{ route('prod.transaction.return') }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- TABLE DATA -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">No. Return</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Sparepart</th>
                        <th class="px-4 py-3">Line Production</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3">Remark</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($returns ?? [] as $index => $item)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors duration-150">
                            <td class="px-4 py-3 text-center font-medium">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-bold text-indigo-600 dark:text-indigo-400">{{ $item->return_no ?? '-' }}</td>
                            <td class="px-4 py-3">{{ isset($item->created_at) ? $item->created_at->format('d M Y H:i') : '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">{{ $item->sparepart->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $item->lineProduction->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800 dark:text-white">{{ $item->qty ?? 0 }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $item->remark ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
                                    Completed
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">
                                <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5"/>
                                </svg>
                                Belum ada data transaksi return.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if(isset($returns) && method_exists($returns, 'links'))
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection