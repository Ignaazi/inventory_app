<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard | SparesCan System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Chart.js untuk Grafik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: '#3c50e0',
              stroke: '#e2e8f0',
              body: '#64748b',
              bodydark: '#aeaeae',
              bodydark1: '#dee4ee',
              bodydark2: '#8a99af',
              whiten: '#F7F9FC',
              boxdark: '#24303f',
              'boxdark-2': '#1a222c',
            },
            borderRadius: {
              'xl': '1rem',
              '2xl': '1.5rem',
            }
          }
        }
      }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      [x-cloak] { display: none !important; }
      
      .sidebar-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }

      .no-scrollbar::-webkit-scrollbar {
        display: none;
      }
      
      @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
      }
      .animate-bounce-slow {
        animation: bounce-slow 3s infinite ease-in-out;
      }
    </style>
  </head>

  <body
    x-data="{ 'darkMode': false, 'sidebarToggle': false }"
    x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')); $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-boxdark-2 text-bodydark1': darkMode === true}"
    class="bg-[#F1F5F9] font-sans text-slate-600 antialiased"
  >
    @include('partials.preloader')

    <div class="flex h-screen overflow-hidden">
      
      @include('partials.sidebar')

      <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
        
        @include('partials.header')

        <main class="sidebar-transition">
            <div class="mx-auto max-w-screen-2xl p-6 md:p-8 2xl:p-10">
              
              {{-- JIKA DI HALAMAN DASHBOARD UTAMA --}}
              @if(Route::is('dashboard'))
                <div class="mb-10">
                  {{-- HEADER SECTION: RESPONSIVE FIX --}}
                  <div class="flex flex-col gap-4 border-b border-slate-100 dark:border-slate-800 pb-5 sm:flex-row sm:items-center sm:justify-between">
                    
                    {{-- Judul & Status Online --}}
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                      <h2 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white uppercase leading-tight">
                        Dashboard Overview
                      </h2>
                      <div class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1 border border-emerald-100 dark:border-emerald-800/50">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Online</span>
                      </div>
                    </div>
            
                    {{-- Tanggal & Jam --}}
                    <div class="flex flex-col items-start sm:items-end">
                      <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-widest">
                        <svg class="h-3 w-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                        </svg>
                        <span id="date-display">...</span>
                      </div>
                      <div class="flex items-center gap-3 font-mono text-2xl sm:text-3xl font-black text-slate-800 dark:text-white tabular-nums leading-none mt-1">
                        <span id="clock">00:00:00</span>
                      </div>
                    </div>
                  </div>
            
                  {{-- Welcome Message --}}
                  <div class="mt-6 flex items-center gap-4">
                    <div class="text-3xl animate-bounce-slow">👋</div>
                    <div>
                      <h3 class="text-xl font-bold text-slate-800 dark:text-white leading-tight">
                        Selamat Datang, {{ Auth::user()->name }}
                      </h3>
                      <p class="text-sm text-slate-500">
                        Sesi Anda aktif sebagai <span class="font-semibold text-primary uppercase text-[10px] tracking-widest">{{ Auth::user()->role ?? 'Admin' }}</span>
                      </p>
                    </div>
                  </div>
                </div>
            
                <div class="grid grid-cols-12 gap-6">
                  {{-- METRIC GROUP --}}
                  <div class="col-span-12">
                    @include('partials.metric-group.metric-group-01')
                  </div>

                  {{-- SECTION GRAFIK --}}
                  <div class="col-span-12 xl:col-span-8">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="text-xl font-bold text-slate-800 dark:text-white">Stock Movement</h4>
                            <select class="bg-transparent text-sm font-medium outline-none dark:text-white">
                                <option value="">Last 7 Days</option>
                                <option value="">Last 30 Days</option>
                            </select>
                        </div>
                        <div class="h-[300px]">
                            <canvas id="mainChart"></canvas>
                        </div>
                    </div>
                  </div>

                  {{-- SECTION TABLE (Recent Activities) --}}
                  <div class="col-span-12 xl:col-span-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h4 class="mb-4 text-xl font-bold text-slate-800 dark:text-white">Recent Requests</h4>
                        <div class="flow-root">
                            <ul role="list" class="-my-5 divide-y divide-slate-100 dark:divide-slate-800">
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold text-slate-800 dark:text-white">Nozzle Type A-1</p>
                                            <p class="truncate text-xs text-slate-500">Requested by Production</p>
                                        </div>
                                        <div class="inline-flex items-center text-xs font-black text-primary">QTY: 12</div>
                                    </div>
                                </li>
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold text-slate-800 dark:text-white">New Rack Added</p>
                                            <p class="truncate text-xs text-slate-500">Engineering Module</p>
                                        </div>
                                        <div class="inline-flex items-center text-[10px] font-bold text-slate-400">2h ago</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <a href="#" class="mt-6 block w-full rounded-xl border border-slate-100 py-2 text-center text-xs font-bold uppercase tracking-widest text-slate-400 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800">View All History</a>
                    </div>
                  </div>
                </div>

              @else
                {{-- JIKA DI HALAMAN LAIN (Seperti Stock Engineering) --}}
                @yield('content')
              @endif
          
            </div>
          </main>

      </div>
    </div>

    <script>
      function updateTime() {
        const clockElement = document.getElementById('clock');
        const dateElement = document.getElementById('date-display');
        if(!clockElement || !dateElement) return;

        const now = new Date();
        clockElement.textContent = now.toLocaleTimeString('en-GB', { hour12: false });

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateElement.textContent = now.toLocaleDateString('id-ID', options);
      }
      
      setInterval(updateTime, 1000);
      updateTime();

      {{-- Inisialisasi Grafik Bar --}}
      document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('mainChart');
          if (ctx) {
              new Chart(ctx, {
                  type: 'bar',
                  data: {
                      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                      datasets: [{
                          label: 'Nozzle Usage',
                          data: [45, 59, 80, 81, 56, 55, 40],
                          backgroundColor: '#3c50e0',
                          borderRadius: 6,
                          barPercentage: 0.5,
                      }]
                  },
                  options: {
                      responsive: true,
                      maintainAspectRatio: false,
                      plugins: { 
                          legend: { display: false }
                      },
                      scales: { 
                          y: { 
                              beginAtZero: true,
                              grid: {
                                  color: 'rgba(226, 232, 240, 0.1)',
                                  drawBorder: false
                              }
                          }, 
                          x: { 
                              grid: { display: false } 
                          } 
                      }
                  }
              });
          }
      });
    </script>
    <script src="{{ asset('js/index.js') }}"></script>
  </body>
</html>