<header
  x-data="{ dropdownOpen: false, notifyOpen: false }"
  class="sticky top-0 z-999 flex w-full border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
>
  <div class="flex w-full items-center justify-between px-4 py-3 sm:px-5 lg:px-9">
    
    <div class="flex items-center gap-5">
      <button
        @click.stop="sidebarToggle = !sidebarToggle"
        class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800"
      >
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>

      <div class="hidden sm:block">
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </span>
          <input 
            type="text" 
            placeholder="Search or type command..." 
            class="w-64 rounded-2xl border border-gray-200 bg-gray-50 py-2 pl-11 pr-4 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white lg:w-[440px]"
          >
          <span class="absolute inset-y-0 right-3 flex items-center">
            <kbd class="hidden rounded border border-gray-200 bg-white px-1.5 py-0.5 text-[10px] font-medium text-gray-400 sm:inline-block dark:border-gray-700 dark:bg-gray-800">⌘ K</kbd>
          </span>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-3">
      
      <button
        @click.prevent="darkMode = !darkMode"
        class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800"
      >
        <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
      </button>

      <div class="relative" @click.outside="notifyOpen = false">
        <button
          @click="notifyOpen = !notifyOpen"
          class="relative flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800"
        >
          <span class="absolute top-2 right-2 flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
          </span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        </button>

        <div
          x-show="notifyOpen"
          x-transition
          class="absolute right-0 mt-3 w-80 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900"
          style="display: none;"
        >
          <div class="p-4 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 dark:text-white text-sm">Notifications</h3>
            <span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full font-bold dark:bg-blue-900/30">New</span>
          </div>
          <div class="max-h-64 overflow-y-auto">
             <div class="p-4 border-b border-gray-50 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <p class="text-xs font-bold text-gray-800 dark:text-white">Engineering Stock Alert</p>
                <p class="text-[10px] text-gray-500 mt-0.5">Stock below threshold (10 pcs remaining).</p>
             </div>
          </div>
        </div>
      </div>

      <div class="relative ml-1" @click.outside="dropdownOpen = false">
        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3">
          {{-- FOTO PROFIL DINAMIS DENGAN CACHE BUSTER --}}
          <div class="h-11 w-11 overflow-hidden rounded-full ring-2 ring-blue-500/10 shadow-sm border border-gray-200 dark:border-gray-700">
            <img 
              src="{{ Auth::user()->profile_photo_path ? asset('storage/'.Auth::user()->profile_photo_path) . '?v=' . time() : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff' }}" 
              alt="User" class="h-full w-full object-cover"
            >
          </div>
          <div class="hidden text-right lg:block">
            <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight">{{ Auth::user()->name }}</p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">{{ Auth::user()->role }}</p>
          </div>
          <svg :class="dropdownOpen && 'rotate-180'" class="h-4 w-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>

        <div
          x-show="dropdownOpen"
          x-transition
          class="absolute right-0 mt-3 w-64 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900"
          style="display: none;"
        >
          <div class="p-4 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
            <p class="text-[11px] text-gray-500 truncate font-mono">NIM: {{ Auth::user()->nim }}</p>
          </div>
          
          <div class="p-2">
            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800 transition-all">
              <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              My Profile
            </a>
          </div>

          <div class="p-2 border-t border-gray-50 dark:border-gray-800">
            <button 
              @click.prevent="document.getElementById('logout-form').submit();"
              class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
              Sign out
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
</header>