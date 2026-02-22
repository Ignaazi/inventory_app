<style>
  /* Menjamin ukuran icon tetap konsisten di semua layar */
  .siix-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
  }
  
  /* Animasi Scroll Smooth dan Custom Scrollbar */
  .custom-scroll-container {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
  }

  /* Membuat scrollbar lebih cantik (Modern Look) */
  .custom-scroll-container::-webkit-scrollbar {
    width: 5px;
  }
  .custom-scroll-container::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scroll-container::-webkit-scrollbar-thumb {
    background: #e2e8f0; /* slate-200 */
    border-radius: 10px;
  }
  .dark .custom-scroll-container::-webkit-scrollbar-thumb {
    background: #334155; /* slate-700 */
  }

  /* Responsivitas dasar */
  @media (max-width: 1024px) {
    .sidebar-responsive {
      transform: translateX(-100%);
      position: fixed !important;
      z-index: 99999;
      width: 290px !important; /* Paksa lebar penuh di mobile */
    }
    .sidebar-show-mobile {
      transform: translateX(0);
    }
  }

  /* Style untuk floating menu saat sidebar mini */
  .sidebar-mini-floating {
    position: absolute !important;
    left: 80px;
    width: 190px;
    background: white;
    box-shadow: 6px 4px 15px rgba(0,0,0,0.08);
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    padding: 8px !important;
    margin-left: 0 !important;
    z-index: 9999;
  }
  .dark .sidebar-mini-floating {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 6px 4px 15px rgba(0,0,0,0.4);
  }

  /* Menambahkan style cursor untuk menu yang disabled saat mini sidebar */
  .cursor-not-allowed-mini {
    cursor: default !important;
  }
</style>

<div 
  x-show="sidebarToggle && window.innerWidth < 1024" 
  @click="sidebarToggle = false" 
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 z-[99998] bg-black/50 lg:hidden"
></div>

<aside
  :class="sidebarToggle ? (window.innerWidth < 1024 ? 'sidebar-show-mobile' : 'w-[90px]') : 'w-[290px]'"
  class="sidebar-responsive fixed left-0 top-0 z-[99999] flex h-screen flex-col overflow-y-hidden border-r border-gray-200 bg-white transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-slate-900 lg:static lg:translate-x-0"
>
  <div
    :class="(sidebarToggle && window.innerWidth >= 1024) ? 'justify-center' : 'justify-between'"
    class="flex items-center px-6 py-8 border-b border-gray-50 dark:border-slate-800"
  >
    <a href="/admin" class="flex items-center gap-3">
      <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-600 shadow-sm">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
      </div>
      <span x-show="!sidebarToggle || window.innerWidth < 1024" class="text-xl font-black tracking-tighter text-slate-800 dark:text-slate-100 uppercase">
        SIIX SYSTEM
      </span>
    </a>

    <button @click="sidebarToggle = false" class="lg:hidden text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>

  <div class="custom-scroll-container flex flex-col overflow-y-auto duration-300 ease-linear grow">
    <nav class="px-4 py-6">
      
      <div class="mb-8">
        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="/admin" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all {{ request()->is('admin') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="/admin/users" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all {{ request()->is('admin/users*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">User Management</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="mb-8" x-data="{ openEngTrans: false }">
        <h3 x-show="!sidebarToggle || window.innerWidth < 1024" class="mb-4 ml-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
          ENGINEERING
        </h3>
        <div x-show="sidebarToggle && window.innerWidth >= 1024" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-bold">ENG</div>

        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="/eng/overview" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Engineering Overview</span>
            </a>
          </li>
          <li>
            <a href="/eng/list-sparepart" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">List Sparepart Eng</span>
            </a>
          </li>
          <li>
            <a href="{{ route('stock.eng.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all {{ request()->is('stock-engineering*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Stock Eng</span>
            </a>
          </li>
          
          <li class="relative">
            <button 
              @click="openEngTrans = !openEngTrans" 
              @click.away="if(sidebarToggle && window.innerWidth >= 1024) openEngTrans = false"
              class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all"
            >
              <div class="flex items-center gap-3">
                <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span x-show="!sidebarToggle || window.innerWidth < 1024">Transaction</span>
              </div>
              <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openEngTrans ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul 
              x-show="openEngTrans" 
              x-transition 
              :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'"
              class="flex flex-col gap-1"
            >
              <li>
                <a href="/eng/in" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                  <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                  <span>In</span>
                </a>
              </li>
              <li>
                <a href="/eng/out" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                  <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                  <span>Out</span>
                </a>
              </li>
              <li>
                <a href="/eng/disposal" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                  <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  <span>Disposal</span>
                </a>
              </li>
            </ul>
          </li>

          <li>
            <a href="/eng/barcode-parsing" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Barcode Parsing</span>
            </a>
          </li>
          <li>
            <a href="/eng/approval" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Approval</span>
            </a>
          </li>
          <li>
            <a href="/eng/purchase-request" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Purchase Request</span>
            </a>
          </li>
          <li>
            <a href="/eng/report" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Report</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="mb-8" x-data="{ openProdTrans: false }">
        <h3 x-show="!sidebarToggle || window.innerWidth < 1024" class="mb-4 ml-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
          PRODUCTION
        </h3>
        <div x-show="sidebarToggle && window.innerWidth >= 1024" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-bold">PROD</div>

        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="/prod/overview" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Production Overview</span>
            </a>
          </li>
          <li>
            <a href="/prod/list-sparepart" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">List Sparepart Prod</span>
            </a>
          </li>
          <li>
            <a href="/prod/stock" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Stock Prod</span>
            </a>
          </li>
          <li>
            <a href="/prod/request" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Request</span>
            </a>
          </li>
          
          <li class="relative">
            <button 
              @click="openProdTrans = !openProdTrans" 
              @click.away="if(sidebarToggle && window.innerWidth >= 1024) openProdTrans = false"
              class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all"
            >
              <div class="flex items-center gap-3">
                <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                <span x-show="!sidebarToggle || window.innerWidth < 1024">Transaction</span>
              </div>
              <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openProdTrans ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul 
              x-show="openProdTrans" 
              x-transition 
              :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'"
              class="flex flex-col gap-1"
            >
              <li>
                <a href="/prod/in" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                  <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                  <span>In</span>
                </a>
              </li>
              <li>
                <a href="/prod/out" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                  <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                  <span>Out</span>
                </a>
              </li>
            </ul>
          </li>

          <li>
            <a href="/prod/history" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">History</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="mb-8">
        <h3 x-show="!sidebarToggle || window.innerWidth < 1024" class="mb-4 ml-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
          COSTING
        </h3>
        <div x-show="sidebarToggle && window.innerWidth >= 1024" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-bold">CST</div>

        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="/costing/overview" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Costing Overview</span>
            </a>
          </li>
          <li>
            <a href="/costing/incoming-pr" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Incoming PR</span>
            </a>
          </li>
          <li>
            <a href="/costing/material-received" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Material Received</span>
            </a>
          </li>
          <li>
            <a href="/costing/history" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">History</span>
            </a>
          </li>
          <li>
            <a href="/costing/report" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Report</span>
            </a>
          </li>
        </ul>
      </div>

    </nav>
  </div>
</aside>