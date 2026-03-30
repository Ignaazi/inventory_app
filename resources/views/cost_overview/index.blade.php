@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    
    {{-- 1. COSTING KPI ROW --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-4">
        @php
            $costKpis = [
                ['label' => 'Pending Review', 'val' => '12', 'icon' => 'clock', 'bg' => 'bg-warning/10', 'color' => 'text-warning', 'sub' => 'Need Approval'],
                ['label' => 'Approved PR', 'val' => '45', 'icon' => 'check-circle', 'bg' => 'bg-success/10', 'color' => 'text-success', 'sub' => 'Ready for ERP'],
                ['label' => 'Rejected', 'val' => '03', 'icon' => 'x-circle', 'bg' => 'bg-danger/10', 'color' => 'text-danger', 'sub' => 'Returned'],
                ['label' => 'Total PR Value', 'val' => 'Rp 125M', 'icon' => 'trending-up', 'bg' => 'bg-primary/10', 'color' => 'text-primary', 'sub' => 'This Month'],
            ];
        @endphp

        @foreach($costKpis as $kpi)
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

    {{-- 2. CHART & URGENT ALERT --}}
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 xl:col-span-8 rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex justify-between items-center mb-6">
                <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm tracking-tight">Monthly PR Summary</h4>
            </div>
            <div class="h-[250px]"><canvas id="costSpendingChart"></canvas></div>
        </div>

        <div class="col-span-12 xl:col-span-4 flex flex-col gap-6">
            <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark flex-1">
                <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm mb-4 flex items-center gap-2">
                    <i data-feather="alert-circle" class="w-4 h-4 text-danger"></i> Stock Out (Critical)
                </h4>
                <div class="space-y-4">
                    <div class="flex items-start gap-3 border-l-4 border-danger pl-3 bg-danger/5 py-2">
                        <div>
                            <p class="text-[11px] font-black text-slate-800 dark:text-white uppercase">Nozzle Yamaha Z1</p>
                            <p class="text-[10px] text-danger font-bold italic underline">PR NEEDED IMMEDIATELY</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. PR APPROVAL TABLE --}}
    <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden mb-10">
        <div class="px-6 py-6 border-b border-stroke dark:border-strokedark flex items-center justify-between bg-gray-50/30 dark:bg-white/0">
            <div>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-tight">PR Approval Tracking</h4>
                <p class="text-[10px] text-bodydark2 font-medium">Review and validate requests from Engineering</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-4 py-2 rounded-lg text-[10px] font-black uppercase shadow-sm flex items-center gap-2 border border-stroke dark:border-strokedark">
                    <i data-feather="filter" class="w-3 h-3"></i> Filter
                </button>
                <button class="bg-primary text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase shadow-md flex items-center gap-2">
                    <i data-feather="download" class="w-3 h-3"></i> Export to Excel
                </button>
            </div>
        </div>
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-left">
                <thead>
                    <tr class="bg-whiten dark:bg-slate-800/50">
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase">PR Number</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase">Sparepart Detail</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase">Est. Price</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase">Requested By</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark font-medium">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 text-xs font-bold text-primary">#PR-ENG-2026-045</td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-slate-800 dark:text-white uppercase font-bold">Nozzle Yamaha Type 221</p>
                            <p class="text-[9px] text-slate-400 font-mono italic">Qty: 50 PCS | Vendor: Yamaha Indo</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-slate-800 dark:text-white">Rp 12.500.000</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-[10px] font-black uppercase text-slate-600 dark:text-slate-400">Engineering Dept</p>
                            <p class="text-[9px] text-slate-400 font-medium">30 Mar 2026</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button title="Approve" class="p-2 bg-success/10 text-success rounded-lg hover:bg-success hover:text-white transition-all">
                                    <i data-feather="check" class="w-4 h-4"></i>
                                </button>
                                <button title="Reject" class="p-2 bg-danger/10 text-danger rounded-lg hover:bg-danger hover:text-white transition-all">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        const ctx = document.getElementById('costSpendingChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line', 
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    datasets: [{
                        label: 'PR Submitted',
                        data: [5, 8, 4, 12, 7, 3],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5,5] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endsection