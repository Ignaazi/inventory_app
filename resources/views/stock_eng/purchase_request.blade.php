@extends('admin')

@section('content')
<div class="-m-4 md:-m-6 2xl:-m-10 bg-[#F9F9FB] dark:bg-slate-900 min-h-[calc(100vh-80px)] text-slate-800 dark:text-slate-200 font-sans p-3">
    
    <div class="px-4 pt-4 max-w-full mx-auto">
        <h1 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Create Purchase Request</h1>
    </div>

    <div class="p-3 max-w-full mx-auto">
        
        @if(session('success'))
        <div class="mb-3 p-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold rounded shadow-sm">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-3 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-bold rounded shadow-sm">
            <p class="uppercase mb-1">Gagal Menyimpan! Periksa Input Berikut:</p>
            <ul class="list-disc pl-4 font-semibold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded shadow-sm overflow-hidden">
            
            <div class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-slate-700 px-4 py-1.5 flex justify-between items-center text-[10px] font-bold uppercase tracking-wider">
                <div class="flex gap-2 text-slate-400">
                    <span>Source: <span class="text-slate-700 dark:text-white">Auto-generated</span></span>
                </div>
                
                <div class="flex items-center bg-gray-200/60 dark:bg-slate-700 rounded overflow-hidden text-[9px]">
                    <span class="bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 text-white px-3 py-1 relative after:content-[''] after:absolute after:top-0 after:right-[-6px] after:border-y-[11px] after:border-y-transparent after:border-l-[6px] after:border-l-orange-500 z-10">Draft</span>
                    <span class="text-slate-500 dark:text-slate-400 px-4 py-1.5">Waiting Approval</span>
                    <span class="text-slate-500 dark:text-slate-400 px-3 py-1.5">Done</span>
                </div>
            </div>

            <form id="odooPrForm" action="{{ route('purchase.request.store') }}" method="POST" class="p-4 md:p-6">
                @csrf

                <div class="mb-4">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-0.5">Purchase Request Reference</label>
                    <input type="hidden" name="pr_code" value="{{ $generatedPrCode }}">
                    <h2 class="text-lg font-extrabold text-slate-700 dark:text-slate-300 tracking-tight">{{ $generatedPrCode }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-xs">
                    
                    <div class="space-y-3">
                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">Requester Name</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ Auth::user() ? Auth::user()->name : 'muhammad ignazi' }}" readonly class="w-full bg-transparent border-0 text-gray-500 font-semibold text-xs p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">NIK / NIM</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ Auth::user() ? (Auth::user()->nim ?? Auth::user()->nik) : '20260001' }}" readonly class="w-full bg-transparent border-0 text-gray-500 font-semibold text-xs p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">Product (Category) <span class="text-rose-500">*</span></label>
                            <div class="col-span-2">
                                <select name="product" required class="w-full bg-transparent border-0 focus:ring-0 outline-none text-xs font-semibold text-indigo-600 dark:text-indigo-400 p-0 cursor-pointer">
                                    <option value="" disabled selected>Select Category...</option>
                                    @foreach($spareparts->unique('category') as $item)
                                        <option value="{{ $item->category }}" {{ old('product') == $item->category ? 'selected' : '' }}>{{ $item->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">Type Product (Name) <span class="text-rose-500">*</span></label>
                            <div class="col-span-2">
                                <select name="type_product" required class="w-full bg-transparent border-0 focus:ring-0 outline-none text-xs font-semibold text-indigo-600 dark:text-indigo-400 p-0 cursor-pointer">
                                    <option value="" disabled selected>Select Product Name...</option>
                                    @foreach($spareparts as $item)
                                        <option value="{{ $item->name }}" {{ old('type_product') == $item->name ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">Quantity (QTY) <span class="text-rose-500">*</span></label>
                            <div class="col-span-2 flex items-center gap-1">
                                <input type="number" name="qty" id="qty_input" min="1" value="{{ old('qty', 1) }}" required class="w-24 bg-transparent border-0 text-slate-900 dark:text-white font-bold font-mono text-xs p-0 outline-none focus:ring-0" placeholder="0">
                                <span class="text-[10px] text-gray-400 font-bold uppercase">Pcs</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">Priority</label>
                            <div class="col-span-2 flex gap-4">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-[11px] font-bold text-slate-700 dark:text-white">
                                    <input type="radio" name="priority" value="normal" {{ old('priority', 'normal') == 'normal' ? 'checked' : '' }} class="text-orange-500 focus:ring-0 border-gray-300 w-3 h-3">
                                    <span>Normal</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-[11px] font-bold text-rose-600">
                                    <input type="radio" name="priority" value="urgent" {{ old('priority') == 'urgent' ? 'checked' : '' }} class="text-red-500 focus:ring-0 border-gray-300 w-3 h-3">
                                    <span>Urgent</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">Request By</label>
                            <div class="col-span-2">
                                <input type="hidden" name="request_by" value="ENGINEERING DEPARTMENT">
                                <select disabled class="w-full bg-transparent border-0 focus:ring-0 outline-none text-xs font-semibold text-gray-600 dark:text-slate-300 p-0 cursor-not-allowed">
                                    <option value="ENGINEERING DEPARTMENT" selected>ENGINEERING DEPARTMENT</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">Request Date</label>
                            <div class="col-span-2">
                                <input type="datetime-local" name="request_date" value="{{ old('request_date', now()->format('Y-m-d\TH:i')) }}" required class="w-full bg-transparent border-0 text-gray-600 dark:text-slate-300 font-semibold text-xs p-0 outline-none focus:ring-0 cursor-pointer">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-100 dark:border-slate-700/50 pb-1">
                            <label class="font-bold text-slate-500 dark:text-slate-400 text-[11px] uppercase">Destination</label>
                            <div class="col-span-2">
                                <select name="destination" required class="w-full bg-transparent border-0 focus:ring-0 outline-none text-xs font-semibold text-gray-600 dark:text-slate-300 p-0 cursor-pointer">
                                    <option value="Costing & Procurement Room" selected>Costing & Procurement Room</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="border-b border-gray-200 dark:border-slate-700 flex gap-6 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <span class="border-b-2 border-orange-500 pb-1 text-orange-500 cursor-pointer">Internal Notes / Reason</span>
                    </div>
                    
                    <div class="mt-2 mb-4">
                        <textarea name="notes" rows="3" required placeholder="Define why engineering needs this purchase request..." class="w-full p-3 border border-gray-200 dark:border-slate-700 rounded bg-transparent focus:ring-1 focus:ring-orange-500 focus:border-orange-500 outline-none text-xs font-semibold transition-all resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-slate-700/50">
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 hover:opacity-90 text-white text-[11px] font-bold rounded shadow-md uppercase tracking-wider transition-all transform hover:-translate-y-0.5">
                        Save Purchase Request
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<style>
    /* Menghilangkan panah spin bawaan browser di input type number agar tampilannya clean ala Odoo */
    #qty_input::-webkit-outer-spin-button,
    #qty_input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    #qty_input {
        -moz-appearance: textfield;
    }
</style>
@endsection