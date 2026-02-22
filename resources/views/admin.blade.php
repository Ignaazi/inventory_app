<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard | SparesCan System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        {{-- LOGIKAL TABEL DUMMY & PAGINATION --}}
        'currentPage': 1,
        'pageSize': 5,
        'search': '',
        'allData': [
            { name: 'Bought PYPL', date: 'Nov 23, 01:00 PM', price: '$2,567.88', category: 'Finance', status: 'Success', color: 'bg-blue-600', initial: 'P' },
            { name: 'Bought AAPL', date: 'Nov 22, 09:00 PM', price: '$1,232.00', category: 'Technology', status: 'Pending', color: 'bg-slate-800', initial: 'A' },
            { name: 'Sell AMZN', date: 'Feb 25, 08:00 PM', price: '$5,698.55', category: 'E-commerce', status: 'Failed', color: 'bg-orange-500', initial: 'A' },
            { name: 'Bought TSLA', date: 'Jan 10, 10:00 AM', price: '$800.00', category: 'Automotive', status: 'Success', color: 'bg-red-500', initial: 'T' },
            { name: 'Bought MSFT', date: 'Jan 12, 02:00 PM', price: '$450.00', category: 'Software', status: 'Success', color: 'bg-teal-500', initial: 'M' },
            { name: 'Bought GOOGL', date: 'Jan 15, 11:00 AM', price: '$2,800.00', category: 'Ads', status: 'Pending', color: 'bg-yellow-500', initial: 'G' },
            { name: 'Sell META', date: 'Jan 18, 04:00 PM', price: '$320.00', category: 'Social', status: 'Success', color: 'bg-blue-500', initial: 'M' },
            { name: 'Bought NFLX', date: 'Jan 20, 09:00 AM', price: '$600.00', category: 'Media', status: 'Failed', color: 'bg-red-700', initial: 'N' }
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
    x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')); $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
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
                
                {{-- TOP CARDS: ASSET OVERVIEW (Sama seperti sebelumnya) --}}
                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    {{-- Loop assets dummy --}}
                    @php
                        $assets = [
                            ['name' => 'AAPL', 'desc' => 'Apple, Inc', 'val' => '$1,232.00', 'perc' => '+ 11.01%', 'up' => true],
                            ['name' => 'PYPL', 'desc' => 'Paypal, Inc', 'val' => '$965.00', 'perc' => '- 9.05%', 'up' => false],
                            ['name' => 'TSLA', 'desc' => 'Tesla, Inc', 'val' => '$1,232.00', 'perc' => '+ 11.01%', 'up' => true],
                            ['name' => 'AMZN', 'desc' => 'Amazon.com', 'val' => '$2,567.99', 'perc' => '+ 11.01%', 'up' => true],
                        ];
                    @endphp
                    @foreach($assets as $asset)
                    <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-boxdark-2 text-xl font-bold">
                                {{ substr($asset['name'], 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ $asset['name'] }}</h4>
                                <p class="text-xs text-bodydark2">{{ $asset['desc'] }}</p>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-between">
                            <span class="text-2xl font-bold text-slate-800 dark:text-white">{{ $asset['val'] }}</span>
                            <span class="flex items-center gap-1 rounded-full px-2 py-1 text-xs font-bold {{ $asset['up'] ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }}">
                                {!! $asset['up'] ? '↑' : '↓' !!} {{ $asset['perc'] }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- CHART SECTION --}}
                <div class="grid grid-cols-12 gap-6 mb-6">
                    <div class="col-span-12 xl:col-span-8">
                        <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                            <h4 class="text-xl font-bold text-slate-800 dark:text-white">Portfolio Performance</h4>
                            <div class="h-[300px] mt-4"><canvas id="portfolioChart"></canvas></div>
                        </div>
                    </div>
                    <div class="col-span-12 xl:col-span-4">
                        <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                            <h4 class="text-xl font-bold text-slate-800 dark:text-white">Dividend</h4>
                            <div class="h-[300px] mt-4"><canvas id="dividendChart"></canvas></div>
                        </div>
                    </div>
                </div>

                {{-- DYNAMIC TABLE WITH PAGINATION (ALpine.js) --}}
                <div class="col-span-12">
                  <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                    <div class="flex flex-col gap-4 px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
                      <h4 class="text-xl font-bold text-slate-800 dark:text-white">Latest Transactions</h4>
                      <div class="relative w-full max-w-[300px]">
                         <span class="absolute left-4 top-1/2 -translate-y-1/2 text-bodydark2">
                           <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" /></svg>
                         </span>
                         <input 
                            x-model="search" 
                            @input="currentPage = 1"
                            type="text" 
                            placeholder="Search data..." 
                            class="w-full rounded-xl border border-stroke bg-slate-50 py-2.5 pl-11 pr-5 text-sm outline-none focus:border-primary focus:bg-white dark:border-strokedark dark:bg-boxdark-2 dark:focus:border-primary transition-all">
                      </div>
                    </div>
                    
                    <div class="max-w-full overflow-x-auto">
                      <table class="w-full table-auto">
                        <thead>
                          <tr class="text-left border-b border-stroke dark:border-strokedark bg-slate-50/50 dark:bg-slate-800/20">
                            <th class="px-6 py-4 text-xs font-bold text-bodydark2 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-xs font-bold text-bodydark2 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-xs font-bold text-bodydark2 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-4 text-xs font-bold text-bodydark2 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-xs font-bold text-bodydark2 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4"></th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-strokedark">
                          <template x-for="(item, index) in pagedData" :key="index">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                              <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                  <div :class="item.color" class="flex h-9 w-9 items-center justify-center rounded-full text-white font-bold text-xs" x-text="item.initial"></div>
                                  <span class="font-bold text-slate-800 dark:text-white" x-text="item.name"></span>
                                </div>
                              </td>
                              <td class="px-6 py-4 text-sm text-slate-600 dark:text-bodydark" x-text="item.date"></td>
                              <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-white" x-text="item.price"></td>
                              <td class="px-6 py-4 text-sm text-slate-600 dark:text-bodydark" x-text="item.category"></td>
                              <td class="px-6 py-4">
                                <span 
                                    :class="{
                                        'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400': item.status === 'Success',
                                        'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400': item.status === 'Pending',
                                        'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400': item.status === 'Failed'
                                    }"
                                    class="inline-flex rounded-lg px-3 py-1 text-xs font-bold uppercase" x-text="item.status"></span>
                              </td>
                              <td class="px-6 py-4 text-right"><button class="text-slate-400 hover:text-primary">•••</button></td>
                            </tr>
                          </template>
                        </tbody>
                      </table>
                    </div>

                    {{-- PAGINATION CONTROLS --}}
                    <div class="flex flex-col items-center justify-between gap-4 border-t border-stroke px-6 py-5 dark:border-strokedark sm:flex-row">
                      <button 
                        @click="currentPage > 1 ? currentPage-- : null"
                        :disabled="currentPage === 1"
                        class="flex items-center gap-2 rounded-lg border border-stroke px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-strokedark dark:text-bodydark2 dark:hover:bg-boxdark-2 transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Previous</span>
                      </button>

                      <div class="flex items-center gap-2">
                        <template x-for="page in totalPages" :key="page">
                            <button 
                                @click="currentPage = page"
                                :class="currentPage === page ? 'bg-primary text-white' : 'text-slate-500 hover:bg-slate-50 dark:text-bodydark2 dark:hover:bg-boxdark-2'"
                                class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-bold transition-all" 
                                x-text="page"></button>
                        </template>
                      </div>

                      <button 
                        @click="currentPage < totalPages ? currentPage++ : null"
                        :disabled="currentPage === totalPages"
                        class="flex items-center gap-2 rounded-lg border border-stroke px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-strokedark dark:text-bodydark2 dark:hover:bg-boxdark-2 transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                        <span>Next</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
          // Chart.js - Portfolio Performance
          const ctxLine = document.getElementById('portfolioChart');
          if (ctxLine) {
            new Chart(ctxLine, {
              type: 'line',
              data: {
                labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
                datasets: [{
                  label: 'Value',
                  data: [30, 32, 31, 33, 29, 31, 32, 31, 33, 34, 38],
                  borderColor: '#3c50e0',
                  backgroundColor: 'rgba(60, 80, 224, 0.1)',
                  fill: true, tension: 0.4, pointRadius: 0
                }]
              },
              options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
          }

          // Chart.js - Dividend
          const ctxBar = document.getElementById('dividendChart');
          if (ctxBar) {
            new Chart(ctxBar, {
              type: 'bar',
              data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                  data: [150, 380, 200, 300, 180, 190],
                  backgroundColor: '#3c50e0', borderRadius: 4, barThickness: 15
                }]
              },
              options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
          }
      });
    </script>
  </body>
</html>