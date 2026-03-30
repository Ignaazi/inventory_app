@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    
    {{-- 1. PRODUCTION KPI ROW (4 Columns) --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-4">
        @php
            $prodKpis = [
                ['label' => 'Total Request', 'val' => $stats['total_request'], 'icon' => 'file-text', 'bg' => 'bg-primary/10', 'color' => 'text-primary', 'sub' => 'All Time'],
                ['label' => 'Waiting Appr.', 'val' => $stats['pending_approval'], 'icon' => 'clock', 'bg' => 'bg-warning/10', 'color' => 'text-warning', 'sub' => 'In Engineering'],
                ['label' => 'Received', 'val' => $stats['received_today'], 'icon' => 'check-circle', 'bg' => 'bg-success/10', 'color' => 'text-success', 'sub' => 'Today'],
                ['label' => 'Line Usage', 'val' => '92%', 'icon' => 'activity', 'bg' => 'bg-indigo-500/10', 'color' => 'text-indigo-500', 'sub' => 'Efficiency'],
            ];
        @endphp

        @foreach($prodKpis as $kpi)
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

    {{-- 2. CHART & SUMMARY SECTION --}}
    <div class="grid grid-cols-12 gap-6">
        {{-- Consumption Chart --}}
        <div class="col-span-12 xl:col-span-8 rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex justify-between items-center mb-6">
                <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm tracking-tight">Part Request Frequency</h4>
                <div class="flex gap-4 text-[10px] font-bold text-primary uppercase">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 bg-primary rounded-full"></span> Requests per Day</span>
                </div>
            </div>
            <div class="h-[250px]"><canvas id="prodRequestChart"></canvas></div>
        </div>

        {{-- Recent Activities --}}
        <div class="col-span-12 xl:col-span-4 flex flex-col gap-6">
            <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark flex-1">
                <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm mb-4 flex items-center gap-2">
                    <i data-feather="bell" class="w-4 h-4 text-warning"></i> Recent Status
                </h4>
                <div class="space-y-4">
                    <div class="flex items-start gap-3 border-l-4 border-success pl-3">
                        <div>
                            <p class="text-[11px] font-black text-slate-800 dark:text-white uppercase">REQ-002 Approved</p>
                            <p class="text-[10px] text-slate-500 italic">Nozzle Yamaha ready to pick up</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 border-l-4 border-warning pl-3">
                        <div>
                            <p class="text-[11px] font-black text-slate-800 dark:text-white uppercase">REQ-005 Pending</p>
                            <p class="text-[10px] text-slate-500 italic">Waiting Eng supervisor sign</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. REQUEST HISTORY TABLE (Production View) --}}
    <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden mb-10">
        <div class="px-6 py-6 border-b border-stroke dark:border-strokedark flex items-center justify-between bg-gray-50/30 dark:bg-white/0">
            <div>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-tight">Recent Request History</h4>
                <p class="text-[10px] text-bodydark2 font-medium">Monitoring part requests from your line</p>
            </div>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase shadow-md flex items-center gap-2">
                <i data-feather="plus" class="w-3 h-3"></i> New Request
            </button>
        </div>
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-whiten dark:bg-slate-800/50 text-left">
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase">Request ID</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase">Part Name</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase text-center">Qty</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase">Target Line</th>
                        <th class="px-6 py-4 text-[10px] font-black text-bodydark2 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark font-medium">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 text-xs font-bold text-primary">#REQ-2026-001</td>
                        <td class="px-6 py-4 text-xs text-slate-800 dark:text-white uppercase font-bold text-sm">Nozzle Yamaha Type 221</td>
                        <td class="px-6 py-4 text-center text-xs">5 PCS</td>
                        <td class="px-6 py-4 text-[10px] font-black uppercase">LINE 04</td>
                        <td class="px-6 py-4 text-xs">
                            <span class="bg-success/10 text-success px-2 py-1 rounded font-black uppercase text-[9px]">Received</span>
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

        const ctx = document.getElementById('prodRequestChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    datasets: [{
                        label: 'Requests',
                        data: [12, 19, 15, 8, 22, 5],
                        borderColor: '#3c50e0',
                        backgroundColor: 'rgba(60, 80, 224, 0.1)',
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