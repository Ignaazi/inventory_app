<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard | SparesCan System</title>

    <!-- Google Fonts: Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & Feather Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Nunito', 'sans-serif'],
                        nunito: ['Nunito', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1e3a8a',
                        stroke: '#e2e8f0',
                        strokedark: '#2e3a47',
                        body: '#64748b',
                        bodydark: '#aeaeae',
                        bodydark1: '#dee4ee',
                        bodydark2: '#8a99af',
                        whiten: '#F7F9FC',
                        boxdark: '#0f172a',
                        'boxdark-2': '#020617',
                        success: '#10b981',
                        danger: '#f43f5e',
                        warning: '#f59e0b',
                    },
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }

        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body
    x-data="{ 
        'darkMode': false, 
        'sidebarToggle': localStorage.getItem('sidebarState') !== null ? localStorage.getItem('sidebarState') === 'true' : window.innerWidth >= 1024
    }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode')) || false;
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
        $watch('sidebarToggle', value => localStorage.setItem('sidebarState', value));
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
                        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                        
                        {{-- HEADER TITLE SECTION --}}
                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Executive Cross-Department Control</h2>
                                <p class="text-[11px] font-bold text-slate-500">PT SIIX EMS INDONESIA — CENTRAL RISK CONTROL & SYSTEM OVERVIEW</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-blue-50 border border-blue-900/30 text-blue-950 text-[11px] font-black shadow-[2px_2px_0px_0px_#1e3a8a]">
                                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                    System Live Data
                                </span>
                            </div>
                        </div>

                        <!-- 1. TOP 4 KPI CARDS (TEMA NEOBRUTALIST INDUSTRIAL) -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            
                            <!-- 1. ENGINEERING CARD -->
                            <a href="{{ route('eng.overview') }}" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#ff869a] to-[#ff6078] p-5 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] min-h-[160px] flex flex-col justify-between no-underline transition-transform hover:-translate-y-0.5">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Engineering</span>
                                        <span class="text-[10px] text-white/80 block font-medium mt-0.5">Stock, approval & receiving</span>
                                    </div>
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/20 border border-white/30 text-white">
                                        <i data-feather="tool" class="w-4 h-4 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-3">
                                    <h4 class="text-3xl font-black tracking-tight">{{ number_format($engineering['stock_qty'] ?? 0) }} <span class="text-xs font-bold opacity-80">Pcs</span></h4>
                                    <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">Critical {{ $engineering['stock_critical'] ?? 0 }}</span>
                                </div>
                                <div class="relative z-10 mt-2 flex gap-2 text-[10px] font-bold text-white/90">
                                    <span>Safe {{ $engineering['stock_safe'] ?? 0 }}</span>
                                    <span>Warn {{ $engineering['stock_warning'] ?? 0 }}</span>
                                    <span>Approve {{ $engineering['pending_approval'] ?? 0 }}</span>
                                </div>
                            </a>

                            <!-- 2. PRODUCTION CARD -->
                            <a href="{{ route('prod.overview') }}" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#52b1ff] to-[#268fff] p-5 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] min-h-[160px] flex flex-col justify-between no-underline transition-transform hover:-translate-y-0.5">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Production</span>
                                        <span class="text-[10px] text-white/80 block font-medium mt-0.5">Stock, request & movement</span>
                                    </div>
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/20 border border-white/30 text-white">
                                        <i data-feather="package" class="w-4 h-4 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-3">
                                    <h4 class="text-3xl font-black tracking-tight">{{ number_format($production['stock_qty'] ?? 0) }} <span class="text-xs font-bold opacity-80">Pcs</span></h4>
                                    <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">Pending {{ $production['pending_requests'] ?? 0 }}</span>
                                </div>
                                <div class="relative z-10 mt-2 flex gap-2 text-[10px] font-bold text-white/90">
                                    <span>{{ $production['requests'] ?? 0 }} Requests</span>
                                    <span>{{ $production['transactions'] ?? 0 }} Logs</span>
                                </div>
                            </a>

                            <!-- 3. COSTING CARD -->
                            <a href="{{ route('costing.overview') }}" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#54e3be] to-[#29cc97] p-5 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] min-h-[160px] flex flex-col justify-between no-underline transition-transform hover:-translate-y-0.5">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Costing</span>
                                        <span class="text-[10px] text-white/80 block font-medium mt-0.5">PR & material receiving</span>
                                    </div>
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/20 border border-white/30 text-white">
                                        <i data-feather="file-text" class="w-4 h-4 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-3">
                                    <h4 class="text-3xl font-black tracking-tight">{{ number_format($costing['purchase_requests'] ?? 0) }} <span class="text-xs font-bold opacity-80">PR</span></h4>
                                    <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">Urgent {{ $costing['urgent'] ?? 0 }}</span>
                                </div>
                                <div class="relative z-10 mt-2 flex gap-2 text-[10px] font-bold text-white/90">
                                    <span>Approve {{ $costing['pending_approval'] ?? 0 }}</span>
                                    <span>MR Open {{ $costing['material_open'] ?? 0 }}</span>
                                </div>
                            </a>

                            <!-- 4. RISK CONTROL CARD -->
                            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#b388ff] to-[#7c4dff] p-5 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] min-h-[160px] flex flex-col justify-between">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Risk Control</span>
                                        <span class="text-[10px] text-white/80 block font-medium mt-0.5">All department action queue</span>
                                    </div>
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/20 border border-white/30 text-white">
                                        <i data-feather="alert-triangle" class="w-4 h-4 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-3">
                                    <h4 class="text-3xl font-black tracking-tight">
                                        {{ number_format(($alerts['critical_stock'] ?? 0) + ($alerts['engineering_approval'] ?? 0) + ($alerts['engineering_receiving'] ?? 0) + ($alerts['production_requests'] ?? 0) + ($alerts['costing_approval'] ?? 0)) }} <span class="text-xs font-bold opacity-80">Alerts</span>
                                    </h4>
                                    <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">Action Required</span>
                                </div>
                                <div class="relative z-10 mt-2 flex flex-wrap gap-x-3 gap-y-1 text-[10px] font-bold text-white/90">
                                    <span>Eng {{ ($alerts['critical_stock'] ?? 0) + ($alerts['engineering_approval'] ?? 0) }}</span>
                                    <span>Prod {{ $alerts['production_requests'] ?? 0 }}</span>
                                    <span>Cost {{ $alerts['costing_approval'] ?? 0 }}</span>
                                </div>
                            </div>

                        </div>

                        {{-- 2. GRAFIK RINGKAS GABUNGAN 3 DEPARTEMEN --}}
                        <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-3">
                            
                            <!-- CHART 1 -->
                            <div class="rounded-2xl border border-blue-900/30 bg-white dark:bg-slate-900 p-5 shadow-[3px_3px_0px_0px_#1e3a8a]">
                                <div class="mb-3 flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <div>
                                        <h3 class="text-xs font-black uppercase tracking-tight text-slate-800 dark:text-white flex items-center gap-1.5">
                                            <i data-feather="bar-chart-2" class="w-3.5 h-3.5 text-blue-900"></i>
                                            Aktivitas Lintas Departemen
                                        </h3>
                                        <p class="text-[10px] font-bold text-slate-400">7 Hari Terakhir</p>
                                    </div>
                                    <span class="text-[9px] font-extrabold text-blue-600 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-md">Realtime</span>
                                </div>
                                <div id="adminActivityChart"></div>
                            </div>

                            <!-- CHART 2 -->
                            <div class="rounded-2xl border border-blue-900/30 bg-white dark:bg-slate-900 p-5 shadow-[3px_3px_0px_0px_#1e3a8a]">
                                <div class="mb-3 flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <div>
                                        <h3 class="text-xs font-black uppercase tracking-tight text-slate-800 dark:text-white flex items-center gap-1.5">
                                            <i data-feather="alert-circle" class="w-3.5 h-3.5 text-amber-600"></i>
                                            Action Queue Breakdown
                                        </h3>
                                        <p class="text-[10px] font-bold text-slate-400">Pending Approval & Verification</p>
                                    </div>
                                    <span class="text-[9px] font-extrabold text-amber-600 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-md">Attention</span>
                                </div>
                                <div id="adminQueueChart"></div>
                            </div>

                            <!-- CHART 3 -->
                            <div class="rounded-2xl border border-blue-900/30 bg-white dark:bg-slate-900 p-5 shadow-[3px_3px_0px_0px_#1e3a8a]">
                                <div class="mb-3 flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <div>
                                        <h3 class="text-xs font-black uppercase tracking-tight text-slate-800 dark:text-white flex items-center gap-1.5">
                                            <i data-feather="pie-chart" class="w-3.5 h-3.5 text-emerald-600"></i>
                                            Engineering Stock Health
                                        </h3>
                                        <p class="text-[10px] font-bold text-slate-400">Rasio Distribusi Stok Safe/Critical</p>
                                    </div>
                                    <span class="text-[9px] font-extrabold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md">Audit</span>
                                </div>
                                <div id="adminStockHealthChart"></div>
                            </div>
                        </div>

                        {{-- 3. TABEL DATA SUMMARY 3 DEPARTEMEN --}}
                        <div class="mt-6 overflow-hidden rounded-2xl border border-blue-900/30 bg-white dark:bg-slate-900 shadow-[3px_3px_0px_0px_#1e3a8a] mb-6">
                            <div class="border-b border-slate-100 dark:border-slate-800 px-5 py-4 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-black uppercase tracking-tight text-slate-800 dark:text-white flex items-center gap-2">
                                        <i data-feather="layers" class="w-4 h-4 text-blue-900"></i> Department Executive Summary
                                    </h3>
                                    <p class="text-[10px] font-bold text-slate-400">Konsolidasi data utama dari Engineering, Production, dan Costing</p>
                                </div>
                                <span class="text-[9px] font-extrabold text-blue-600 bg-blue-50 dark:bg-blue-950/40 px-2.5 py-1 rounded-md">Live Sync</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[760px] text-left text-xs font-bold">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-800/50 text-[10px] font-black uppercase text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-800">
                                            <th class="px-5 py-3">Department</th>
                                            <th class="px-5 py-3">Stock / Document Volume</th>
                                            <th class="px-5 py-3">Pending Action Queue</th>
                                            <th class="px-5 py-3">Movement / Receiving Logs</th>
                                            <th class="px-5 py-3">Risk Alert Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td class="px-5 py-3 font-black text-blue-900 dark:text-blue-400">Engineering</td>
                                            <td class="px-5 py-3 font-mono font-extrabold">{{ number_format($engineering['stock_qty'] ?? 0) }} Pcs</td>
                                            <td class="px-5 py-3">Approval {{ $engineering['pending_approval'] ?? 0 }} · MR {{ $engineering['pending_receiving'] ?? 0 }}</td>
                                            <td class="px-5 py-3">{{ $engineering['transactions'] ?? 0 }} Logs</td>
                                            <td class="px-5 py-3">
                                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border border-rose-200 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400">
                                                    Critical {{ $engineering['stock_critical'] ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td class="px-5 py-3 font-black text-indigo-600 dark:text-indigo-400">Production</td>
                                            <td class="px-5 py-3 font-mono font-extrabold">{{ number_format($production['stock_qty'] ?? 0) }} Pcs · {{ $production['requests'] ?? 0 }} Req</td>
                                            <td class="px-5 py-3">{{ $production['pending_requests'] ?? 0 }} Request Pending</td>
                                            <td class="px-5 py-3">{{ $production['transactions'] ?? 0 }} Logs</td>
                                            <td class="px-5 py-3">
                                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border border-amber-200 bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                                                    Pending {{ $production['pending_requests'] ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td class="px-5 py-3 font-black text-emerald-600 dark:text-emerald-400">Costing</td>
                                            <td class="px-5 py-3 font-mono font-extrabold">{{ $costing['purchase_requests'] ?? 0 }} PR · {{ $costing['material_received'] ?? 0 }} MR</td>
                                            <td class="px-5 py-3">Approval {{ $costing['pending_approval'] ?? 0 }} · MR {{ $costing['material_open'] ?? 0 }}</td>
                                            <td class="px-5 py-3">Urgent {{ $costing['urgent'] ?? 0 }}</td>
                                            <td class="px-5 py-3">
                                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border border-blue-200 bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400">
                                                    Action {{ $costing['pending_approval'] ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- APEXCHARTS INITIALIZATION --}}
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                if (typeof feather !== 'undefined') {
                                    feather.replace();
                                }

                                const activityChart = new ApexCharts(document.querySelector('#adminActivityChart'), {
                                    series: [
                                        { name: 'Engineering Logs', data: @json($engineeringActivity ?? []) },
                                        { name: 'Production Logs', data: @json($productionActivity ?? []) },
                                        { name: 'Costing PR', data: @json($costingPurchaseRequests ?? []) },
                                        { name: 'Costing MR', data: @json($costingMaterialReceived ?? []) }
                                    ],
                                    chart: { type: 'bar', height: 230, toolbar: { show: false }, fontFamily: 'Nunito, sans-serif' },
                                    colors: ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b'],
                                    plotOptions: { bar: { columnWidth: '48%', borderRadius: 3 } },
                                    dataLabels: { enabled: false },
                                    xaxis: { categories: @json($chartDates ?? []), labels: { style: { fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 } } },
                                    legend: { position: 'top', horizontalAlign: 'left', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 }
                                });
                                activityChart.render();

                                const queueChart = new ApexCharts(document.querySelector('#adminQueueChart'), {
                                    series: @json(array_values($queueSummary ?? [])),
                                    labels: @json(array_keys($queueSummary ?? [])),
                                    chart: { type: 'donut', height: 230, fontFamily: 'Nunito, sans-serif' },
                                    colors: ['#8b5cf6', '#f59e0b', '#3b82f6', '#10b981', '#f43f5e'],
                                    legend: { position: 'bottom', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 },
                                    dataLabels: { enabled: true, style: { fontSize: '9px', fontWeight: 'bold' } }
                                });
                                queueChart.render();

                                const stockHealthChart = new ApexCharts(document.querySelector('#adminStockHealthChart'), {
                                    series: @json(array_values($stockHealth ?? [])),
                                    labels: ['Safe Stock', 'Warning Stock', 'Critical Stock'],
                                    chart: { type: 'donut', height: 230, fontFamily: 'Nunito, sans-serif' },
                                    colors: ['#10b981', '#f59e0b', '#f43f5e'],
                                    legend: { position: 'bottom', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 },
                                    dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 'bold' } }
                                });
                                stockHealthChart.render();
                            });
                        </script>

                    @else
                        @yield('content')
                    @endif
                </div>
            </main>
        </div>
    </div>
</body>
</html>
