<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard | SparesCan System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
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
                  <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                    <div class="flex items-center gap-4">
                      <h2 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white uppercase">
                        Dashboard Overview
                      </h2>
                      <div class="flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1 border border-emerald-100 dark:border-emerald-800/50">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Online</span>
                      </div>
                    </div>
            
                    <div class="flex flex-col items-end">
                      <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-widest">
                        <svg class="h-3 w-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span id="date-display">...</span>
                      </div>
                      <div class="flex items-center gap-3 font-mono text-3xl font-black text-slate-800 dark:text-white tabular-nums leading-none mt-1">
                        <span id="clock">00:00:00</span>
                      </div>
                    </div>
                  </div>
            
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
                  <div class="col-span-12">
                    @include('partials.metric-group.metric-group-01')
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
    </script>
    <script src="{{ asset('js/index.js') }}"></script>
  </body>
</html>