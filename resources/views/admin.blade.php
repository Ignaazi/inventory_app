<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard | SparesCan System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3c50e0',
                        stroke: '#e2e8f0',
                        strokedark: '#2e3a47',
                        body: '#64748b',
                        bodydark: '#aeaeae',
                        bodydark1: '#dee4ee',
                        bodydark2: '#8a99af',
                        whiten: '#F7F9FC',
                        boxdark: '#24303f',
                        'boxdark-2': '#1a222c',
                        success: '#219653',
                        danger: '#D34053',
                        warning: '#FFA70B',
                    },
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .shadow-default { box-shadow: 0px 8px 13px -3px rgba(0, 0, 0, 0.07); }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #2e3a47; }

        /* Perbaikan Scroll: Memastikan kontainer utama stabil */
        html, body {
            height: 100%;
            overflow: hidden;
        }
    </style>
</head>

<body
    x-data="{ 
        'darkMode': false, 
        'sidebarToggle': false,
        'currentPage': 1,
        'pageSize': 5,
        'search': '',
        'allData': [
            { name: 'Bought PYPL', date: 'Nov 23, 2025', price: '$2,567.88', category: 'Finance', status: 'Success', color: 'bg-blue-600', initial: 'P' },
            { name: 'Bought AAPL', date: 'Nov 22, 2025', price: '$2,567.88', category: 'Technology', status: 'Pending', color: 'bg-slate-800', initial: 'A' },
            { name: 'Sell KKST', date: 'Oct 12, 2025', price: '$6,754.99', category: 'Finance', status: 'Success', color: 'bg-emerald-500', initial: 'K' },
            { name: 'Bought FB', date: 'Sep 09, 2025', price: '$1,445.41', category: 'Social media', status: 'Success', color: 'bg-blue-500', initial: 'F' },
            { name: 'Sell AMZN', date: 'Feb 15, 2026', price: '$5,698.55', category: 'E-commerce', status: 'Failed', color: 'bg-orange-500', initial: 'A' },
            { name: 'Nozzle Type A', date: 'Feb 20, 2026', price: '$120.00', category: 'Engineering', status: 'Success', color: 'bg-purple-500', initial: 'N' },
            { name: 'Sparepart B2', date: 'Feb 21, 2026', price: '$45.00', category: 'Production', status: 'Pending', color: 'bg-yellow-500', initial: 'S' }
        ],
        get filteredData() {
            return this.allData.filter(item => 
                item.name.toLowerCase().includes(this.search.toLowerCase()) ||
                item.category.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        get pagedData() {
            let start = (this.currentPage - 1) * this.pageSize;
            let end = start + this.pageSize;
            return this.filteredData.slice(start, end);
        },
        get totalPages() {
            return Math.ceil(this.filteredData.length / this.pageSize) || 1;
        }
    }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode')) || false;
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
        $watch('pagedData', () => { $nextTick(() => { feather.replace(); }); });
        feather.replace();
    "
    :class="{'dark bg-boxdark-2 text-bodydark1': darkMode === true}"
    class="bg-[#F1F5F9] font-sans antialiased"
>
    <div class="flex h-screen overflow-hidden">
        
        @include('partials.sidebar')

        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden custom-scrollbar">
            
            @include('partials.header')

            <main class="w-full">
                <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                    
                    @if(Route::is('dashboard'))
                        
                        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-7">
                            @php
                                $kpis = [
                                    ['label' => 'ALL', 'val' => '2,140', 'icon' => 'package', 'bg' => 'bg-primary/10', 'color' => 'text-primary', 'trend' => '+11.01%', 'up' => true],
                                    ['label' => 'READY', 'val' => '1,840', 'icon' => 'check-circle', 'bg' => 'bg-success/10', 'color' => 'text-success', 'trend' => '+11.01%', 'up' => true],
                                    ['label' => 'USED', 'val' => '256', 'icon' => 'activity', 'bg' => 'bg-blue-400/10', 'color' => 'text-blue-400', 'trend' => '+11.01%', 'up' => true],
                                    ['label' => 'DAMAGED', 'val' => '14', 'icon' => 'alert-octagon', 'bg' => 'bg-danger/10', 'color' => 'text-danger', 'trend' => '-9.05%', 'up' => false],
                                    ['label' => 'LOST', 'val' => '3', 'icon' => 'help-circle', 'bg' => 'bg-warning/10', 'color' => 'text-warning', 'trend' => '-2.10%', 'up' => false],
                                    ['label' => 'TRASH', 'val' => '42', 'icon' => 'trash-2', 'bg' => 'bg-slate-400/10', 'color' => 'text-slate-500', 'trend' => '+5.02%', 'up' => true],
                                    ['label' => 'HEALTH', 'val' => '86%', 'icon' => 'heart', 'bg' => 'bg-indigo-500/10', 'color' => 'text-indigo-500', 'trend' => '+11.01%', 'up' => true],
                                ];
                            @endphp
                            @foreach($kpis as $kpi)
                            <div class="rounded-2xl border border-stroke bg-white p-4 shadow-sm dark:border-strokedark dark:bg-boxdark">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $kpi['bg'] }}">
                                        <i data-feather="{{ $kpi['icon'] }}" class="w-4 h-4 {{ $kpi['color'] }}"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <span class="block text-[10px] font-black text-slate-800 dark:text-white uppercase truncate">{{ $kpi['label'] }}</span>
                                        <span class="text-[9px] text-bodydark2 block">Stock</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between gap-1">
                                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ $kpi['val'] }}</h4>
                                    <span class="flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[9px] font-bold {{ $kpi['up'] ? 'bg-emerald-50 text-success' : 'bg-rose-50 text-danger' }}">
                                        {{ $kpi['trend'] }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-12 gap-6 mb-6">
                            <div class="col-span-12 xl:col-span-12 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="rounded-2xl border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 text-sm uppercase">
                                                <i data-feather="settings" class="w-4 h-4 text-primary"></i> Operational
                                            </h4>
                                            <i data-feather="more-vertical" class="w-4 h-4 text-bodydark2"></i>
                                        </div>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-2xl font-bold dark:text-white">17/20</h3>
                                            <div class="w-24 h-12"><canvas id="opChart"></canvas></div>
                                        </div>
                                        <div class="mt-4 space-y-2 border-t border-stroke dark:border-strokedark pt-4">
                                            <div class="flex justify-between text-[10px] items-center">
                                                <span class="text-bodydark2 uppercase">Active Lines</span>
                                                <span class="font-bold">17 Lines</span>
                                            </div>
                                            <div class="flex justify-between text-[10px] items-center text-danger">
                                                <span class="font-medium uppercase">Critical Alert</span>
                                                <span class="font-bold underline">3 Lines</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 text-sm uppercase">
                                                <i data-feather="shield" class="w-4 h-4 text-danger"></i> Risk Control
                                            </h4>
                                            <i data-feather="more-vertical" class="w-4 h-4 text-bodydark2"></i>
                                        </div>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-2xl font-bold dark:text-white">5 Pcs</h3>
                                            <div class="w-24 h-12"><canvas id="riskChart"></canvas></div>
                                        </div>
                                        <div class="mt-4 space-y-2 border-t border-stroke dark:border-strokedark pt-4">
                                            <div class="flex justify-between text-[10px] items-center text-danger">
                                                <span class="font-medium uppercase">Lost Monthly</span>
                                                <span class="font-bold">5 Pcs</span>
                                            </div>
                                            <div class="flex justify-between text-[10px] items-center">
                                                <span class="text-bodydark2 uppercase">Pending Disposal</span>
                                                <span class="font-bold">12 Pcs</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 text-sm uppercase">
                                                <i data-feather="dollar-sign" class="w-4 h-4 text-success"></i> Costing
                                            </h4>
                                            <i data-feather="more-vertical" class="w-4 h-4 text-bodydark2"></i>
                                        </div>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-2xl font-bold dark:text-white">$4,250</h3>
                                            <div class="w-24 h-12"><canvas id="costChart"></canvas></div>
                                        </div>
                                        <div class="mt-4 space-y-2 border-t border-stroke dark:border-strokedark pt-4">
                                            <div class="flex justify-between text-[10px] items-center text-success">
                                                <span class="font-medium uppercase">Approved Req</span>
                                                <span class="font-bold">18 Items</span>
                                            </div>
                                            <div class="flex justify-between text-[10px] items-center text-warning">
                                                <span class="font-medium uppercase">Pending Req</span>
                                                <span class="font-bold">4 Items</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-12 gap-6">
                                    <div class="col-span-12 xl:col-span-8 rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm">Inventory Traceability Trend</h4>
                                            <div class="flex gap-4 text-[10px] font-bold">
                                                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-primary rounded-full"></span> IN</span>
                                                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-success rounded-full"></span> OUT</span>
                                            </div>
                                        </div>
                                        <div class="h-[250px]"><canvas id="portfolioChart"></canvas></div>
                                    </div>

                                    <div class="col-span-12 xl:col-span-4 rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                                        <div class="px-6 py-4 border-b border-stroke dark:border-strokedark">
                                            <h4 class="font-bold text-slate-800 dark:text-white uppercase text-sm">Live Alerts</h4>
                                        </div>
                                        <div class="p-4 space-y-4">
                                            <template x-for="i in 2">
                                                <div class="flex gap-4 p-2 rounded-xl hover:bg-whiten dark:hover:bg-boxdark-2 transition-all">
                                                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-danger shrink-0">
                                                        <i data-feather="alert-circle" class="w-5 h-5"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold dark:text-white">Stock Critical</p>
                                                        <p class="text-xs text-bodydark2">Line 05 needs nozzle refill.</p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 mb-10">
                            <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                                <div class="px-6 py-6 border-b border-stroke dark:border-strokedark flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <h4 class="text-xl font-bold text-slate-800 dark:text-white">Latest Transactions</h4>
                                    <div class="relative w-full max-w-[300px]">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-bodydark2">
                                            <i data-feather="search" class="w-4 h-4"></i>
                                        </span>
                                        <input 
                                            x-model="search" 
                                            @input="currentPage = 1"
                                            type="text" 
                                            placeholder="Search code or category..." 
                                            class="w-full rounded-xl border border-stroke bg-gray-50 py-2.5 pl-11 pr-4 text-sm outline-none focus:border-primary focus:bg-white dark:border-strokedark dark:bg-boxdark-2 transition-all">
                                    </div>
                                </div>
                                
                                <div class="max-w-full overflow-x-auto custom-scrollbar">
                                    <table class="w-full table-auto">
                                        <thead>
                                            <tr class="bg-whiten dark:bg-slate-800/50 text-left">
                                                <th class="px-6 py-4 text-xs font-black text-bodydark2 uppercase">Name</th>
                                                <th class="px-6 py-4 text-xs font-black text-bodydark2 uppercase">Date</th>
                                                <th class="px-6 py-4 text-xs font-black text-bodydark2 uppercase">Price</th>
                                                <th class="px-6 py-4 text-xs font-black text-bodydark2 uppercase">Category</th>
                                                <th class="px-6 py-4 text-xs font-black text-bodydark2 uppercase">Status</th>
                                                <th class="px-6 py-4"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-stroke dark:divide-strokedark">
                                            <template x-for="(item, index) in pagedData" :key="index">
                                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center gap-3">
                                                            <div :class="item.color" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white font-bold text-xs shadow-sm" x-text="item.initial"></div>
                                                            <span class="font-bold text-slate-800 dark:text-white text-sm" x-text="item.name"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-bodydark2" x-text="item.date"></td>
                                                    <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-white" x-text="item.price"></td>
                                                    <td class="px-6 py-4 text-sm text-bodydark2" x-text="item.category"></td>
                                                    <td class="px-6 py-4">
                                                        <span :class="{
                                                            'bg-emerald-100 text-emerald-600': item.status === 'Success', 
                                                            'bg-orange-100 text-orange-600': item.status === 'Pending', 
                                                            'bg-rose-100 text-rose-600': item.status === 'Failed'
                                                          }"
                                                          class="inline-flex rounded-lg px-3 py-1 text-[10px] font-black uppercase tracking-wider" x-text="item.status"></span>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <button class="text-bodydark2 hover:text-primary transition-colors">
                                                            <i data-feather="more-horizontal" class="w-5 h-5"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="px-6 py-6 border-t border-stroke dark:border-strokedark flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-gray-50/50 dark:bg-white/0">
                                    <div class="text-xs font-bold text-bodydark2 uppercase tracking-widest">
                                        Showing <span class="text-slate-800 dark:text-white" x-text="filteredData.length > 0 ? ((currentPage - 1) * pageSize) + 1 : 0"></span> 
                                        to <span class="text-slate-800 dark:text-white" x-text="Math.min(currentPage * pageSize, filteredData.length)"></span> 
                                        of <span class="text-slate-800 dark:text-white" x-text="filteredData.length"></span> Entries
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <button 
                                            @click="currentPage > 1 ? currentPage-- : null"
                                            :disabled="currentPage === 1"
                                            class="p-2 border border-stroke rounded-lg disabled:opacity-30 hover:bg-white dark:border-strokedark dark:text-white">
                                            <i data-feather="chevron-left" class="w-4 h-4"></i>
                                        </button>

                                        <div class="flex items-center gap-1">
                                            <template x-for="page in totalPages" :key="page">
                                                <button 
                                                    @click="currentPage = page"
                                                    :class="currentPage === page ? 'bg-primary text-white border-primary shadow-md' : 'text-bodydark2 hover:bg-white border-transparent'"
                                                    class="w-8 h-8 rounded-lg font-bold text-xs transition-all border"
                                                    x-text="page">
                                                </button>
                                            </template>
                                        </div>

                                        <button 
                                            @click="currentPage < totalPages ? currentPage++ : null"
                                            :disabled="currentPage >= totalPages"
                                            class="p-2 border border-stroke rounded-lg disabled:opacity-30 hover:bg-white dark:border-strokedark dark:text-white">
                                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @else
                            @yield('content')
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();

            const sparklineOptions = (color) => ({
                type: 'line',
                data: {
                    labels: [1, 2, 3, 4, 5, 6, 7, 8],
                    datasets: [{
                        data: [30, 45, 35, 60, 50, 70, 65, 80],
                        borderColor: color,
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: { x: { display: false }, y: { display: false } }
                }
            });

            new Chart(document.getElementById('opChart'), sparklineOptions('#3c50e0'));
            
            const riskCtx = document.getElementById('riskChart');
            if(riskCtx) {
                const riskOpt = sparklineOptions('#D34053');
                riskOpt.data.datasets[0].data = [80, 60, 70, 40, 50, 30, 40, 35];
                new Chart(riskCtx, riskOpt);
            }

            const costCtx = document.getElementById('costChart');
            if(costCtx) {
                const costOpt = sparklineOptions('#219653');
                costOpt.data.datasets[0].data = [40, 50, 45, 70, 80, 85, 90, 100];
                new Chart(costCtx, costOpt);
            }

            const ctxLine = document.getElementById('portfolioChart');
            if (ctxLine) {
                new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: ['16 Feb', '17 Feb', '18 Feb', '19 Feb', '20 Feb', '21 Feb', '22 Feb'],
                        datasets: [
                            { label: 'IN', data: [120, 150, 100, 180, 140, 210, 190], borderColor: '#3c50e0', backgroundColor: 'rgba(60, 80, 224, 0.05)', fill: true, tension: 0.4, pointRadius: 3, hitRadius: 10 },
                            { label: 'OUT', data: [80, 130, 110, 140, 120, 190, 170], borderColor: '#219653', tension: 0.4, pointRadius: 3, hitRadius: 10 }
                        ]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { borderDash: [5,5], color: '#e2e8f0' } },
                            x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>