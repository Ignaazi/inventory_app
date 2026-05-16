@extends('admin')

@section('content')
<div class="-m-4 md:-m-6 2xl:-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-[calc(100vh-80px)] font-sans">
    
    <div class="p-4 md:p-8 2xl:p-10">
        
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-slate-900 dark:text-white">
            <div>
                <h1 class="text-xl md:text-2xl font-black tracking-tight uppercase">Engineering Approval</h1>
                <nav class="flex text-[9px] md:text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">
                    <span>Engineering</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-indigo-600 font-bold uppercase">Production Requests</span>
                </nav>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Incoming Requests</p>
                <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-white uppercase">{{ date('d M Y') }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-xs uppercase tracking-wide">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden text-slate-900 dark:text-white">
            
            <div class="p-4 md:p-6 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 border-b border-slate-100 dark:border-strokedark">
                <div class="w-full xl:w-auto">
                    <h3 class="text-base md:text-lg font-bold uppercase leading-tight">Production Line Requests</h3>
                    <p class="text-[10px] md:text-xs text-slate-500 font-bold uppercase tracking-tight mt-1">Verify and approve nozzle/sparepart requests from production lines.</p>
                </div>
                <div class="flex items-center gap-3 w-full xl:w-auto">
                    <button class="flex-1 xl:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-strokedark rounded-xl text-[10px] font-black text-slate-700 dark:text-white hover:bg-slate-50 transition-all uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2a4 4 0 00-4-4H5m14 4v2a4 4 0 004 4h1m-4-4a4 4 0 01-8 0v-5h8v5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        History
                    </button>
                </div>
            </div>

            <div class="p-4 md:p-6 flex flex-col lg:flex-row justify-between items-center gap-4 bg-white dark:bg-boxdark">
                <div class="relative w-full lg:w-96">
                    <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" placeholder="SEARCH REQ NUMBER OR LINE..." class="w-full pl-11 pr-4 py-3 border border-slate-200 dark:border-strokedark rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all dark:bg-meta-4 text-[11px] font-black uppercase">
                </div>
            </div>

            <div class="max-w-full overflow-x-auto scrollbar-hide">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="border-y border-slate-100 dark:border-strokedark bg-slate-50/50 dark:bg-meta-4/20">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">REQ No.</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Sparepart Info</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Qty Req</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Machine / Line</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Requestor</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-strokedark font-bold">
                        @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all">
                            <td class="px-6 py-6 text-[11px] text-slate-400 font-black tracking-widest uppercase">
                                {{ $req->request_no }}
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-slate-100 dark:bg-meta-4 flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-strokedark text-indigo-600">
                                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs md:text-sm font-black uppercase leading-none">{{ $req->sparepart_name }}</span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1.5 leading-none">{{ $req->sap_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center text-xs font-black uppercase text-indigo-600">
                                {{ sprintf("%02d", $req->qty_req) }} PCS
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-[10px] font-black uppercase tracking-tight leading-none">{{ $req->line_machine }}</span>
                            </td>
                            <td class="px-6 py-6 text-center uppercase text-[10px] text-slate-500 font-black tracking-widest">
                                {{ $req->requestor }}
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex items-center justify-center gap-3">
                                    
                                    <form action="{{ route('eng.approval.reject', $req->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK request ini?');">
                                        @csrf
                                        <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-md border border-rose-600">
                                            Reject
                                        </button>
                                    </form>

                                    <button type="button" onclick="openApprovalModal('{{ $req->id }}', '{{ $req->request_no }}')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-md border border-emerald-600">
                                        Approve
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-xs font-bold uppercase text-slate-400 tracking-widest">
                                No Incoming Production Requests Queue.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="signatureModal" class="fixed inset-0 z-9999 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-boxdark w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-strokedark overflow-hidden">
        <div class="bg-slate-50 dark:bg-meta-4/30 p-4 border-b border-slate-100 dark:border-strokedark flex justify-between items-center">
            <div>
                <h3 class="text-sm font-black uppercase text-slate-800 dark:text-white">Engineering Authorization</h3>
                <p id="modalReqNo" class="text-[10px] font-bold text-indigo-600 tracking-widest uppercase mt-0.5"></p>
            </div>
            <button type="button" onclick="closeApprovalModal()" class="text-slate-400 hover:text-slate-600 text-sm font-black">✕</button>
        </div>
        
        <form id="approveForm" method="POST">
            @csrf
            <input type="hidden" id="signature_base64" name="signature_image">
            
            <div class="p-6 flex flex-col gap-4">
                <div class="flex flex-col gap-1.5 text-slate-800 dark:text-white">
                    <label class="text-[10px] font-black uppercase tracking-wider text-slate-500">Nama Staff / Leader / SPV *</label>
                    <input type="text" name="approved_by" placeholder="Masukkan Nama Lengkap Anda" class="w-full rounded-xl border-[1.5px] border-stroke bg-transparent py-2.5 px-4 text-xs font-semibold outline-none transition focus:border-primary dark:border-strokedark dark:bg-form-input" required>
                </div>

                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-500">Goreskan Tanda Tangan Asli *</label>
                        <button type="button" onclick="clearCanvas()" class="text-[9px] font-bold text-rose-600 uppercase tracking-wider hover:underline">Clear Canvas</button>
                    </div>
                    <div class="border-[1.5px] border-dashed border-slate-300 dark:border-strokedark bg-slate-50 dark:bg-form-input rounded-xl flex justify-center p-2 bg-white">
                        <canvas id="signaturePad" width="360" height="150" class="cursor-crosshair border border-slate-200 rounded-lg"></canvas>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-meta-4/30 border-t border-slate-100 dark:border-strokedark flex justify-end gap-2">
                <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-wider bg-white">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider">Confirm Approve</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('signatureModal');
    const canvas = document.getElementById('signaturePad');
    const ctx = canvas.getContext('2d');
    const approveForm = document.getElementById('approveForm');
    let isDrawing = false;
    let isSigned = false; // Flag baru untuk validasi tanda tangan

    ctx.strokeStyle = "#000000";
    ctx.lineWidth = 3;
    ctx.lineCap = "round";

    function openApprovalModal(id, reqNo) {
        // FIX 2: Menyesuaikan URL target action form agar cocok dengan rute approve di Controller lo
        approveForm.action = `/eng/approval/approve/${id}`;
        document.getElementById('modalReqNo').innerText = reqNo;
        modal.classList.remove('hidden');
        clearCanvas();
    }

    function closeApprovalModal() {
        modal.classList.add('hidden');
    }

    function getMousePos(canvasDom, event) {
        const rect = canvasDom.getBoundingClientRect();
        const clientX = event.touches ? event.touches[0].clientX : event.clientX;
        const clientY = event.touches ? event.touches[0].clientY : event.clientY;
        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    }

    // Event handler menggambar pada canvas
    canvas.addEventListener('mousedown', (e) => { 
        isDrawing = true; 
        isSigned = true; 
        const pos = getMousePos(canvas, e); 
        ctx.beginPath(); 
        ctx.moveTo(pos.x, pos.y); 
    });
    canvas.addEventListener('mousemove', (e) => { 
        if (!isDrawing) return; 
        const pos = getMousePos(canvas, e); 
        ctx.lineTo(pos.x, pos.y); 
        ctx.stroke(); 
    });
    canvas.addEventListener('mouseup', () => isDrawing = false);
    
    // Support layar sentuh (Mobile/Tablet/Trackpad Mac)
    canvas.addEventListener('touchstart', (e) => { 
        isDrawing = true; 
        isSigned = true; 
        const pos = getMousePos(canvas, e); 
        ctx.beginPath(); 
        ctx.moveTo(pos.x, pos.y); 
        e.preventDefault(); 
    });
    canvas.addEventListener('touchmove', (e) => { 
        if (!isDrawing) return; 
        const pos = getMousePos(canvas, e); 
        ctx.lineTo(pos.x, pos.y); 
        ctx.stroke(); 
        e.preventDefault(); 
    });
    canvas.addEventListener('touchend', () => isDrawing = false);

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('signature_base64').value = "";
        isSigned = false;
    }

    // Proses Submit Form Validation
    approveForm.addEventListener('submit', function(e) {
        // FIX 3: Menggunakan flag isSigned yang 100% akurat mencegah macet akibat kesalahan kalkulasi string base64 kosong
        if (!isSigned) {
            alert('Tanda tangan wajib diisi sebelum menyetujui request!');
            e.preventDefault();
            return;
        }
        
        document.getElementById('signature_base64').value = canvas.toDataURL();
    });
</script>
@endsection