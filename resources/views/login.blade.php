<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SparesCan System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen font-sans">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-8">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight">SSM<span class="text-blue-600"> System</span></h1>
                    <p class="text-slate-500 mt-2 text-sm">Sign in to manage your production spareparts</p>
                </div>

                <form action="/login" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">NIM</label>
                        <input type="text" name="nim" required value="{{ old('nim') }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all bg-slate-50 text-slate-900"
                            placeholder="Enter your NIM">
                        
                        @error('nim') 
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all bg-slate-50 text-slate-900"
                            placeholder="••••••••">
                    </div>

                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98]">
                        Sign In
                    </button>
                </form>
            </div>
            <div class="bg-slate-50 px-8 py-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400">&copy; 2026 Process Engineer</p>
            </div>
        </div>
    </div>
</body>
</html>