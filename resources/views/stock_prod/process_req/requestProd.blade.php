@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-5xl pb-10">
    <div class="mb-6 px-4 sm:px-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-6 w-1.5 bg-primary rounded-full"></span>
                New Sparepart Request
            </h2>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Production Department Form</p>
        </div>
        <div class="flex items-center gap-2 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-lg px-3 py-1.5 self-start sm:self-center">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Status: Draft/Pending</span>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-900/50 text-green-700 dark:text-green-400 font-semibold text-sm flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-md overflow-hidden">
        <form action="{{ route('prod.request.store') }}" method="POST">
            @csrf
            
            <div class="p-5 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                    
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider">Nama Pelapor (Requestor)</label>
                        <input type="text" name="requestor" value="{{ old('requestor') }}" placeholder="Masukkan Nama Pelapor" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white @error('requestor') border-danger @enderror" required>
                        @error('requestor') <p class="text-xs text-danger font-semibold mt-0.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider">Target Line / Mesin (Line Machine)</label>
                        <input type="text" name="line_machine" value="{{ old('line_machine') }}" placeholder="Contoh: Line 04 - SMT" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white @error('line_machine') border-danger @enderror" required>
                        @error('line_machine') <p class="text-xs text-danger font-semibold mt-0.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider">Nama Sparepart (Sparepart Name)</label>
                        <input type="text" name="sparepart_name" value="{{ old('sparepart_name') }}" placeholder="Contoh: Nozzle Fuji NXT H01 1.0mm" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white @error('sparepart_name') border-danger @enderror" required>
                        @error('sparepart_name') <p class="text-xs text-danger font-semibold mt-0.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider">SAP Code / Barcode</label>
                        <div class="relative">
                            <input type="text" name="sap_code" value="{{ old('sap_code') }}" placeholder="Scan Barcode atau Ketik SAP Code" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 pl-4 pr-10 text-sm font-bold text-primary outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input @error('sap_code') border-danger @enderror" required>
                            <span class="absolute top-1/2 right-4 -translate-y-1/2 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        @error('sap_code') <p class="text-xs text-danger font-semibold mt-0.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider">Quantity Request (Qty Req)</label>
                        <input type="number" name="qty_req" value="{{ old('qty_req', 1) }}" min="1" placeholder="0" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white @error('qty_req') border-danger @enderror" required>
                        @error('qty_req') <p class="text-xs text-danger font-semibold mt-0.5">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:justify-end border-t border-stroke dark:border-strokedark pt-6">
                    <button type="reset" class="flex justify-center rounded-lg border border-stroke py-3 px-8 text-sm font-bold uppercase text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:border-strokedark dark:hover:bg-white/5 transition-all">
                        Reset Form
                    </button>
                    <button type="submit" class="flex justify-center rounded-lg bg-primary py-3 px-10 text-sm font-bold uppercase text-white hover:bg-opacity-90 shadow-md transition-all active:scale-95">
                        Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection