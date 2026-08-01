<link rel="stylesheet" href="{{ asset('css/partials/sidebar.css') }}">

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
  <!-- HEADER LOGO AREA -->
  <div
    :class="(sidebarToggle && window.innerWidth >= 1024) ? 'justify-center px-0' : 'justify-between px-6'"
    class="flex items-center h-[65  px] border-b border-gray-50 dark:border-slate-800 transition-all duration-300 w-full"
  >
    <a href="/admin" class="flex items-center justify-center w-full">
      <div 
        class="logo-wrapper"
        :class="(!sidebarToggle || window.innerWidth < 1024) ? 'logo-expanded' : 'logo-collapsed'"
      >
        <!-- Penambahan style pelapis anti-melar -->
        <img 
          src="{{ asset('images/logosidebar.png') }}" 
          alt="SIIX Logo" 
          class="object-contain w-full h-full"
          style="max-width: 100%; object-fit: contain;"
        >
      </div>
    </a>

    <button type="button" @click="sidebarToggle = false" class="absolute right-4 lg:hidden text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>

  <div class="custom-scroll-container flex flex-col overflow-y-auto duration-300 ease-linear grow">
    <nav class="px-4 py-6">
      
      <!-- ================= ADMIN MENU ================= -->
      @if(auth()->user()->role === 'admin')
      <div class="mb-8">
        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="/admin" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->is('admin') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="/admin/users" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->is('admin/users*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">User Management</span>
            </a>
          </li>
        </ul>
      </div>
      @endif

      <!-- ================= ENGINEERING MENU ================= -->
      @if(in_array(auth()->user()->role, ['admin', 'engineering']))
      <div class="mb-8" x-data="{ 
          openMenu: '{{ request()->routeIs('stock_eng.transaction.*') ? 'trans' : (request()->routeIs('barcode.*') ? 'barcode' : (request()->routeIs('eng.approval', 'approval.history') ? 'approval' : (request()->is('*purchase-request*') ? 'pr' : (request()->routeIs('eng.material.receiving.*', 'eng.material.receipt.*') ? 'mat_received' : 'none')))) }}'
        }">
        <h3 x-show="!sidebarToggle || window.innerWidth < 1024" class="mb-4 ml-4 text-[10px] font-extrabold uppercase tracking-[0.15em] text-slate-400 dark:text-slate-500">
          ENGINEERING
        </h3>
        <div x-show="sidebarToggle && window.innerWidth >= 1024" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-extrabold">ENG</div>
  
        <ul class="flex flex-col gap-1.5">
          <li>
            <a href="/eng/overview" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->is('*eng/overview*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Engineering Overview</span>
            </a>
          </li>
          <li>
            <a href="/eng/list-sparepart" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->is('*eng/list-sparepart*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">List Sparepart Eng</span>
            </a>
          </li>
          <li>
            <a href="{{ route('stock.eng.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('stock.eng.index') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
              <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
              <span x-show="!sidebarToggle || window.innerWidth < 1024">Stock Eng</span>
            </a>
          </li>
          
          <!-- Dropdown: Transaction -->
          <li class="relative">
            <button 
              type="button"
              @click.stop="openMenu = (openMenu === 'trans' ? 'none' : 'trans')" 
              class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('stock_eng.transaction.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
            >
              <div class="flex items-center gap-3">
                <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span x-show="!sidebarToggle || window.innerWidth < 1024">Transaction</span>
              </div>
              <div class="flex items-center gap-2">
                <span x-show="!sidebarToggle || window.innerWidth < 1024" class="flex h-[18px] w-[18px] items-center justify-center rounded bg-[#ef4444] text-[10px] font-bold text-white shadow-sm">
                  {{ $notifEngTrans ?? '3' }}
                </span>
                <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenu === 'trans' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </button>
            
            <div x-show="openMenu === 'trans'" x-collapse class="overflow-hidden">
              <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
                <li>
                  <a href="{{ route('stock_eng.transaction.in') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('stock_eng.transaction.in') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('stock_eng.transaction.in') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>In</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('stock_eng.transaction.out') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('stock_eng.transaction.out') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('stock_eng.transaction.out') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>Out</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('stock_eng.transaction.return') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('stock_eng.transaction.return') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('stock_eng.transaction.return') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>Return</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('stock_eng.transaction.disposal') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('stock_eng.transaction.disposal') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('stock_eng.transaction.disposal') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>Disposal</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
  
          <!-- Dropdown: Barcode Parsing -->
          <li class="relative">
            <button 
              type="button"
              @click.stop="openMenu = (openMenu === 'barcode' ? 'none' : 'barcode')" 
              class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('barcode.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
            >
              <div class="flex items-center gap-3">
                <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-show="!sidebarToggle || window.innerWidth < 1024">Barcode Parsing</span>
              </div>
              <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenu === 'barcode' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openMenu === 'barcode'" x-collapse class="overflow-hidden">
              <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
                <li>
                  <a href="{{ route('barcode.parsing') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('barcode.parsing') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('barcode.parsing') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>Create Barcode</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('barcode.db') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('barcode.db') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('barcode.db') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>DB Barcode</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('barcode.type') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('barcode.type') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('barcode.type') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>Type Barcode</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
  
          <!-- Dropdown: Approval -->
          <li class="relative">
            <button 
              type="button"
              @click.stop="openMenu = (openMenu === 'approval' ? 'none' : 'approval')" 
              class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('eng.approval', 'approval.history') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
            >
              <div class="flex items-center gap-3">
                <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-show="!sidebarToggle || window.innerWidth < 1024">Approval</span>
              </div>
              <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenu === 'approval' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openMenu === 'approval'" x-collapse class="overflow-hidden">
              <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
                <li>
                  <a href="{{ route('eng.approval') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('eng.approval') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('eng.approval') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>List Approval</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('approval.history') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('approval.history') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('approval.history') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>History Approval</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
  
         <!-- Dropdown: Purchase Request -->
<li class="relative">
  <button 
    type="button"
    @click.stop="openMenu = (openMenu === 'pr' ? 'none' : 'pr')" 
    class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->is('*purchase-request*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
  >
    <div class="flex items-center gap-3">
      <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      <span x-show="!sidebarToggle || window.innerWidth < 1024">Purchase Request</span>
    </div>
    <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenu === 'pr' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
  </button>
  
  <div x-show="openMenu === 'pr'" x-collapse class="overflow-hidden">
    <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
      
      <!-- 1. Create PR -->
      <li>
        <a href="/eng/purchase-request" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->is('eng/purchase-request') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
          <span class="w-1.5 h-1.5 rounded-full {{ request()->is('eng/purchase-request') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
          <span>Create PR</span>
        </a>
      </li>

      <!-- 2. Verify PR (Menu baru untuk Checker/Admin memproses data 'pending') -->
      <li>
        <a href="{{ route('purchase.request.list') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('purchase.request.list') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
          <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('purchase.request.list') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
          <span>List Purchase Request</span>
        </a>
      </li>

      <!-- 3. History PR -->
      <li>
        <a href="{{ route('purchase.request.history') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('purchase.request.history') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
          <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('purchase.request.history') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
          <span>History PR</span>
        </a>
      </li>

    </ul>
  </div>
</li>

          <!-- Dropdown: Material Received -->
          <li class="relative">
            <button 
              type="button"
              @click.stop="openMenu = (openMenu === 'mat_received' ? 'none' : 'mat_received')" 
              class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('eng.material.receiving.*', 'eng.material.receipt.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
            >
              <div class="flex items-center gap-3">
                <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <span x-show="!sidebarToggle || window.innerWidth < 1024">Material Received</span>
              </div>
              <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenu === 'mat_received' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openMenu === 'mat_received'" x-collapse class="overflow-hidden">
              <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
                <li>
                  <a href="{{ route('eng.material.receiving.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('eng.material.receiving.index', 'eng.material.receipt.index') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('eng.material.receiving.index', 'eng.material.receipt.index') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>List Material Received</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('eng.material.receiving.history') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('eng.material.receiving.history', 'eng.material.receipt.history') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('eng.material.receiving.history', 'eng.material.receipt.history') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                    <span>History Material Received</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
      @endif

      <!-- ================= PRODUCTION MENU ================= -->
      @if(in_array(auth()->user()->role, ['admin', 'production']))
      <div class="mb-8" x-data="{ 
          openMenuProd: '{{ request()->routeIs('prod.transaction.*') ? 'trans' : (request()->routeIs('prod.request.*') ? 'request' : 'none') }}'
        }">
          <h3 x-show="!sidebarToggle || window.innerWidth < 1024" class="mb-4 ml-4 text-[10px] font-extrabold uppercase tracking-[0.15em] text-slate-400 dark:text-slate-500">
            PRODUCTION
          </h3>
          <div x-show="sidebarToggle && window.innerWidth >= 1024" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-extrabold">PROD</div>
        
          <ul class="flex flex-col gap-1.5">
            <li>
              <a href="/prod/overview" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->is('*prod/overview*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span x-show="!sidebarToggle || window.innerWidth < 1024">Production Overview</span>
              </a>
            </li>
            <li>
              <a href="{{ route('prod.line.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('prod.line.index') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                  <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  <span x-show="!sidebarToggle || window.innerWidth < 1024">List Line Prod</span>
              </a>
            </li>
            <li>
              <a href="/prod/stock" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->is('*prod/stock*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span x-show="!sidebarToggle || window.innerWidth < 1024">Stock Prod</span>
              </a>
            </li>
        
            <!-- Dropdown: Request Nozzle -->
            <li class="relative">
              <button 
                type="button"
                @click.stop="openMenuProd = (openMenuProd === 'request' ? 'none' : 'request')" 
                class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('prod.request.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
              >
                <div class="flex items-center gap-3">
                  <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                  <span x-show="!sidebarToggle || window.innerWidth < 1024">Request Nozzle</span>
                </div>
                <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenuProd === 'request' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div x-show="openMenuProd === 'request'" x-collapse class="overflow-hidden">
                <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
                  <li>
                    <a href="{{ route('prod.request.create') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('prod.request.create') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                      <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('prod.request.create') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                      <span>Create Request</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('prod.request.list') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('prod.request.list') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                      <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('prod.request.list') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                      <span>List Request</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>
            
            <!-- Dropdown: Transaction -->
            <li class="relative">
              <button 
                type="button"
                @click.stop="openMenuProd = (openMenuProd === 'trans' ? 'none' : 'trans')" 
                class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('prod.transaction.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
              >
                <div class="flex items-center gap-3">
                  <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                  <span x-show="!sidebarToggle || window.innerWidth < 1024">Transaction</span>
                </div>
                <div class="flex items-center gap-2">
                  <span x-show="!sidebarToggle || window.innerWidth < 1024" class="flex h-[18px] w-[18px] items-center justify-center rounded bg-[#ef4444] text-[10px] font-bold text-white shadow-sm">
                    {{ $notifProdTrans ?? '1' }}
                  </span>
                  <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenuProd === 'trans' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
              </button>
              <div x-show="openMenuProd === 'trans'" x-collapse class="overflow-hidden">
                <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
                  <li>
                    <a href="{{ route('prod.transaction.in') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('prod.transaction.in') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                      <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('prod.transaction.in') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                      <span>In</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('prod.transaction.out') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('prod.transaction.out') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                      <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('prod.transaction.out') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
                      <span>Out</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      @endif


        <!-- ================= COSTING MENU ================= -->
@if(in_array(auth()->user()->role, ['admin', 'costing']))
<div class="mb-8" x-data="{ openMenuCosting: '{{ request()->routeIs('costing.pr.*') ? 'pr' : (request()->routeIs('costing.material.*') ? 'material' : 'none') }}' }">
  <h3 x-show="!sidebarToggle || window.innerWidth < 1024" class="mb-4 ml-4 text-[10px] font-extrabold uppercase tracking-[0.15em] text-slate-400 dark:text-slate-500">
    COSTING
  </h3>
  <div x-show="sidebarToggle && window.innerWidth >= 1024" class="mb-4 flex justify-center text-indigo-500 text-[10px] font-extrabold">CST</div>

  <ul class="flex flex-col gap-1.5">
    
    {{-- 1. COSTING OVERVIEW (SINGLE LINK) --}}
    <li>
      <a href="{{ route('costing.overview') }}" class="group flex items-center gap-3 rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('costing.overview') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
        <span x-show="!sidebarToggle || window.innerWidth < 1024">Costing Overview</span>
      </a>
    </li>
    
    {{-- 2. APPROVE PR (DROPDOWN MENU) --}}
    <li class="relative">
      <button 
        type="button"
        @click.stop="openMenuCosting = (openMenuCosting === 'pr' ? 'none' : 'pr')" 
        class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('costing.pr.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
      >
        <div class="flex items-center gap-3">
          <!-- Icon Baru: Clipboard Check (Approval) -->
          <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          <span x-show="!sidebarToggle || window.innerWidth < 1024">Approve PR</span>
        </div>
        <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenuCosting === 'pr' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </button>
      
      <div x-show="openMenuCosting === 'pr'" x-collapse class="overflow-hidden">
        <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
          <li>
            <a href="{{ route('costing.pr.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('costing.pr.index') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
              <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('costing.pr.index') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
              <span>List Approve PR</span>
            </a>
          </li>
          <li>
            {{-- Note: Pastikan route costing.pr.history sudah terdaftar di web.php lu --}}
            <a href="{{ route('costing.pr.history') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('costing.pr.history') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
              <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('costing.pr.history') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
              <span>History PR</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    {{-- 3. MATERIAL RECEIVED (DROPDOWN MENU) --}}
    <li class="relative">
      <button 
        type="button"
        @click.stop="openMenuCosting = (openMenuCosting === 'material' ? 'none' : 'material')" 
        class="w-full group flex items-center justify-between rounded-lg px-4 py-2.5 text-[13px] font-bold transition-all {{ request()->routeIs('costing.material.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800' }}"
      >
        <div class="flex items-center gap-3">
          <!-- Icon Baru: Box 3D / Package (Fisik Material) -->
          <svg class="siix-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          <span x-show="!sidebarToggle || window.innerWidth < 1024">Material Received</span>
        </div>
        <svg x-show="!sidebarToggle || window.innerWidth < 1024" :class="openMenuCosting === 'material' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </button>
      
      <div x-show="openMenuCosting === 'material'" x-collapse class="overflow-hidden">
        <ul :class="(sidebarToggle && window.innerWidth >= 1024) ? 'sidebar-mini-floating' : 'mt-1 ml-9 border-l border-slate-100 dark:border-slate-800'" class="flex flex-col gap-1 py-1">
          <li>
            <a href="{{ route('costing.material.received') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('costing.material.received') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
              <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('costing.material.received') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
              <span>Material Received Form</span>
            </a>
          </li>
          <li>
            <a href="{{ route('costing.material.list') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-[13px] font-semibold transition-all {{ request()->routeIs('costing.material.list') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
              <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('costing.material.list') ? 'bg-indigo-600' : 'bg-slate-300' }}"></span>
              <span>List Material Received</span>
            </a>
          </li>
        </ul>
      </div>
    </li>
    
  </ul>
</div>
@endif

    </nav>
  </div>
</aside>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const scrollContainer = document.querySelector('.custom-scroll-container');
    
    if (scrollContainer) {
      const savedScrollPosition = localStorage.getItem('sidebarScrollPosition');
      if (savedScrollPosition) {
        scrollContainer.scrollTop = savedScrollPosition;
      }

      scrollContainer.addEventListener('scroll', function () {
        localStorage.setItem('sidebarScrollPosition', scrollContainer.scrollTop);
      });
    }
  });
</script>