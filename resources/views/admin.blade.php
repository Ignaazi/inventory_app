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
                        
                        <!-- Main Large KPI Grid Section (Sejajar Sempurna dari Kiri ke Kanan) -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            
                            <!-- 1. ENGINEERING CARD -->
                            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#ff869a] to-[#ff6078] p-6 shadow-lg text-white border-0 min-h-[160px] flex flex-col justify-between">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Engineering</span>
                                        <span class="text-[11px] text-white/70 block font-medium mt-0.5">Total engineering stock balance</span>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                        <i data-feather="tool" class="w-5 h-5 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-4">
                                    <h4 class="text-3xl font-black tracking-tight">1,240 <span class="text-sm font-medium opacity-80 ml-1">Pcs</span></h4>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black bg-white/25">Engineering</span>
                                </div>
                            </div>

                            <!-- 2. PRODUCTION CARD -->
                            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#52b1ff] to-[#268fff] p-6 shadow-lg text-white border-0 min-h-[160px] flex flex-col justify-between">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Production</span>
                                        <span class="text-[11px] text-white/70 block font-medium mt-0.5">Total production stock balance</span>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                        <i data-feather="package" class="w-5 h-5 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-4">
                                    <h4 class="text-3xl font-black tracking-tight">900 <span class="text-sm font-medium opacity-80 ml-1">Pcs</span></h4>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black bg-white/25">Production</span>
                                </div>
                            </div>

                            <!-- 3. COSTING CARD -->
                            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#54e3be] to-[#29cc97] p-6 shadow-lg text-white border-0 min-h-[160px] flex flex-col justify-between">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Costing</span>
                                        <span class="text-[11px] text-white/70 block font-medium mt-0.5">Total Purchase Request (PR)</span>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                        <i data-feather="file-text" class="w-5 h-5 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-4">
                                    <h4 class="text-3xl font-black tracking-tight">24 <span class="text-sm font-medium opacity-80 ml-1">PR</span></h4>
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black bg-white/25">Costing</span>
                                </div>
                            </div>

                            <!-- 4. RISK CONTROL CARD (SEJAJAR HORIZONTAL & REMARK KOTAK LENGKAP) -->
                            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#b388ff] to-[#7c4dff] p-6 shadow-lg text-white border-0 min-h-[160px] flex flex-col justify-between">
                                <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
                                <div class="absolute -right-2 -top-6 w-20 h-20 bg-white/15 rounded-full pointer-events-none"></div>
                                
                                <div class="relative z-10 flex items-start justify-between">
                                    <div>
                                        <span class="block text-xs font-black uppercase tracking-wider opacity-90">Risk Control</span>
                                        <span class="text-[11px] text-white/70 block font-medium mt-0.5">Damaged & Lost monitoring</span>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                                        <i data-feather="alert-triangle" class="w-5 h-5 stroke-[2.5]"></i>
                                    </div>
                                </div>
                                <div class="relative z-10 flex items-baseline justify-between mt-4">
                                    <!-- Angka ditulis sejajar horizontal dengan ukuran font 3xl yang presisi -->
                                    <h4 class="text-3xl font-black tracking-tight">
                                        14 <span class="text-xs font-bold opacity-75 mr-2">Dmg</span>
                                        3 <span class="text-xs font-bold opacity-75">Lst</span>
                                    </h4>
                                    <!-- Badge remark kotak di pojok kanan bawah -->
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black bg-white/25">Incident</span>
                                </div>
                            </div>

                        </div>

                    @else
                        @yield('content')
                    @endif
                </div>
            </main>
        </div>
    </div>
</body>
</html>