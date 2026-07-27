<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SparesCan System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>
<body class="h-screen flex font-sans overflow-hidden bg-white relative">

    <!-- TAILADMIN POPUP ALERT MODAL (PREMIUM & RESPONSIVE) -->
    @if(session('error_type'))
    <div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300">
        
        <!-- Kotak Putih Utama (Lebih Lebar di PC, Menyesuaikan di Mobile) -->
        <div class="w-full max-w-2xl bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden text-left animate-[fadeIn_0.2s_ease-out_1]">
            
            <!-- HEADER MODAL: Judul di Pojok Kiri -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <span class="text-sm font-black uppercase tracking-wider text-slate-700">
                    @if(session('error_type') === 'wrong_password' || session('error_type') === 'lockout')
                        Error Alert
                    @else
                        Warning Alert
                    @endif
                </span>
            </div>

            <!-- BODY MODAL: Tempat Alert Box TailAdmin -->
            <div class="p-6">
                
                <!-- 1. STYLE SALAH PASSWORD / LOCKOUT (Tema Merah TailAdmin) -->
                @if(session('error_type') === 'wrong_password' || session('error_type') === 'lockout')
                <div class="flex w-full rounded-md border border-[#F87171] bg-[#F87171]/10 p-4 text-left">
                    <div class="mr-4 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-[#F87171] text-[#F87171]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="w-full">
                        <h5 class="text-xs font-bold text-[#F87171] uppercase tracking-wide">Error Message</h5>
                        <p class="text-[11px] font-semibold text-slate-600 mt-1 leading-normal">
                            @if(session('error_type') === 'lockout')
                                Too many login attempts. Please wait <span id="countdown" class="font-black text-[#F87171] underline">{{ session('seconds') }}</span> seconds.
                            @else
                                {{ session('message') }}
                            @endif
                        </p>
                    </div>
                </div>
                @endif

                <!-- 2. STYLE BELUM TERDAFTAR (Tema Kuning/Oranye TailAdmin) -->
                @if(session('error_type') === 'not_registered')
                <div class="flex w-full rounded-md border border-[#F59E0B] bg-[#F59E0B]/10 p-4 text-left">
                    <div class="mr-4 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-[#F59E0B] text-[#F59E0B]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="w-full">
                        <h5 class="text-xs font-bold text-[#F59E0B] uppercase tracking-wide">Warning Message</h5>
                        <p class="text-[11px] font-semibold text-slate-600 mt-1 leading-normal">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
                @endif
            </div>

            <!-- FOOTER MODAL: Button "Close" di Pojok Kanan Bawah -->
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end bg-slate-50/30">
                <button type="button" onclick="closeModal()" class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white font-bold px-6 py-2 rounded-lg text-xs uppercase tracking-widest shadow-md transition-all duration-150 active:scale-95 cursor-pointer text-center">
                    Close
                </button>
            </div>

        </div>
    </div>
    @endif
    
    <!-- LEFT SIDE: DESKTOP ONLY INFRASTRUCTURE -->
    <div class="hidden lg:flex w-1/2 bg-white items-center justify-center p-12 relative z-0">
        <div class="absolute top-12 left-12 text-slate-900 font-black text-[11px] tracking-widest uppercase">
            PT SIIX EMS KARAWANG
        </div>
        
        <div class="relative z-10 w-full max-w-lg text-center">
            <div class="transition-transform duration-300 hover:scale-[1.01]">
                <img src="{{ asset('images/loginpage.png') }}" alt="Sparepart Management System" class="w-full h-auto object-contain mx-auto">
            </div>
            <div class="mt-8">
                <h2 class="text-[#1E293B] text-2xl font-black uppercase tracking-tight">SPAREPART MANAGEMENT SYSTEM</h2>
                <p class="text-slate-400 text-sm font-semibold mt-1">Precision Nozzle Tracking & Stock Infrastructure</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: LOGIN FORM & MOBILE BG CONTAINER -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 md:p-20 bg-gradient-to-br from-[#4F46E5] via-[#2563EB] to-[#06B6D4] animate-rainbow relative overflow-hidden z-10 rounded-none shadow-[-20px_0_50px_rgba(0,0,0,0.15)]">
        <canvas id="particleCanvas" class="absolute inset-0 w-full h-full pointer-events-none opacity-60"></canvas>
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#0EA5E9]/30 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#38BDF8]/20 rounded-full blur-[100px] animate-pulse" style="animation-delay: 3s;"></div>

        <!-- BOX WRAPPER -->
        <div class="w-full max-w-sm relative z-10 bg-white/10 p-8 sm:p-10 rounded-2xl border border-white/20 shadow-[0_25px_50px_-15px_rgba(0,0,0,0.25)] backdrop-blur-xl">
            
            <!-- HEADER SECTION -->
            <div class="mb-6 text-center lg:text-left">
                <div class="lg:hidden w-full max-w-[160px] mx-auto mb-4 transition-transform duration-300 hover:scale-105">
                    <img src="{{ asset('images/loginpage.png') }}" alt="Sparepart System Mobile" class="w-full h-auto object-contain drop-shadow-[0_10px_15px_rgba(0,0,0,0.2)]">
                </div>
                <h1 class="text-2xl font-black text-white tracking-tight">Welcome Back</h1>
                <p class="text-white text-xs font-extrabold uppercase tracking-wider mt-1 opacity-90">SIGN IN TO YOUR ACCOUNT</p>
            </div>

            <!-- FORM VALIDATION -->
            <form action="/login" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-white mb-2">NIK</label>
                    <div class="flex items-center bg-white/15 border border-white/20 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-white focus-within:bg-white/20 transition-all duration-150">
                        <div class="pl-4 pr-3 py-3.5 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        {{-- 👈 Mengubah name, id, value old, dan placeholder agar merujuk ke 'nik' --}}
                        <input type="text" name="nik" id="loginNik" required value="{{ old('nik') }}"
                            class="w-full p-3.5 bg-[#EBF2FA] text-slate-900 text-sm font-bold outline-none placeholder-slate-400 rounded-r-xl shadow-inner disabled:opacity-40" 
                            placeholder="Input NIK Anda" {{ session('error_type') === 'lockout' ? 'disabled' : '' }}>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-white mb-2">PASSWORD</label>
                    <div class="flex items-center bg-white/15 border border-white/20 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-white focus-within:bg-white/20 transition-all duration-150">
                        <div class="pl-4 pr-3 py-3.5 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="w-full p-3.5 bg-[#EBF2FA] text-slate-900 text-sm font-bold outline-none placeholder-slate-400 tracking-widest shadow-inner disabled:opacity-40" 
                            placeholder="••••••••" {{ session('error_type') === 'lockout' ? 'disabled' : '' }}>
                        <div class="pr-4 pl-2 bg-[#EBF2FA] h-full flex items-center justify-center py-3.5">
                            <button type="button" onclick="togglePassword()" class="text-slate-400 hover:text-blue-600 transition-colors focus:outline-none cursor-pointer">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" id="loginBtn" {{ session('error_type') === 'lockout' ? 'disabled' : '' }}
                    class="w-full bg-[#10B981] hover:bg-[#34D399] text-white font-black py-3.5 rounded-xl shadow-[0_4px_20px_rgba(16,185,129,0.4)] hover:shadow-[0_0_30px_rgba(52,211,153,0.8)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 text-xs uppercase tracking-widest mt-2 disabled:bg-slate-500 disabled:shadow-none disabled:scale-100 disabled:cursor-not-allowed">
                    {{ session('error_type') === 'lockout' ? 'LOCKED' : 'LOGIN' }}
                </button>
            </form>
            
            <div class="mt-8 pt-4 border-t border-white/20 flex items-center justify-between text-[9px] text-white/70 font-black uppercase tracking-wider">
                <span>&copy; 2026 PT SIIX EMS</span>
                <span>ENGINEERING DEPT</span>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>

    <script>
        function closeModal() {
            const modal = document.getElementById('errorModal');
            if(modal) {
                modal.classList.add('opacity-0');
                setTimeout(() => { modal.remove(); }, 300);
            }
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput && passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />`;
            } else if (passwordInput) {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const countdownEl = document.getElementById("countdown");
            if (countdownEl) {
                let timeLeft = parseInt(countdownEl.textContent);
                const timer = setInterval(() => {
                    timeLeft--;
                    countdownEl.textContent = timeLeft;
                    
                    if (timeLeft <= 0) {
                        clearInterval(timer);
                        window.location.reload();
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>