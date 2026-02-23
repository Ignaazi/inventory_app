@extends('admin')

@section('content')
{{-- Container utama tanpa padding tambahan karena sudah ada di admin.blade.php --}}
<div class="flex flex-col gap-6">
    
    {{-- 1. ENGINEERING KPI ROW (5 Columns) --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-5">
        @php
            $engKpis = [
                ['label' => 'Latest Stock', 'val' => $stats['total_part'], 'icon' => 'package', 'bg' => 'bg-primary/10', 'color' => 'text-primary', 'sub' => 'Total Items'],
                ['label' => 'Critical Alert', 'val' => $stats['critical'], 'icon' => 'alert-circle', 'bg' => 'bg-danger/10', 'color' => 'text-danger', 'sub' => 'Need PR'],
                ['label' => 'Lost Report', 'val' => '5', 'icon' => 'frown', 'bg' => 'bg-warning/10', 'color' => 'text-warning', 'sub' => 'This Month'],
                ['label' => 'Pending PR', 'val' => '3', 'icon' => 'shopping-cart', 'bg' => 'bg-success/10', 'color' => 'text-success', 'sub' => 'To Costing'],
                ['label' => 'Trans. (IN/OUT)', 'val' => '124', 'icon' => 'refresh-cw', 'bg' => 'bg-indigo-500/10', 'color' => 'text-indigo-500', 'sub' => 'Today'],
            ];
        @endphp

        @foreach($engKpis as $kpi)
        <div class="rounded-2xl border border-stroke bg-white p-4 shadow-sm dark:border-strokedark dark:bg-boxdark transition-all hover:shadow-md">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $kpi['bg'] }}">
                    <i data-feather="{{ $kpi['icon'] }}" class="w-4 h-4 {{ $kpi['color'] }}"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="block text-[10px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $kpi['label'] }}</span>
                    <span class="text-[9px] text-bodydark2 block">{{ $kpi['sub'] }}</span>
                </div>
            </div>
            <h4 class="text-xl font-bold text-slate-800 dark:text-white leading-none">{{ $kpi['val'] }}</h4>
        </div>
        @endforeach
    </div>

    {{-- 2. CHART & ALERTS SECTION --}}
    <div class="grid grid-cols-12 gap-6">
        {{-- Activity Chart --}}
        <div class="col-span-12 xl:col-span-8 rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex justify-between items-center mb-6">
                <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm tracking-tight">Aktivitas Transaksi Sparepart</h4>
                <div class="flex gap-4 text-[10px] font-bold">
                    <span class="flex items-center gap-1 text-primary uppercase"><span class="w-2 h-2 bg-primary rounded-full"></span> Stock In</span>
                    <span class="flex items-center gap-1 text-success uppercase"><span class="w-2 h-2 bg-success rounded-full"></span> Stock Out</span>
                </div>
            </div>
            <div class="h-[250px]"><canvas id="engActivityChart"></canvas></div>
        </div>

        {{-- Urgent PR & Lost Summary --}}
        <div class="col-span-12 xl:col-span-4 flex flex-col gap-6">
            <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark flex-1">
                <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm mb-4 flex items-center gap-2">
                    <i data-feather="shopping-bag" class="w-4 h-4 text-danger"></i> Urgent PR Required
                </h4>
                <div class="space-y-3">
                    @foreach($parts->where('status', 'Critical')->take(3) as $critical)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-rose-50 border border-rose-100 dark:bg-danger/5 dark:border-danger/20">
                        <div class="overflow-hidden">
                            <p class="text-[11px] font-black text-danger uppercase truncate">{{ $critical->sap_code }}</p>
                            <p class="text-[10px] text-slate-600 dark:text-slate-400 truncate">{{ $critical->part_name }}</p>
                        </div>
                        <button class="shrink-0 bg-danger text-white px-2 py-1 rounded text-[9px] font-bold hover:bg-opacity-90 transition-all">CREATE PR</button>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm mb-2 text-warning">Lost Report Summary</h4>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-black text-slate-800 dark:text-white">5 <span class="text-xs font-normal text-slate-500 uppercase tracking-widest">Items Lost</span></span>
                    <div class="h-10 w-10 bg-warning/10 rounded-full flex items-center justify-center text-warning">
                        <i data-feather="alert-octagon" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. STOCK TABLE (Engineering View) --}}
    <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden mb-10">
        <div class="px-6 py-6 border-b border-stroke dark:border-strokedark flex items-center justify-between bg-gray-50/30 dark:bg-white/0">
            <div>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-tight">Sparepart Stock Master</h4>
                <p class="text-[10px] text-bodydark2 font-medium">Monitoring level stok untuk SPV & Staff Engineering</p>
            </div>
            <div class="flex gap-2">
                <button class="flex items-center gap-2 px-3 py-1.5 border border-stroke rounded-lg text-[10px] font-black uppercase hover:bg-whiten transition-all dark:border-strokedark dark:text-white">
                    <i data-feather="filter" class="w-3 h-3"></i> Filter
                </button>
                <button class="flex items-center gap-2 px-3 py-1.5 bg-primary text-white rounded-lg text-[10px] font-black uppercase hover:bg-opacity-90 transition-all shadow-md">
                    <i data-feather="plus-circle" class="w-3 h-3"></i> New Item
                </button>
            </div>
        </div>
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-whiten dark:bg-slate-800/50 text-left">
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase tracking-widest">Part & Rack</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase tracking-widest">SAP Code</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase tracking-widest text-center">Stock Level</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase tracking-widest">Last Movement</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach($parts as $part)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600 font-black text-[10px] dark:bg-boxdark dark:text-white shadow-sm">
                                    {{ $part->rack_position }}
                                </div>
                                <span class="font-bold text-slate-800 dark:text-white text-sm uppercase tracking-tight">{{ $part->part_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-primary font-bold">{{ $part->sap_code }}</td>
                        <td class="px-6 py-4 min-w-[150px]">
                            <div class="w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full mt-2 relative overflow-hidden">
                                @php 
                                    $percent = ($part->current_stock / ($part->min_stock_threshold * 2)) * 100;
                                    $percent = $percent > 100 ? 100 : $percent;
                                    $barColor = $part->status == 'Critical' ? 'bg-danger' : ($part->status == 'Warning' ? 'bg-warning' : 'bg-success');
                                @endphp
                                <div class="{{ $barColor }} h-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1 px-0.5">
                                <span class="text-[9px] font-bold text-slate-500">{{ $part->current_stock }} Qty</span>
                                <span class="text-[9px] font-medium text-slate-400">Min: {{ $part->min_stock_threshold }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-[10px] text-bodydark2 font-bold uppercase">23 Feb 2026, 08:00</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-lg px-3 py-1 text-[10px] font-black uppercase tracking-wider
                                {{ $part->status == 'Critical' ? 'bg-rose-100 text-danger' : 'bg-emerald-100 text-success' }}">
                                {{ $part->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();

        const ctx = document.getElementById('engActivityChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['17 Feb', '18 Feb', '19 Feb', '20 Feb', '21 Feb', '22 Feb', '23 Feb'],
                    datasets: [
                        { label: 'Stock In', data: [45, 52, 38, 65, 48, 20, 15], backgroundColor: '#3c50e0', borderRadius: 4 },
                        { label: 'Stock Out', data: [30, 40, 45, 50, 42, 10, 5], backgroundColor: '#10b981', borderRadius: 4 }
                    ]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, 
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: { font: { size: 9, weight: 'bold' } }, 
                            grid: { borderDash: [5,5], color: '#e2e8f0' } 
                        },
                        x: { 
                            ticks: { font: { size: 9, weight: 'bold' } }, 
                            grid: { display: false } 
                        }
                    }
                }
            });
        }
    });
</script>
@endsection