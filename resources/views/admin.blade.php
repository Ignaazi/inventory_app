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
            { name: 'Bought PYPL', date: 'Nov 23, 01:00 PM', price: '$2,567.88', category: 'Finance', status: 'Success', color: 'bg-blue-600', initial: 'P' },
            { name: 'Bought AAPL', date: 'Nov 22, 09:00 PM', price: '$2,567.88', category: 'Technology', status: 'Pending', color: 'bg-slate-800', initial: 'A' },
            { name: 'Sell KKST', date: 'Oct 12, 03:54 PM', price: '$6,754.99', category: 'Finance', status: 'Success', color: 'bg-emerald-500', initial: 'K' },
            { name: 'Bought FB', date: 'Sep 09, 02:00 AM', price: '$1,445.41', category: 'Social media', status: 'Success', color: 'bg-blue-500', initial: 'F' },
            { name: 'Sell AMZN', date: 'Feb 35, 08:00 PM', price: '$5,698.55', category: 'E-commerce', status: 'Failed', color: 'bg-orange-500', initial: 'A' }
        ],
        get filteredData() {
            return this.allData.filter(item => item.name.toLowerCase().includes(this.search.toLowerCase()));
        },
        get pagedData() {
            let start = (this.currentPage - 1) * this.pageSize;
            let end = start + this.pageSize;
            return this.filteredData.slice(start, end);
        },
        get totalPages() {
            return Math.ceil(this.filteredData.length / this.pageSize);
        }
    }"
    x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')); $watch('darkMode', value => { localStorage.setItem('darkMode', JSON.stringify(value)); feather.replace(); }); feather.replace();"
    :class="{'dark bg-boxdark-2 text-bodydark1': darkMode === true}"
    class="bg-[#F1F5F9] font-sans antialiased"
  >
    @include('partials.preloader')

    <div class="flex h-screen overflow-hidden">
      @include('partials.sidebar')

      <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
        @include('partials.header')

        <main class="sidebar-transition">
            <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
              
              @if(Route::is('dashboard'))
                
                {{-- 1. TOP 7 KPI GRID --}}
                <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4 xl:grid-cols-7">
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
                          <div class="flex h-9 w-9 items-center justify-center rounded-full {{ $kpi['bg'] }}">
                            <i data-feather="{{ $kpi['icon'] }}" class="w-4 h-4 {{ $kpi['color'] }}"></i>
                          </div>
                          <div>
                            <span class="block text-[10px] font-black text-slate-800 dark:text-white uppercase leading-none">{{ $kpi['label'] }}</span>
                            <span class="text-[9px] text-bodydark2">System Stock</span>
                          </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                          <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ $kpi['val'] }}</h4>
                          <span class="flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[9px] font-bold {{ $kpi['up'] ? 'bg-emerald-50 text-success' : 'bg-rose-50 text-danger' }}">
                            @if($kpi['up']) <i data-feather="arrow-up-right" class="w-2.5 h-2.5"></i> @else <i data-feather="arrow-down-right" class="w-2.5 h-2.5"></i> @endif
                            {{ $kpi['trend'] }}
                          </span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-12 gap-6 mb-6">
                    <div class="col-span-12 xl:col-span-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- OPERATIONAL STATUS --}}
                            <div class="rounded-2xl border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                            <i data-feather="settings" class="w-4 h-4 text-primary"></i> OPERATIONAL STATUS
                                        </h4>
                                        <p class="text-[10px] text-bodydark2 mt-1">Real-time Line Efficiency</p>
                                    </div>
                                    <i data-feather="more-vertical" class="w-4 h-4 text-bodydark2 cursor-pointer"></i>
                                </div>
                                
                                <div class="flex items-end justify-between">
                                    <div>
                                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white">17/20</h3>
                                        <p class="text-[10px] font-medium text-success flex items-center gap-1 mt-1">
                                            <i data-feather="trending-up" class="w-3 h-3"></i> +3.85% <span class="text-bodydark2">vs last week</span>
                                        </p>
                                    </div>
                                    <div class="w-24 h-12">
                                        <canvas id="opChart"></canvas>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-2 border-t border-stroke dark:border-strokedark pt-4">
                                    <div class="flex justify-between text-[10px] items-center">
                                        <span class="text-bodydark2">Active Production Lines</span>
                                        <span class="font-bold">17 Lines</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] items-center text-danger">
                                        <span class="font-medium">Critical Stock Alert</span>
                                        <span class="font-bold underline">3 Lines</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] items-center">
                                        <span class="text-bodydark2">Requests Today</span>
                                        <span class="font-bold">24 Req</span>
                                    </div>
                                </div>
                            </div>

                            {{-- RISK & SECURITY --}}
                            <div class="rounded-2xl border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                            <i data-feather="shield" class="w-4 h-4 text-danger"></i> RISK & SECURITY
                                        </h4>
                                        <p class="text-[10px] text-bodydark2 mt-1">Loss & Disposal Tracking</p>
                                    </div>
                                    <i data-feather="more-vertical" class="w-4 h-4 text-bodydark2 cursor-pointer"></i>
                                </div>

                                <div class="flex items-end justify-between">
                                    <div>
                                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white">5 Pcs</h3>
                                        <p class="text-[10px] font-medium text-danger flex items-center gap-1 mt-1">
                                            <i data-feather="trending-down" class="w-3 h-3"></i> 0.31% <span class="text-bodydark2">than last week</span>
                                        </p>
                                    </div>
                                    <div class="w-24 h-12">
                                        <canvas id="riskChart"></canvas>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-2 border-t border-stroke dark:border-strokedark pt-4">
                                    <div class="flex justify-between text-[10px] items-center text-danger">
                                        <span class="font-medium">Monthly Item Lost</span>
                                        <span class="font-bold">5 Pcs</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] items-center">
                                        <span class="text-bodydark2">Disposal Pending</span>
                                        <span class="font-bold">12 Pcs</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] items-center text-warning">
                                        <span class="font-medium">PR Delayed</span>
                                        <span class="font-bold">2 Req</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                            <div class="flex justify-between items-center mb-4">
                              <h4 class="font-bold text-slate-800 dark:text-white">Inventory Traceability Trend</h4>
                              <div class="flex gap-2 text-[10px] font-bold">
                                <span class="flex items-center gap-1"><span class="w-3 h-3 bg-primary rounded-full"></span> IN</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 bg-success rounded-full"></span> OUT</span>
                              </div>
                            </div>
                            <div class="h-[250px]"><canvas id="portfolioChart"></canvas></div>
                        </div>
                    </div>

                    <div class="col-span-12 xl:col-span-4 space-y-6">
                        <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                            <h4 class="mb-4 font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i data-feather="dollar-sign" class="w-4 h-4 text-success"></i> COSTING SUMMARY
                            </h4>
                            <div class="p-4 bg-slate-900 text-white rounded-xl mb-4 relative overflow-hidden">
                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Est. Consumption</p>
                                <h3 class="text-2xl font-black mt-1">$4,250.00</h3>
                                <i data-feather="trending-up" class="absolute right-4 top-4 text-white/10 w-12 h-12"></i>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-center">
                                <div class="p-2 border border-stroke dark:border-strokedark rounded-lg">
                                    <p class="text-[9px] text-bodydark2 font-bold">APPROVED</p>
                                    <p class="font-bold text-success text-base">18</p>
                                </div>
                                <div class="p-2 border border-stroke dark:border-strokedark rounded-lg">
                                    <p class="text-[9px] text-bodydark2 font-bold">PENDING</p>
                                    <p class="font-bold text-warning text-base">4</p>
                                </div>
                            </div>
                        </div>

                        {{-- LIVE ALERTS - UPDATED TO MATCH THEME --}}
                        <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                            <div class="px-6 py-4 border-b border-stroke dark:border-strokedark">
                                <h4 class="font-bold text-slate-800 dark:text-white text-base">Live Alerts</h4>
                            </div>
                            <div class="p-4 space-y-4">
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/20 flex items-center justify-center text-danger shrink-0">
                                      <i data-feather="alert-circle" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                      <p class="text-sm font-bold text-slate-800 dark:text-white">Stock Critical</p>
                                      <p class="text-xs text-slate-500">Line 05 needs nozzle refill soon.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/20 flex items-center justify-center text-warning shrink-0">
                                      <i data-feather="clock" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                      <p class="text-sm font-bold text-slate-800 dark:text-white">Unverified Loss</p>
                                      <p class="text-xs text-slate-500">4 items not found in scan out.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TRACEABILITY LOG - UPDATED TO MATCH PHOTO --}}
                <div class="col-span-12 mb-10">
                  <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                    <div class="flex flex-col gap-4 px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
                      <h4 class="text-xl font-bold text-slate-800 dark:text-white">Latest Transactions</h4>
                      <div class="relative w-full max-w-[300px]">
                         <span class="absolute left-4 top-1/2 -translate-y-1/2 text-bodydark2">
                            <i data-feather="search" class="w-4 h-4"></i>
                         </span>
                         <input 
                            x-model="search" 
                            @input="currentPage = 1"
                            type="text" 
                            placeholder="Search..." 
                            class="w-full rounded-xl border border-stroke bg-white py-2 pl-10 pr-4 text-sm outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark-2 transition-all">
                      </div>
                    </div>
                    
                    <div class="max-w-full overflow-x-auto">
                      <table class="w-full table-auto">
                        <thead>
                          <tr class="text-left border-b border-stroke dark:border-strokedark">
                            <th class="px-6 py-4 text-sm font-medium text-bodydark2">Name</th>
                            <th class="px-6 py-4 text-sm font-medium text-bodydark2">Date</th>
                            <th class="px-6 py-4 text-sm font-medium text-bodydark2">Price</th>
                            <th class="px-6 py-4 text-sm font-medium text-bodydark2">Category</th>
                            <th class="px-6 py-4 text-sm font-medium text-bodydark2">Status</th>
                            <th class="px-6 py-4 text-sm font-medium text-bodydark2"></th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-strokedark">
                          <template x-for="(item, index) in pagedData" :key="index">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                              <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                  <div :class="item.color" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white font-bold text-xs" x-text="item.initial"></div>
                                  <span class="font-bold text-slate-800 dark:text-white text-sm" x-text="item.name"></span>
                                </div>
                              </td>
                              <td class="px-6 py-4 text-sm text-slate-600 dark:text-bodydark" x-text="item.date"></td>
                              <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-white" x-text="item.price"></td>
                              <td class="px-6 py-4 text-sm text-slate-600 dark:text-bodydark" x-text="item.category"></td>
                              <td class="px-6 py-4">
                                <span :class="{
                                    'bg-emerald-100 text-emerald-600': item.status === 'Success', 
                                    'bg-orange-100 text-orange-600': item.status === 'Pending', 
                                    'bg-rose-100 text-rose-600': item.status === 'Failed'
                                  }"
                                  class="inline-flex rounded-full px-3 py-1 text-xs font-bold" x-text="item.status"></span>
                              </td>
                              <td class="px-6 py-4 text-right">
                                <button class="text-bodydark2 hover:text-primary"><i data-feather="more-horizontal" class="w-5 h-5"></i></button>
                              </td>
                            </tr>
                          </template>
                        </tbody>
                      </table>
                    </div>

                    {{-- PAGINATION - MATCHING SCREENSHOT --}}
                    <div class="px-6 py-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-between border-t border-stroke dark:border-strokedark">
                        <button 
                            @click="currentPage > 1 ? currentPage-- : null"
                            class="flex items-center gap-2 px-4 py-2 border border-stroke rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-50">
                            <i data-feather="arrow-left" class="w-4 h-4"></i> Previous
                        </button>
                        
                        <div class="flex items-center gap-2">
                            <button @click="currentPage = 1" :class="currentPage === 1 ? 'bg-primary text-white' : 'text-slate-500'" class="w-10 h-10 rounded-xl font-bold text-sm">1</button>
                            <button @click="currentPage = 2" :class="currentPage === 2 ? 'bg-primary text-white' : 'text-slate-500'" class="w-10 h-10 rounded-xl font-bold text-sm">2</button>
                            <button class="w-10 h-10 rounded-xl font-bold text-sm text-slate-500">3</button>
                            <span class="px-2 text-slate-400 text-sm">...</span>
                            <button class="w-10 h-10 rounded-xl font-bold text-sm text-slate-500">8</button>
                            <button class="w-10 h-10 rounded-xl font-bold text-sm text-slate-500">9</button>
                            <button class="w-10 h-10 rounded-xl font-bold text-sm text-slate-500">10</button>
                        </div>

                        <button 
                            @click="currentPage < totalPages ? currentPage++ : null"
                            class="flex items-center gap-2 px-4 py-2 border border-primary rounded-xl text-sm font-bold text-primary hover:bg-blue-50">
                            Next <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                  </div>
                </div>

              @else
                <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark p-6">
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

          // Sparkline Configuration Helper
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

          // 1. Operational Status Sparkline
          new Chart(document.getElementById('opChart'), sparklineOptions('#219653'));

          // 2. Risk & Security Sparkline
          const riskOpt = sparklineOptions('#D34053');
          riskOpt.data.datasets[0].data = [80, 60, 70, 40, 50, 30, 40, 35];
          new Chart(document.getElementById('riskChart'), riskOpt);

          // 3. Main Inventory Chart
          const ctxLine = document.getElementById('portfolioChart');
          if (ctxLine) {
            new Chart(ctxLine, {
              type: 'line',
              data: {
                labels: ['Feb 16', 'Feb 17', 'Feb 18', 'Feb 19', 'Feb 20', 'Feb 21', 'Feb 22'],
                datasets: [
                    { label: 'IN', data: [120, 150, 100, 180, 140, 210, 190], borderColor: '#3c50e0', backgroundColor: 'rgba(60, 80, 224, 0.05)', fill: true, tension: 0.4, pointRadius: 2 },
                    { label: 'OUT', data: [80, 130, 110, 140, 120, 190, 170], borderColor: '#219653', tension: 0.4, pointRadius: 2 }
                ]
              },
              options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } },
                scales: {
                  y: { ticks: { font: { size: 10 } }, grid: { borderDash: [5,5] } },
                  x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                }
              }
            });
          }
      });
    </script>
  </body>
</html>