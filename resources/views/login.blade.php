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

    @if($errors->any() || session('error'))
    <div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300">
        <div class="w-full max-w-sm bg-white/15 border border-white/30 backdrop-blur-xl p-6 rounded-2xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] text-center animate-[bounce_1s_ease-in-out_1]">
            <div class="w-16 h-16 bg-red-500/20 border border-red-500 text-red-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-[0_0_20px_rgba(239,68,68,0.4)]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-white font-black text-lg tracking-tight">Authentication Failed</h3>
            
            <p class="text-amber-300 text-xs font-bold mt-2 px-2 leading-relaxed">
                The NIK and Password you entered is incorrect.
            </p>
            
            <button type="button" onclick="closeModal()" class="mt-5 w-full bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl shadow-lg transition-colors duration-150 text-xs uppercase tracking-widest">
                Try Again
            </button>
        </div>
    </div>
    @endif
    <div class="hidden lg:flex w-1/2 bg-white items-center justify-center p-12 relative z-0">
        <div class="absolute top-12 left-12 text-slate-900 font-black text-[11px] tracking-widest uppercase">
            PT SIIX EMS KARAWANG
        </div>
        
        <div class="relative z-10 w-full max-w-lg text-center">
            <div class="transition-transform duration-300 hover:scale-[1.01]">
                <img src="{{ asset('images/loginpage.png') }}" 
                     alt="Sparepart Management System" 
                     class="w-full h-auto object-contain mx-auto">
            </div>
            
            <div class="mt-8">
                <h2 class="text-[#1E293B] text-2xl font-black uppercase tracking-tight">SYSTEM SPAREPART MANAGEMENT</h2>
                <p class="text-slate-400 text-sm font-semibold mt-1">Precision Nozzle Tracking & Stock Infrastructure</p>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-16 md:p-20 bg-gradient-to-br from-[#4F46E5] via-[#2563EB] to-[#06B6D4] animate-rainbow relative overflow-hidden z-10 rounded-none Shadow-[-20px_0_50px_rgba(0,0,0,0.15)]">
        
        <canvas id="particleCanvas" class="absolute inset-0 w-full h-full pointer-events-none opacity-60"></canvas>
        
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#0EA5E9]/30 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#38BDF8]/20 rounded-full blur-[100px] animate-pulse" style="animation-delay: 3s;"></div>

        <div class="w-full max-w-sm relative z-10 bg-white/10 p-8 sm:p-10 rounded-2xl border border-white/20 shadow-[0_25px_50px_-15px_rgba(0,0,0,0.25)] backdrop-blur-xl">
            
            <div class="mb-8">
                <h1 class="text-2xl font-black text-white tracking-tight">Welcome Back</h1>
                <p class="text-white text-xs font-extrabold uppercase tracking-wider mt-1 opacity-90">SIGN IN TO YOUR ACCOUNT</p>
            </div>

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
                        <input type="text" name="nim" required value="{{ old('nim') }}"
                            class="w-full p-3.5 bg-[#EBF2FA] text-slate-900 text-sm font-bold outline-none placeholder-slate-400 rounded-r-xl shadow-inner" 
                            placeholder="123456">
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
                            class="w-full p-3.5 bg-[#EBF2FA] text-slate-900 text-sm font-bold outline-none placeholder-slate-400 tracking-widest shadow-inner" 
                            placeholder="••••••••">
                        <div class="pr-4 pl-2 bg-[#EBF2FA] h-full flex items-center justify-center py-3.5">
                            <button type="button" onclick="togglePassword()" class="text-slate-400 hover:text-blue-600 transition-colors focus:outline-none">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-[#10B981] hover:bg-[#34D399] text-white font-black py-3.5 rounded-xl shadow-[0_4px_20px_rgba(16,185,129,0.4)] hover:shadow-[0_0_30px_rgba(52,211,153,0.8)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 text-xs uppercase tracking-widest mt-2">
                    LOGIN
                </button>
            </form>
            
            <div class="mt-8 pt-4 border-t border-white/20 flex items-center justify-between text-[9px] text-white/70 font-black uppercase tracking-wider">
                <span>&copy; 2026 PT SIIX EMS</span>
                <span>ENGINEERING DEPT</span>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>