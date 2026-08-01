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
        <h1 class="text-xl font-black text-slate-950 dark:text-white tracking-tight">Verify & Check Purchase Request</h1>
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
            <p class="uppercase mb-1 font-black">Gagal Memproses! Periksa Kembali Data:</p>
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
                    <span class="text-slate-500 dark:text-slate-400 px-4 py-1">Pending</span>
                    {{-- WARNA STRIP GRADASI DIUBAH JADI BIRU MODERN DENGAN PANAH MATCHING --}}
                    <span class="bg-gradient-to-r from-blue-700 via-indigo-600 to-sky-500 text-white px-4 py-1 relative after:content-[''] after:absolute after:top-0 after:right-[-6px] after:border-y-[12px] after:border-y-transparent after:border-l-[6px] after:border-l-sky-500 z-10">Checked</span>
                    <span class="text-slate-700 dark:text-slate-300 px-4 py-1">Approved</span>
                </div>
            </div>

            <form id="odooPrForm" action="{{ route('purchase.request.check', $pr->id) }}" method="POST" class="p-6 md:p-8">
                @csrf

                <div class="mb-6">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-0.5">Purchase Request Reference</label>
                    <h2 class="text-xl font-black text-slate-950 dark:text-white tracking-tight">{{ $pr->no_pr }}</h2>
                </div>

                <!-- MAIN FORM GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                    
                    {{-- KOLOM KIRI --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Requester Name</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ optional($pr->user)->name ?? '-' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-black text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">NIK / NIM</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ optional($pr->user)->nim ?? optional($pr->user)->nik ?? '-' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-black text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Email Address</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ optional($pr->user)->email ?? '-' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-black text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-indigo-600 dark:text-indigo-400 text-xs uppercase tracking-wide">Sparepart ID</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ optional($pr->sparepart)->sparepart_id ?? $pr->sparepart_id }}" readonly class="w-full bg-transparent border-0 text-indigo-600 dark:text-indigo-400 font-black text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Part Number</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ optional($pr->sparepart)->part_number ?? '-' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">SAP Code</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ optional($pr->sparepart)->sap_code ?? '-' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Category</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ optional($pr->sparepart)->category ?? '-' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Quantity (QTY)</label>
                            <div class="col-span-2 flex items-center gap-1">
                                <input type="text" value="{{ $pr->qty_pr }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-black text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                                <span class="text-xs text-slate-600 dark:text-slate-400 font-black uppercase pr-2">Pcs</span>
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Priority</label>
                            <div class="col-span-2 flex gap-6">
                                <label class="inline-flex items-center gap-2 text-xs font-black text-slate-950 dark:text-white">
                                    <input type="radio" disabled {{ $pr->priority == 'normal' ? 'checked' : '' }} class="text-orange-500 focus:ring-0 border-gray-400 w-4 h-4 cursor-not-allowed">
                                    <span class="{{ $pr->priority == 'normal' ? 'text-slate-950 dark:text-white font-black' : 'text-slate-400' }}">Normal</span>
                                </label>
                                <label class="inline-flex items-center gap-2 text-xs font-black text-rose-600">
                                    <input type="radio" disabled {{ $pr->priority == 'urgent' ? 'checked' : '' }} class="text-red-500 focus:ring-0 border-gray-400 w-4 h-4 cursor-not-allowed">
                                    <span class="{{ $pr->priority == 'urgent' ? 'text-rose-600 font-black' : 'text-slate-400' }}">Urgent</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Request Date</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ $pr->request_date ? \Carbon\Carbon::parse($pr->request_date)->format('d/m/Y H:i') : '-' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Expected Arrival</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ $pr->expected_arrival_date ? \Carbon\Carbon::parse($pr->expected_arrival_date)->format('d/m/Y H:i') : '-' }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 items-center border-b border-gray-300 dark:border-slate-600 pb-1.5">
                            <label class="font-black text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wide">Destination</label>
                            <div class="col-span-2">
                                <input type="text" value="{{ $pr->destination }}" readonly class="w-full bg-transparent border-0 text-slate-950 dark:text-white font-bold text-sm p-0 cursor-not-allowed outline-none focus:ring-0">
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
                        <textarea readonly rows="3" class="w-full p-3 border border-gray-300 dark:border-slate-600 rounded bg-transparent outline-none text-sm font-bold text-slate-950 dark:text-white transition-all resize-none cursor-not-allowed">{{ $pr->remark ?? 'No notes provided.' }}</textarea>
                    </div>
                </div>

                <!-- ODOO STYLE TAB BAR & TABLES -->
                <div class="mt-8 border border-gray-300 dark:border-slate-600 rounded-lg overflow-hidden bg-gray-50/50 dark:bg-slate-900/40">
                    
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-300 dark:border-slate-600 bg-gray-100 dark:bg-slate-800 text-xs font-black uppercase tracking-wider">
                        <button type="button" id="btn-tab-products" onclick="switchOdooTab('products')" class="px-6 py-3 border-r border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-orange-600 outline-none transition-all cursor-pointer">
                            Products Details
                        </button>
                        <button type="button" id="btn-tab-approval" onclick="switchOdooTab('approval')" class="px-6 py-3 border-r border-gray-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-gray-200/50 dark:hover:bg-slate-700/50 outline-none transition-all cursor-pointer">
                            Approval Workflow (Proses TTD)
                        </button>
                    </div>

                    <!-- Tab Contents Container -->
                    <div class="p-4">
                        
                        {{-- CONTENT TAB 1: PRODUCT DETAILS TABLE --}}
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
                                        <td class="p-3 border-r border-gray-300 dark:border-slate-600 text-indigo-600 dark:text-indigo-400">
                                            {{ optional($pr->sparepart)->sparepart_id ?? $pr->sparepart_id }}
                                        </td>
                                        <td class="p-3 border-r border-gray-300 dark:border-slate-600">{{ optional($pr->sparepart)->part_number ?? '-' }}</td>
                                        <td class="p-3 border-r border-gray-300 dark:border-slate-600">{{ optional($pr->sparepart)->sap_code ?? '-' }}</td>
                                        <td class="p-3 border-r border-gray-300 dark:border-slate-600">{{ optional($pr->sparepart)->category ?? '-' }}</td>
                                        <td class="p-3 border-r border-gray-300 dark:border-slate-600 text-center text-orange-600">{{ $pr->qty_pr }} Pcs</td>
                                        <td class="p-3">{{ $pr->destination }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- CONTENT TAB 2: APPROVAL PROCESS SIGNATURES --}}
                        <div id="odoo-tab-approval" class="hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center text-sm">
                                
                                <!-- TTD 1: PREPARED BY -->
                                <div class="border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800 p-4">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Step 1: Prepared By</span>
                                    <div class="h-16 flex items-center justify-center font-black text-slate-950 dark:text-white border-b border-dashed border-gray-300 dark:border-slate-600 mb-2">
                                        @if($pr->prepared_signature)
                                            <img src="{{ asset('storage/' . $pr->prepared_signature) }}" alt="Prepared Signature" class="max-h-14 object-contain">
                                        @elseif(optional($pr->user)->signature_path || optional($pr->user)->signature)
                                            @php
                                                $sigFile = $pr->user->signature_path ?? $pr->user->signature;
                                                $sigUrl = (str_contains($sigFile, 'uploads/') || str_contains($sigFile, 'storage/')) 
                                                    ? asset($sigFile) 
                                                    : asset('storage/' . $sigFile);
                                            @endphp
                                            <img src="{{ $sigUrl }}" alt="User Signature" class="max-h-14 object-contain">
                                        @else
                                            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 text-xs rounded border border-amber-300">SYSTEM GENERATED</span>
                                        @endif
                                    </div>
                                    <span class="block font-black text-slate-950 dark:text-white">{{ optional($pr->user)->name ?? '-' }}</span>
                                    <span class="text-xs text-slate-500 font-bold">Requester (Engineering)</span>
                                </div>

                                <!-- TTD 2: CHECKED BY (DIGANTI LOGIC-NYA BIAR LANGSUNG MUNCULIN GAMBAR TTD USER YANG SEDANG LOGIN) -->
                                <div class="border-2 border-indigo-500 dark:border-indigo-400 rounded bg-white dark:bg-slate-800 p-4 shadow-sm">
                                    <span class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1">Step 2: Checked By (You)</span>
                                    <div class="h-16 flex items-center justify-center font-black text-slate-950 dark:text-white border-b border-dashed border-gray-300 dark:border-slate-600 mb-2">
                                        @if(Auth::user() && (Auth::user()->signature_path || Auth::user()->signature))
                                            @php
                                                $sigFileChecker = Auth::user()->signature_path ?? Auth::user()->signature;
                                                $sigUrlChecker = (str_contains($sigFileChecker, 'uploads/') || str_contains($sigFileChecker, 'storage/')) 
                                                    ? asset($sigFileChecker) 
                                                    : asset('storage/' . $sigFileChecker);
                                            @endphp
                                            <img src="{{ $sigUrlChecker }}" alt="Checker Signature" class="max-h-14 object-contain">
                                        @else
                                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 text-xs rounded border border-blue-300">SYSTEM GENERATED</span>
                                        @endif
                                    </div>
                                    <span class="block font-black text-slate-950 dark:text-white">{{ Auth::user() ? Auth::user()->name : 'Admin Engineering' }}</span>
                                    <span class="text-xs text-indigo-500 font-bold">Waiting Verification</span>
                                </div>

                                <!-- TTD 3: APPROVED BY -->
                                <div class="border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800 p-4">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Step 3: Approved By</span>
                                    <div class="h-16 flex items-center justify-center font-black text-slate-300 dark:text-slate-600 border-b border-dashed border-gray-300 dark:border-slate-600 mb-2 italic">
                                        Waiting Approval
                                    </div>
                                    <span class="block font-black text-slate-400 dark:text-slate-500">- Pending Approver -</span>
                                    <span class="text-xs text-slate-500 font-bold">Costing Department</span>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <!-- ACTIONS BUTTONS -->
                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-300 dark:border-slate-600">
                    <a href="{{ route('purchase.request.list') }}" class="px-6 py-2.5 bg-gray-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-black rounded shadow-sm uppercase tracking-wider transition-all hover:bg-gray-300 dark:hover:bg-slate-600 text-center">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-700 via-indigo-600 to-sky-500 hover:opacity-95 text-white text-xs font-black rounded shadow-md uppercase tracking-wider transition-all transform hover:-translate-y-0.5">
                        Confirm & Mark as Checked
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

            btnProducts.className = "px-6 py-3 border-r border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-orange-600 outline-none transition-all cursor-pointer";
            btnApproval.className = "px-6 py-3 border-r border-gray-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-gray-200/50 dark:hover:bg-slate-700/50 outline-none transition-all cursor-pointer";
        } else {
            tabProducts.classList.remove('block');
            tabProducts.classList.add('hidden');
            tabApproval.classList.remove('hidden');
            tabApproval.classList.add('block');

            btnProducts.className = "px-6 py-3 border-r border-gray-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-gray-200/50 dark:hover:bg-slate-700/50 outline-none transition-all cursor-pointer";
            btnApproval.className = "px-6 py-3 border-r border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-orange-600 outline-none transition-all cursor-pointer";
        }
    }
</script>
@endsection