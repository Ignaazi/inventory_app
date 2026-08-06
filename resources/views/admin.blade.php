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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;900&display=swap" rel="stylesheet">

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
                    },
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
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #2e3a47; }

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
                        
                        <!-- Main Large KPI Grid Section (Sejajar Sempurna dari Kiri ke Kanan) -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            
                            <!-- 1. ENGINEERING CARD -->
                            <a href="{{ route('eng.overview') }}" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#ff869a] to-[#ff6078] p-6 shadow-lg text-white border-0 min-h-[160px] flex flex-col justify-between no-underline transition-transform hover:-translate-y-0.5">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Engineering</span>
                                        <span class="text-[11px] text-white/70 block font-medium mt-0.5">Stock, approval & receiving</span>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                        <i data-feather="tool" class="w-5 h-5 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-4">
                                    <h4 class="text-3xl font-black tracking-tight">{{ number_format($engineering['stock_qty'] ?? 0) }} <span class="text-sm font-medium opacity-80 ml-1">Pcs</span></h4>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black bg-white/25">Critical {{ $engineering['stock_critical'] ?? 0 }}</span>
                                </div>
                                <div class="relative z-10 mt-2 flex gap-2 text-[10px] font-bold text-white/90">
                                    <span>Safe {{ $engineering['stock_safe'] ?? 0 }}</span>
                                    <span>Warn {{ $engineering['stock_warning'] ?? 0 }}</span>
                                    <span>Approve {{ $engineering['pending_approval'] ?? 0 }}</span>
                                </div>
                            </a>

                            <!-- 2. PRODUCTION CARD -->
                            <a href="{{ route('prod.overview') }}" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#52b1ff] to-[#268fff] p-6 shadow-lg text-white border-0 min-h-[160px] flex flex-col justify-between no-underline transition-transform hover:-translate-y-0.5">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Production</span>
                                        <span class="text-[11px] text-white/70 block font-medium mt-0.5">Stock, request & movement</span>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                        <i data-feather="package" class="w-5 h-5 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-4">
                                    <h4 class="text-3xl font-black tracking-tight">{{ number_format($production['stock_qty'] ?? 0) }} <span class="text-sm font-medium opacity-80 ml-1">Pcs</span></h4>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black bg-white/25">Pending {{ $production['pending_requests'] ?? 0 }}</span>
                                </div>
                                <div class="relative z-10 mt-2 flex gap-2 text-[10px] font-bold text-white/90">
                                    <span>{{ $production['requests'] ?? 0 }} Requests</span>
                                    <span>{{ $production['transactions'] ?? 0 }} Logs</span>
                                </div>
                            </a>

                            <!-- 3. COSTING CARD -->
                            <a href="{{ route('costing.overview') }}" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#54e3be] to-[#29cc97] p-6 shadow-lg text-white border-0 min-h-[160px] flex flex-col justify-between no-underline transition-transform hover:-translate-y-0.5">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Costing</span>
                                        <span class="text-[11px] text-white/70 block font-medium mt-0.5">PR & material receiving</span>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                        <i data-feather="file-text" class="w-5 h-5 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-4">
                                    <h4 class="text-3xl font-black tracking-tight">{{ number_format($costing['purchase_requests'] ?? 0) }} <span class="text-sm font-medium opacity-80 ml-1">PR</span></h4>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black bg-white/25">Urgent {{ $costing['urgent'] ?? 0 }}</span>
                                </div>
                                <div class="relative z-10 mt-2 flex gap-2 text-[10px] font-bold text-white/90">
                                    <span>Approve {{ $costing['pending_approval'] ?? 0 }}</span>
                                    <span>MR Open {{ $costing['material_open'] ?? 0 }}</span>
                                </div>
                            </a>

                            <!-- 4. RISK CONTROL CARD (SEJAJAR HORIZONTAL & REMARK KOTAK LENGKAP) -->
                            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#b388ff] to-[#7c4dff] p-6 shadow-lg text-white border-0 min-h-[160px] flex flex-col justify-between">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Risk Control</span>
                                        <span class="text-[11px] text-white/70 block font-medium mt-0.5">All department action queue</span>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                        <i data-feather="alert-triangle" class="w-5 h-5 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-4">
                                    <!-- Angka ditulis sejajar horizontal dengan ukuran font 3xl yang presisi -->
                                    <h4 class="text-3xl font-black tracking-tight">
                                        {{ number_format(($alerts['critical_stock'] ?? 0) + ($alerts['engineering_approval'] ?? 0) + ($alerts['engineering_receiving'] ?? 0) + ($alerts['production_requests'] ?? 0) + ($alerts['costing_approval'] ?? 0)) }} <span class="text-xs font-bold opacity-75">Alerts</span>
                                    </h4>
                                    <!-- Badge remark kotak di pojok kanan bawah -->
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black bg-white/25">Action Required</span>
                                </div>
                                <div class="relative z-10 mt-2 flex flex-wrap gap-x-3 gap-y-1 text-[10px] font-bold text-white/90">
                                    <span>Eng {{ ($alerts['critical_stock'] ?? 0) + ($alerts['engineering_approval'] ?? 0) }}</span>
                                    <span>Prod {{ $alerts['production_requests'] ?? 0 }}</span>
                                    <span>Cost {{ $alerts['costing_approval'] ?? 0 }}</span>
                                </div>
                            </div>

                        </div>

                        {{-- GRAFIK RINGKAS GABUNGAN 3 DEPARTEMEN --}}
                        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-lg dark:border-slate-700 dark:bg-boxdark">
                                <div class="mb-3 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-black uppercase tracking-tight text-slate-800 dark:text-white">Aktivitas Departemen</h3>
                                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400">7 hari terakhir</p>
                                    </div>
                                    <i data-feather="bar-chart-2" class="h-4 w-4 text-primary"></i>
                                </div>
                                <div id="adminActivityChart"></div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-lg dark:border-slate-700 dark:bg-boxdark">
                                <div class="mb-3 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-black uppercase tracking-tight text-slate-800 dark:text-white">Action Queue</h3>
                                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Pending lintas departemen</p>
                                    </div>
                                    <i data-feather="alert-circle" class="h-4 w-4 text-warning"></i>
                                </div>
                                <div id="adminQueueChart"></div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-lg dark:border-slate-700 dark:bg-boxdark">
                                <div class="mb-3 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-black uppercase tracking-tight text-slate-800 dark:text-white">Engineering Stock Health</h3>
                                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Safe, warning, critical</p>
                                    </div>
                                    <i data-feather="pie-chart" class="h-4 w-4 text-success"></i>
                                </div>
                                <div id="adminStockHealthChart"></div>
                            </div>
                        </div>

                        {{-- TABEL DATA SUMMARY 3 DEPARTEMEN --}}
                        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-boxdark">
                            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                                <h3 class="text-sm font-black uppercase tracking-tight text-slate-800 dark:text-white">Department Summary</h3>
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Ringkasan data utama dari Engineering, Production, dan Costing</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[760px] text-left text-xs font-bold">
                                    <thead class="bg-slate-50 text-[10px] uppercase text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        <tr>
                                            <th class="px-5 py-3">Department</th>
                                            <th class="px-5 py-3">Stock / Document</th>
                                            <th class="px-5 py-3">Pending Action</th>
                                            <th class="px-5 py-3">Movement / Receiving</th>
                                            <th class="px-5 py-3">Alert</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-800 dark:divide-slate-700 dark:text-slate-200">
                                        <tr>
                                            <td class="px-5 py-3 font-black">Engineering</td>
                                            <td class="px-5 py-3">{{ number_format($engineering['stock_qty'] ?? 0) }} Pcs</td>
                                            <td class="px-5 py-3">Approval {{ $engineering['pending_approval'] ?? 0 }} · MR {{ $engineering['pending_receiving'] ?? 0 }}</td>
                                            <td class="px-5 py-3">{{ $engineering['transactions'] ?? 0 }} Logs</td>
                                            <td class="px-5 py-3 text-rose-600">Critical {{ $engineering['stock_critical'] ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-5 py-3 font-black">Production</td>
                                            <td class="px-5 py-3">{{ number_format($production['stock_qty'] ?? 0) }} Pcs · {{ $production['requests'] ?? 0 }} Req</td>
                                            <td class="px-5 py-3">{{ $production['pending_requests'] ?? 0 }} Request</td>
                                            <td class="px-5 py-3">{{ $production['transactions'] ?? 0 }} Logs</td>
                                            <td class="px-5 py-3 text-amber-600">Pending {{ $production['pending_requests'] ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-5 py-3 font-black">Costing</td>
                                            <td class="px-5 py-3">{{ $costing['purchase_requests'] ?? 0 }} PR · {{ $costing['material_received'] ?? 0 }} MR</td>
                                            <td class="px-5 py-3">Approval {{ $costing['pending_approval'] ?? 0 }} · MR {{ $costing['material_open'] ?? 0 }}</td>
                                            <td class="px-5 py-3">Urgent {{ $costing['urgent'] ?? 0 }}</td>
                                            <td class="px-5 py-3 text-amber-600">Action {{ $costing['pending_approval'] ?? 0 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const activityChart = new ApexCharts(document.querySelector('#adminActivityChart'), {
                                    series: [
                                        { name: 'Engineering Logs', data: @json($engineeringActivity ?? []) },
                                        { name: 'Production Logs', data: @json($productionActivity ?? []) },
                                        { name: 'Costing PR', data: @json($costingPurchaseRequests ?? []) },
                                        { name: 'Costing MR', data: @json($costingMaterialReceived ?? []) }
                                    ],
                                    chart: { type: 'bar', height: 250, toolbar: { show: false }, fontFamily: 'Nunito, sans-serif' },
                                    colors: ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b'],
                                    plotOptions: { bar: { columnWidth: '48%', borderRadius: 3 } },
                                    dataLabels: { enabled: false },
                                    xaxis: { categories: @json($chartDates ?? []), labels: { style: { fontSize: '10px', fontWeight: 700 } } },
                                    legend: { position: 'top', fontSize: '10px', fontWeight: 700 }
                                });
                                activityChart.render();

                                const queueChart = new ApexCharts(document.querySelector('#adminQueueChart'), {
                                    series: @json(array_values($queueSummary ?? [])),
                                    labels: @json(array_keys($queueSummary ?? [])),
                                    chart: { type: 'donut', height: 250, fontFamily: 'Nunito, sans-serif' },
                                    colors: ['#8b5cf6', '#f59e0b', '#3b82f6', '#10b981', '#f43f5e'],
                                    legend: { position: 'bottom', fontSize: '10px', fontWeight: 700 },
                                    dataLabels: { enabled: true, style: { fontSize: '9px', fontWeight: 'bold' } }
                                });
                                queueChart.render();

                                const stockHealthChart = new ApexCharts(document.querySelector('#adminStockHealthChart'), {
                                    series: @json(array_values($stockHealth ?? [])),
                                    labels: ['Safe', 'Warning', 'Critical'],
                                    chart: { type: 'donut', height: 250, fontFamily: 'Nunito, sans-serif' },
                                    colors: ['#10b981', '#f59e0b', '#f43f5e'],
                                    legend: { position: 'bottom', fontSize: '10px', fontWeight: 700 },
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
