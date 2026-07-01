<!-- Style Tag & Nunito Dropdown Reference Fix -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');

  header, 
  header *,
  [x-show="profileModalOpen"],
  [x-show="profileModalOpen"] * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  [x-cloak] { 
    display: none !important; 
  }

  /* Custom Clean Dropdown matching image_093e3e.png */
  .custom-dropdown {
    min-width: 260px;
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  }
  .dark .custom-dropdown {
    background-color: #1e293b;
    border: 1px solid #334155;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
  }

  .custom-dropdown-item {
    color: #475569;
    font-weight: 500;
    transition: all 0.15s ease;
  }
  .dark .custom-dropdown-item {
    color: #cbd5e1;
  }
  .custom-dropdown-item:hover {
    background-color: #f8fafc;
    color: #1e40af;
  }
  .dark .custom-dropdown-item:hover {
    background-color: #334155;
    color: #f8fafc;
  }
</style>

<!-- Inline Script Pencegah Kedip -->
<script>
  (function () {
    const isDark = localStorage.getItem('darkMode') === 'true' || 
                   (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
    if (isDark) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  })();
</script>

<header
  x-data="{ dropdownOpen: false, notifyOpen: false, profileModalOpen: false }"
  class="sticky top-0 z-50 flex w-full border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
>
  <div class="flex w-full items-center justify-between px-4 py-3 sm:px-5 lg:px-9">
    
    <!-- LEFT CONTENT: SIDEBAR TOGGLE & SEARCH -->
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

    <!-- RIGHT CONTENT: THEME, NOTIFICATION, & USER DROP DOWN -->
    <div class="flex items-center gap-3">
      
      <!-- DARK MODE TOGGLE -->
      <button
        @click.prevent="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); if(darkMode){ document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') }"
        class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800"
      >
        <svg x-show="!darkMode" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        <svg x-show="darkMode" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
      </button>

      <!-- NOTIFICATIONS -->
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
          x-cloak
          x-transition
          class="absolute right-0 mt-3 w-80 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900"
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

      <!-- HEADER USER BLOCK -->
      <div class="relative ml-1" @click.outside="dropdownOpen = false">
        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3 group">
          <div class="h-10 w-10 overflow-hidden rounded-full ring-2 ring-blue-500/10 shadow-sm border border-gray-200 dark:border-gray-700 transition-transform group-hover:scale-105">
            <img 
              id="header-profile-img"
              src="{{ Auth::user()->profile_photo_path ? asset('storage/'.Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff' }}" 
              alt="User" class="h-full w-full object-cover"
            >
          </div>
          <div class="hidden text-left lg:block">
            <!-- Teks nama hitam bersih (text-slate-900) & tidak bold (font-normal) -->
            <p class="text-sm font-normal text-slate-900 dark:text-gray-100 leading-tight">{{ Auth::user()->name }}</p>
            <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wide mt-0.5">{{ Auth::user()->role }}</p>
          </div>
          <svg :class="dropdownOpen && 'rotate-180'" class="h-4 w-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>

        <!-- RE-DESIGNED DROPDOWN (Berdasarkan sample image_093e3e.png) -->
        <div
          x-show="dropdownOpen"
          x-cloak
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-100"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          class="custom-dropdown absolute right-0 mt-2 w-64 overflow-hidden rounded-2xl"
        >
          <!-- User Profile Header Info Block -->
          <div class="pt-5 px-5 pb-4">
            <!-- Teks nama hitam bersih (text-slate-900) & tidak bold (font-normal) -->
            <h5 class="text-base font-normal text-slate-900 dark:text-white leading-snug">{{ Auth::user()->name }}</h5>
            <!-- Pemisah diganti menggunakan garis tegak (|) -->
            <p class="text-xs text-slate-400 dark:text-slate-400 font-medium mt-1 truncate">
              {{ Auth::user()->role }} <span class="mx-0.5 text-slate-300 dark:text-slate-600">|</span> {{ Auth::user()->nim }}
            </p>
          </div>
          
          <!-- Dropdown Navigation Items List -->
          <div class="px-2 pb-2 space-y-0.5">
            <button 
              @click="profileModalOpen = true; dropdownOpen = false" 
              class="custom-dropdown-item flex w-full items-center gap-3.5 rounded-xl px-3 py-2.5 text-sm"
            >
              <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span>My Profile</span>
            </button>
          </div>

          <!-- Sign Out Item -->
          <div class="px-2 py-1.5 border-t border-slate-100 dark:border-slate-800">
            <button 
              @click.prevent="document.getElementById('logout-form').submit();"
              class="custom-dropdown-item flex w-full items-center gap-3.5 rounded-xl px-3 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20"
            >
              <svg class="h-5 w-5 text-red-500/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              <span>Sign out</span>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- PROFILE MODAL COMPONENT -->
  <div 
    x-show="profileModalOpen" 
    class="fixed inset-0 z-[1000] flex items-center justify-center bg-slate-900/60 backdrop-blur-md px-4"
    x-cloak
  >
    <div @click.outside="profileModalOpen = false" class="w-full max-w-sm rounded-[2.5rem] bg-white dark:bg-slate-900 shadow-2xl overflow-hidden border border-white/20">
      <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700 relative">
          <button @click="profileModalOpen = false" class="absolute top-4 right-4 text-white/80 hover:text-white">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
      </div>

      <div class="px-8 pb-8">
          <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="relative -mt-12 flex flex-col items-center">
              @csrf
              @method('PUT')
              
              <input type="hidden" name="remove_photo" id="remove_photo_input" value="0">

              <div class="relative group">
                  <div class="h-28 w-28 rounded-full ring-4 ring-white dark:ring-slate-900 overflow-hidden bg-slate-200 shadow-xl">
                      <img id="profile-preview" 
                           src="{{ Auth::user()->profile_photo_path ? asset('storage/'.Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff' }}" 
                           class="h-full w-full object-cover">
                  </div>
                  
                  <label for="photo-upload" class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer">
                      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                  </label>

                  @if(Auth::user()->profile_photo_path)
                  <button type="button" id="delete-photo-btn" onclick="removePhotoAction()" class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white border-2 border-white dark:border-slate-900 hover:bg-red-600 shadow-lg transition-all active:scale-90">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                  @endif

                  <input id="photo-upload" type="file" name="photo" class="hidden" onchange="previewImage(event)">
              </div>

              <div class="mt-4 text-center">
                  <h3 class="text-xl font-normal text-slate-800 dark:text-white tracking-tight">{{ Auth::user()->name }}</h3>
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ Auth::user()->role }}</p>
              </div>

              <div class="mt-8 w-full space-y-4">
                  <div class="relative">
                      <label class="absolute -top-2 left-4 bg-white dark:bg-slate-900 px-1 text-[10px] font-black text-indigo-600 uppercase tracking-widest">Full Name</label>
                      <input type="text" name="name" value="{{ Auth::user()->name }}" 
                          class="w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-transparent py-4 px-5 text-sm font-bold text-slate-800 dark:text-white outline-none focus:border-indigo-600 transition-all">
                  </div>
                  <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 text-center">
                      <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">NIM / Identifier</p>
                      <p class="text-sm font-mono font-bold text-slate-600 dark:text-slate-300 mt-1">{{ Auth::user()->nim }}</p>
                  </div>
              </div>

              <div class="mt-8 flex w-full gap-3">
                  <button type="submit" class="flex-1 rounded-2xl bg-indigo-600 py-4 text-xs font-black text-white shadow-lg shadow-indigo-200 dark:shadow-none hover:bg-indigo-700 transition-all active:scale-95 uppercase tracking-widest">
                      Update Profile
                  </button>
              </div>
          </form>
      </div>
    </div>
  </div>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
</header>

<script>
  function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
      const output = document.getElementById('profile-preview');
      output.src = reader.result;
      document.getElementById('remove_photo_input').value = '0';
    };
    reader.readAsDataURL(event.target.files[0]);
  }

  function removePhotoAction() {
    document.getElementById('remove_photo_input').value = '1';
    const name = "{{ Auth::user()->name }}";
    const placeholder = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=6366f1&color=fff`;
    document.getElementById('profile-preview').src = placeholder;
    document.getElementById('photo-upload').value = '';
    const btn = document.getElementById('delete-photo-btn');
    if(btn) btn.style.display = 'none';
  }
</script>