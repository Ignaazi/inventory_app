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
      z-index: 9999;
    }
    .sidebar-show-mobile {
      transform: translateX(0);
    }
  }
</style>

<aside
  :class="sidebarToggle ? 'w-[90px] sidebar-show-mobile' : 'w-[290px]'"
  class="sidebar-responsive fixed left-0 top-0 z-9999 flex h-screen flex-col overflow-y-hidden border-r border-gray-200 bg-white transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-slate-900 lg:static"
>
  <div
    :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center px-6 py-8 border-b border-gray-50 dark:border-slate-800"
  >
    <a href="/admin" class="flex items-center gap-3">
      <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-600 shadow-sm">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
      </div>
      <span x-show="!sidebarToggle" class="text-xl font-black tracking-tighter text-slate-800 dark:text-slate-100 uppercase">
        SIIX SYSTEM
      </span>
    </a>
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
              <span x-show="!sidebarToggle">Main Dashboard</span>
            </a>
          </li>
          
          <li>
            <a href="/admin/users" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all {{ request()->is('admin/users*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              <span x-show="!sidebarToggle">Management User</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="mb-8">
        <h3 x-show="!sidebarToggle" class="mb-4 ml-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
          ENGINEERING
        </h3>
        <div x-show="sidebarToggle" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-bold">ENG</div>

        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="{{ route('stock.eng.index') }}" 
               class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all {{ request()->is('stock-engineering*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
              <span x-show="!sidebarToggle">Stock Nozzle ENG</span>
            </a>
          </li>
          <li>
            <a href="/eng/request" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span x-show="!sidebarToggle">Request Prod</span>
            </a>
          </li>
          <li>
            <a href="/eng/in-out" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
              <span x-show="!sidebarToggle">In / Out</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="mb-8">
        <h3 x-show="!sidebarToggle" class="mb-4 ml-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
          PRODUCTION
        </h3>
        <div x-show="sidebarToggle" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-bold">PROD</div>

        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="/prod/stock" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
              <span x-show="!sidebarToggle">Stock Nozzle PROD</span>
            </a>
          </li>
          <li>
            <a href="/prod/request" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
              <span x-show="!sidebarToggle">Request Nozzle</span>
            </a>
          </li>
          <li>
            <a href="/prod/in-out" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 transition-all">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"/></svg>
              <span x-show="!sidebarToggle">In / Out</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="mb-8">
        <h3 x-show="!sidebarToggle" class="mb-4 ml-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
          COSTING
        </h3>
        <div x-show="sidebarToggle" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-bold">CST</div>

        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="/costing/purchase-request" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all {{ request()->is('costing/purchase-request*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span x-show="!sidebarToggle">Purchase Order</span>
            </a>
          </li>
        </ul>
      </div>

    </nav>
  </div>
</aside>