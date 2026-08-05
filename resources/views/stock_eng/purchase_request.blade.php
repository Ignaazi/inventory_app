@extends('admin')

@section('content')
<!-- Inject Font Nunito & Global Layout Size Overrides -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .create-pr-view, .create-pr-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }
</style>

<div class="create-pr-view -m-4 md:-m-6 2xl:-m-10 bg-[#F9F9FB] dark:bg-slate-900 min-h-[calc(100vh-80px)] text-slate-950 dark:text-slate-100 font-sans p-4">
    
    <div class="px-4 pt-4 max-w-full mx-auto">
        <h1 class="text-xl font-black text-slate-950 dark:text-white tracking-tight">Create Purchase Request</h1>
    </div>

    <div class="p-4 max-w-full mx-auto">
        
        @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-400 text-emerald-900 text-xs font-bold rounded shadow-sm">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-3 bg-rose-50 border border-rose-400 text-rose-900 text-xs font-bold rounded shadow-sm">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 p-4 bg-rose-50 border border-rose-400 text-rose-900 text-xs font-bold rounded shadow-sm">
            <p class="uppercase mb-1 font-black">Gagal Menyimpan! Periksa Input Berikut:</p>
            <ul class="list-disc pl-4 font-bold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg shadow-md overflow-hidden">
            
            <!-- TOP BAR STATUS ODOO -->
            <div class="bg-gray-100 dark:bg-slate-800/80 border-b border-gray-300 dark:border-slate-600 px-4 py-2 flex justify-between items-center text-xs font-black uppercase tracking-wider">
                <div class="flex gap-2 text-slate-600 dark:text-slate-400">
                    <span>Source: <span class="text-slate-950 dark:text-white">Auto-generated</span></span>
                </div>
                
                <div class="flex items-center bg-gray-200 dark:bg-slate-700 rounded overflow-hidden text-[10px] font-black border border-gray-300 dark:border-slate-600">
                    <span class="bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 text-white px-4 py-1 relative after:content-[''] after:absolute after:top-0 after:right-[-6px] after:border-y-[12px] after:border-y-transparent after:border-l-[6px] after:border-l-orange-500 z-10">Pending</span>
                    <span class="text-slate-700 dark:text-slate-300 px-4 py-1">Checked</span>
                    <span class="text-slate-700 dark:text-slate-300 px-4 py-1">Approved</span>
                </div>
            </div>

            <form id="odooPrForm" action="{{ route('purchase.request.store') }}" method="POST" class="p-6 md:p-8">
                @csrf

                <div class="mb-6">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-0.5">Purchase Request Reference</label>
                    <input type="hidden" name="no_pr" value="{{ $generatedPrCode }}">
                    <h2 class="text-xl font-black text-slate-950 dark:text-white tracking-tight">{{ $generatedPrCode }}</h2>
                </div>

                <!-- MAIN FORM GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                    
                    {{-- KOLOM KIRI --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Requester Name</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ Auth::user() ? Auth::user()->name : 'Muhammad Anwar' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-black text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">NIK / NIM</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ Auth::user() ? (Auth::user()->nim ?? Auth::user()->nik) : '123456' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-black text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Email Address</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ Auth::user() ? Auth::user()->email : 'admin@company.com' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-black text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-indigo-600 dark:text-indigo-400 text-xs uppercase tracking-wide">Sparepart ID <span class="text-rose-500">*</span></label>
                            <div class="col-span-2">
                                <select name="sparepart_id" id="sparepart_select" required class="w-full bg-transparent border-0 focus:ring-0 outline-none text-sm font-black text-indigo-600 dark:text-indigo-400 p-0 cursor-pointer">
                                    <option value="" disabled selected>Select Sparepart ID...</option>
                                    @foreach($spareparts as $item)
                                        <option value="{{ $item->id }}" 
                                                data-sparepart-id="{{ $item->sparepart_id }}"
                                                data-part="{{ $item->part_number }}"
                                                data-sap="{{ $item->sap_code ?? '-' }}"
                                                data-category="{{ $item->category }}"
                                                {{ old('sparepart_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->sparepart_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Part Number</label>
                            <div class="col-span-2">
                                <input type="text" id="display_part_number" readonly placeholder="Auto-filled" class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">SAP Code</label>
                            <div class="col-span-2">
                                <input type="text" id="display_sap_code" readonly placeholder="Auto-filled" class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Category</label>
                            <div class="col-span-2">
                                <input type="text" id="display_category" readonly placeholder="Auto-filled" class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        {{-- QTY: DEFAULT 0, NO MINUS (-), NO SUBMIT 0 --}}
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Quantity (QTY) <span class="text-rose-500">*</span></label>
                            <div class="col-span-2 flex items-center gap-1">
                                <input type="number" 
                                       name="qty_pr" 
                                       id="qty_input" 
                                       min="1" 
                                       step="1" 
                                       value="{{ old('qty_pr', 0) }}" 
                                       required 
                                       onkeydown="if(event.key==='-' || event.key==='e' || event.key==='E' || event.key==='.') event.preventDefault();"
                                       class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-black text-sm p-0 outline-none focus:ring-0" 
                                       placeholder="0">
                                <span class="text-xs text-slate-600 dark:text-slate-400 font-black uppercase pr-2">Pcs</span>
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Priority</label>
                            <div class="col-span-2 flex gap-6">
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-black text-slate-950 dark:text-white">
                                    <input type="radio" name="priority" value="normal" {{ old('priority', 'normal') == 'normal' ? 'checked' : '' }} class="text-orange-500 focus:ring-0 border-gray-400 w-4 h-4">
                                    <span>Normal</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-black text-rose-600">
                                    <input type="radio" name="priority" value="urgent" {{ old('priority') == 'urgent' ? 'checked' : '' }} class="text-red-500 focus:ring-0 border-gray-400 w-4 h-4">
                                    <span>Urgent</span>
                                </label>
                            </div>
                        </div>

                        @php
                            $nowFormatted = now()->format('Y-m-d\TH:i');
                        @endphp

                        {{-- REQUEST DATE: Minimum Today/Now --}}
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Request Date <span class="text-rose-500">*</span></label>
                            <div class="col-span-2">
                                <input type="datetime-local" 
                                       name="request_date" 
                                       id="request_date"
                                       min="{{ $nowFormatted }}"
                                       value="{{ old('request_date') ? \Carbon\Carbon::parse(old('request_date'))->format('Y-m-d\TH:i') : $nowFormatted }}" 
                                       required 
                                       class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 outline-none focus:ring-0 cursor-pointer">
                            </div>
                        </div>

                        {{-- EXPECTED ARRIVAL DATE: Cannot be earlier than Request Date --}}
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Expected Arrival <span class="text-rose-500">*</span></label>
                            <div class="col-span-2">
                                <input type="datetime-local" 
                                       name="expected_arrival_date" 
                                       id="expected_arrival_date"
                                       min="{{ $nowFormatted }}"
                                       value="{{ old('expected_arrival_date') ? \Carbon\Carbon::parse(old('expected_arrival_date'))->format('Y-m-d\TH:i') : now()->addDays(3)->format('Y-m-d\TH:i') }}" 
                                       required 
                                       class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 outline-none focus:ring-0 cursor-pointer">
                            </div>
                        </div>

                        {{-- DESTINATION: Costing Dept & Purchasing Dept --}}
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Destination</label>
                            <div class="col-span-2">
                                <select name="destination" id="destination_select" required class="w-full bg-transparent border-0 focus:ring-0 outline-none text-sm font-bold text-slate-950 dark:text-white p-0 cursor-pointer">
                                    <option value="Costing Dept & Purchasing Dept" selected>Costing Dept & Purchasing Dept</option>
                                </select>
                            </div>
                        </div>

                        {{-- ROW VISUAL EMAIL COSTING --}}
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-indigo-600 dark:text-indigo-400 text-xs uppercase tracking-wide">Costing Email</label>
                            <div class="col-span-2">
                                <select name="recipient_email" id="recipient_email_select" class="w-full bg-transparent border-0 focus:ring-0 outline-none text-sm font-black text-indigo-600 dark:text-indigo-400 p-0 cursor-pointer">
                                    <option value="" disabled selected>Will be assigned during Costing Approval...</option>
                                    @if(isset($costingUsers) && count($costingUsers) > 0)
                                        @foreach($costingUsers as $cUser)
                                            <option value="{{ $cUser->email }}">
                                                {{ $cUser->name }} ({{ $cUser->email ?? 'No Email' }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="costing@company.com">Costing Dept Approver (costing@company.com)</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- SECTION REMARK / INTERNAL NOTES -->
                <div class="mt-8">
                    <div class="border-b border-gray-300 dark:border-slate-600 flex gap-6 text-xs font-black uppercase tracking-wider text-slate-500 mb-3">
                        <span class="border-b-2 border-orange-500 pb-1 text-orange-600 cursor-pointer">Internal Notes / Reason</span>
                    </div>
                    
                    <div class="mt-2 mb-6">
                        <textarea name="remark" rows="3" required placeholder="Define why engineering needs this purchase request..." class="w-full p-3 border border-gray-300 dark:border-slate-600 rounded bg-transparent focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none text-sm font-bold text-slate-950 dark:text-white transition-all resize-none">{{ old('remark') }}</textarea>
                    </div>
                </div>

                <!-- ODOO STYLE TAB BAR & TABLES -->
                <div class="mt-8 border border-gray-300 dark:border-slate-600 rounded-lg overflow-hidden bg-gray-50/50 dark:bg-slate-900/40">
                    
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-300 dark:border-slate-600 bg-gray-100 dark:bg-slate-800 text-xs font-black uppercase tracking-wider">
                        <button type="button" id="btn-tab-products" onclick="switchOdooTab('products')" class="px-6 py-3 border-r border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-orange-600 outline-none transition-all">
                            Products Details
                        </button>
                        <button type="button" id="btn-tab-approval" onclick="switchOdooTab('approval')" class="px-6 py-3 border-r border-gray-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-gray-200/50 dark:hover:bg-slate-700/50 outline-none transition-all">
                            Approval Workflow (Proses TTD)
                        </button>
                    </div>

                    <!-- Tab Contents Container -->
                    <div class="p-4">
                        
                        {{-- CONTENT TAB 1: PRODUCT DETAILS TABLE (LIVE SYNCED) --}}
                        <div id="odoo-tab-products" class="block overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="border-b-2 border-gray-300 dark:border-slate-600 text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-400 bg-gray-100/80 dark:bg-slate-800/60">
                                        <th class="p-3 border-r border-gray-300 dark:border-slate-600">Sparepart ID</th>
                                        <th class="p-3 border-r border-gray-300 dark:border-slate-600">Part Number</th>
                                        <th class="p-3 border-r border-gray-300 dark:border-slate-600">SAP Code</th>
                                        <th class="p-3 border-r border-gray-300 dark:border-slate-600">Category</th>
                                        <th class="p-3 border-r border-gray-300 dark:border-slate-600 text-center">Quantity</th>
                                        <th class="p-3">Destination</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-300 dark:border-slate-600 font-black text-slate-950 dark:text-white bg-white dark:bg-slate-800">
                                        <td id="table_sparepart_id" class="p-3 border-r border-gray-300 dark:border-slate-600 text-indigo-600 dark:text-indigo-400">-</td>
                                        <td id="table_part_number" class="p-3 border-r border-gray-300 dark:border-slate-600">-</td>
                                        <td id="table_sap_code" class="p-3 border-r border-gray-300 dark:border-slate-600">-</td>
                                        <td id="table_category" class="p-3 border-r border-gray-300 dark:border-slate-600">-</td>
                                        <td id="table_qty" class="p-3 border-r border-gray-300 dark:border-slate-600 text-center text-orange-600">0 Pcs</td>
                                        <td id="table_destination" class="p-3">Costing Dept & Purchasing Dept</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- CONTENT TAB 2: APPROVAL PROCESS SIGNATURES (STEP 2 & 3 BLOCKED/LOCKED) --}}
                        <div id="odoo-tab-approval" class="hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center text-sm">
                                
                                <!-- TTD 1: PREPARED BY (AKTIF - REQUESTER) -->
                                <div class="border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800 p-4">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Step 1: Prepared By</span>
                                    <div class="h-16 flex items-center justify-center font-black text-slate-950 dark:text-white border-b border-dashed border-gray-300 dark:border-slate-600 mb-2">
                                        @if(Auth::user() && (Auth::user()->signature_path || Auth::user()->signature))
                                            @php
                                                $sigFile = Auth::user()->signature_path ?? Auth::user()->signature;
                                                $sigUrl = (str_contains($sigFile, 'uploads/') || str_contains($sigFile, 'storage/')) 
                                                    ? asset($sigFile) 
                                                    : asset('storage/' . $sigFile);
                                            @endphp
                                            <img src="{{ $sigUrl }}" alt="User Signature" class="max-h-14 object-contain">
                                        @else
                                            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 text-xs rounded border border-amber-300">SYSTEM GENERATED</span>
                                        @endif
                                    </div>
                                    <span class="block font-black text-slate-950 dark:text-white">{{ Auth::user() ? Auth::user()->name : 'Muhammad Anwar' }}</span>
                                    <span class="text-xs text-slate-500 font-bold">Requester (Engineering)</span>
                                </div>

                                <!-- TTD 2: CHECKED BY (LOCKED / BLOCKED) -->
                                <div class="border border-gray-200 dark:border-slate-700/60 rounded bg-gray-100/70 dark:bg-slate-900/60 p-4 opacity-60 select-none cursor-not-allowed">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Step 2: Checked By</span>
                                    <div class="h-16 flex flex-col items-center justify-center border-b border-dashed border-gray-300 dark:border-slate-700 mb-2">
                                        <svg class="w-5 h-5 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">LOCKED / BLOCKED</span>
                                    </div>
                                    <span class="block font-extrabold text-slate-400 text-xs">-</span>
                                    <span class="text-xs text-slate-400 font-semibold">Admin Engineering</span>
                                </div>

                                <!-- TTD 3: APPROVED BY (LOCKED / BLOCKED) -->
                                <div class="border border-gray-200 dark:border-slate-700/60 rounded bg-gray-100/70 dark:bg-slate-900/60 p-4 opacity-60 select-none cursor-not-allowed">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Step 3: Approved By</span>
                                    <div class="h-16 flex flex-col items-center justify-center border-b border-dashed border-gray-300 dark:border-slate-700 mb-2">
                                        <svg class="w-5 h-5 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">LOCKED / BLOCKED</span>
                                    </div>
                                    <span class="block font-extrabold text-slate-400 text-xs">-</span>
                                    <span class="text-xs text-slate-400 font-semibold">Costing Department</span>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <!-- SAVE BUTTON ACTIONS -->
                <div class="flex justify-end pt-6 mt-6 border-t border-gray-300 dark:border-slate-600">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-600 via-orange-500 to-yellow-500 hover:opacity-95 text-white text-xs font-black rounded shadow-md uppercase tracking-wider transition-all transform hover:-translate-y-0.5">
                        Save Purchase Request
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<!-- REAL-TIME JAVASCRIPT LOGIC -->
<script>
    function switchOdooTab(tabName) {
        const tabProducts = document.getElementById('odoo-tab-products');
        const tabApproval = document.getElementById('odoo-tab-approval');
        const btnProducts = document.getElementById('btn-tab-products');
        const btnApproval = document.getElementById('btn-tab-approval');

        if(tabName === 'products') {
            tabProducts.classList.remove('hidden');
            tabProducts.classList.add('block');
            tabApproval.classList.remove('block');
            tabApproval.classList.add('hidden');

            btnProducts.className = "px-6 py-3 border-r border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-orange-600 outline-none transition-all";
            btnApproval.className = "px-6 py-3 border-r border-gray-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-gray-200/50 dark:hover:bg-slate-700/50 outline-none transition-all";
        } else {
            tabProducts.classList.remove('block');
            tabProducts.classList.add('hidden');
            tabApproval.classList.remove('hidden');
            tabApproval.classList.add('block');

            btnProducts.className = "px-6 py-3 border-r border-gray-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-gray-200/50 dark:hover:bg-slate-700/50 outline-none transition-all";
            btnApproval.className = "px-6 py-3 border-r border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-orange-600 outline-none transition-all";
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const selectElement = document.getElementById('sparepart_select');
        const qtyInput = document.getElementById('qty_input');
        const reqDateInput = document.getElementById('request_date');
        const expDateInput = document.getElementById('expected_arrival_date');

        // Dynamic Sync: Expected Arrival minimal sama dengan Request Date
        reqDateInput.addEventListener('change', function() {
            const selectedReqDate = this.value;
            expDateInput.min = selectedReqDate;
            if (expDateInput.value && expDateInput.value < selectedReqDate) {
                expDateInput.value = selectedReqDate;
            }
        });
        
        function updateSparepartFields() {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            if (selectedOption && selectedOption.value !== "") {
                const customPrId = selectedOption.getAttribute('data-sparepart-id');
                const part = selectedOption.getAttribute('data-part');
                const sap = selectedOption.getAttribute('data-sap');
                const category = selectedOption.getAttribute('data-category');
                
                document.getElementById('display_part_number').value = part;
                document.getElementById('display_sap_code').value = sap;
                document.getElementById('display_category').value = category;

                document.getElementById('table_sparepart_id').innerText = customPrId;
                document.getElementById('table_part_number').innerText = part;
                document.getElementById('table_sap_code').innerText = sap;
                document.getElementById('table_category').innerText = category;
            } else {
                document.getElementById('display_part_number').value = '';
                document.getElementById('display_sap_code').value = '';
                document.getElementById('display_category').value = '';

                document.getElementById('table_sparepart_id').innerText = '-';
                document.getElementById('table_part_number').innerText = '-';
                document.getElementById('table_sap_code').innerText = '-';
                document.getElementById('table_category').innerText = '-';
            }
        }

        function updateLiveQty() {
            let qtyVal = qtyInput.value ? parseInt(qtyInput.value) : 0;
            if (qtyVal < 0 || isNaN(qtyVal)) {
                qtyVal = 0;
            }
            document.getElementById('table_qty').innerText = qtyVal + ' Pcs';
        }

        selectElement.addEventListener('change', updateSparepartFields);
        qtyInput.addEventListener('input', updateLiveQty);
        
        updateSparepartFields();
        updateLiveQty();
    });
</script>

<style>
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