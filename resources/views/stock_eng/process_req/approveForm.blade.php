@extends('admin')

@section('content')
<div class="-m-4 md:-m-6 2xl:-m-10 bg-slate-100 dark:bg-boxdark-2 min-h-[calc(100vh-80px)] font-sans p-4 md:p-8">
    
    <div class="max-w-5xl mx-auto mb-4">
        <a href="{{ route('eng.approval') }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to List Approval
        </a>
    </div>

    <div class="max-w-5xl mx-auto bg-white dark:bg-boxdark border border-slate-300 dark:border-strokedark shadow-xl rounded-xl p-6 md:p-10 text-slate-900 dark:text-white">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b-2 border-slate-900 dark:border-slate-600 pb-4 mb-6 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 border border-dashed border-slate-400 flex items-center justify-center text-[9px] font-black uppercase tracking-tight text-slate-400 text-center p-1 rounded">
                    SIIX LOGO
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-950 dark:text-white">PT. SIIX EMS KARAWANG</h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">ELECTRONIC MANUFACTURING SERVICES</p>
                </div>
            </div>
            <div class="border-2 border-slate-950 dark:border-slate-500 p-2 text-center min-w-[200px]">
                <h2 class="text-xs font-black tracking-widest uppercase">FORM REQUEST NOZZLE</h2>
                <p class="text-[8px] font-mono font-bold text-slate-400 mt-0.5">Doc No: FORM/SEK/REQ/{{ date('Y/m') }}/XXXX</p>
            </div>
        </div>

        <div class="overflow-x-auto border border-slate-950 dark:border-slate-700 mb-6">
            <table class="w-full border-collapse text-left text-xs uppercase font-bold">
                <tbody>
                    <tr class="border-b border-slate-950 dark:border-slate-700">
                        <td class="w-1/3 px-4 py-3 bg-slate-50 dark:bg-meta-4/30 border-r border-slate-950 dark:border-slate-700 font-black tracking-wide">NAME</td>
                        <td class="px-4 py-3 font-black text-slate-950 dark:text-white">{{ $req->requestor }}</td>
                    </tr>
                    <tr class="border-b border-slate-950 dark:border-slate-700">
                        <td class="px-4 py-3 bg-slate-50 dark:bg-meta-4/30 border-r border-slate-950 dark:border-slate-700 font-black tracking-wide">LINE</td>
                        <td class="px-4 py-3 font-black text-slate-950 dark:text-white">{{ $req->line_machine }}</td>
                    </tr>
                    <tr class="border-b border-slate-950 dark:border-slate-700">
                        <td class="px-4 py-3 bg-slate-50 dark:bg-meta-4/30 border-r border-slate-950 dark:border-slate-700 font-black tracking-wide">NO NOZLE</td>
                        <td class="px-4 py-3 font-black text-indigo-600 dark:text-indigo-400 text-sm">{{ $req->sparepart_name }}</td>
                    </tr>
                    <tr class="border-b border-slate-950 dark:border-slate-700">
                        <td class="px-4 py-3 bg-slate-50 dark:bg-meta-4/30 border-r border-slate-950 dark:border-slate-700 font-black tracking-wide">SAP CODE</td>
                        <td class="px-4 py-3 font-mono font-black text-slate-950 dark:text-white tracking-widest">{{ $req->sap_code }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 bg-slate-50 dark:bg-meta-4/30 border-r border-slate-950 dark:border-slate-700 font-black tracking-wide">QUANTITY REQUESTED</td>
                        <td class="px-4 py-3 font-black text-slate-950 dark:text-white">{{ $req->qty_req }} Pcs</td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="border border-slate-950 dark:border-slate-700 overflow-hidden">
            
            <div class="grid grid-cols-3 text-center text-[9px] font-black uppercase tracking-wider bg-slate-50 dark:bg-meta-4/30 border-b border-slate-950 dark:border-slate-700 py-1.5">
                <div class="border-r border-slate-950 dark:border-slate-700">REQUESTED BY</div>
                <div class="border-r border-slate-950 dark:border-slate-700 text-indigo-600 dark:text-indigo-400">CHECKED BY</div>
                <div>APPROVED BY</div>
            </div>
            
            <div class="grid grid-cols-3 min-h-[140px] text-center">
                
                <div class="border-r border-slate-950 dark:border-slate-700 p-2 flex flex-col justify-between items-center bg-white dark:bg-boxdark">
                    <div class="flex items-center justify-center flex-1 w-full p-2">
                        
                        @php
                            $validSignature = false;
                            if ($req->production_signature && 
                                $req->production_signature !== 'null' && 
                                str_starts_with($req->production_signature, 'data:image')) {
                                $validSignature = true;
                            }
                        @endphp

                        @if($validSignature)
                            <img src="{{ $req->production_signature }}" 
                                 alt="Production Sign" 
                                 class="max-h-20 max-w-full object-contain mix-blend-multiply dark:mix-blend-normal">
                        @else
                            @if(is_null($req->production_signature))
                                <span class="text-[9px] text-rose-500 font-black uppercase italic">[Ttd Kosong (NULL)]</span>
                            @elseif($req->production_signature === 'null')
                                <span class="text-[9px] text-rose-500 font-black uppercase italic">[Ttd Is String 'null']</span>
                            @else
                                <span class="text-[9px] text-rose-500 font-black uppercase italic">[Ttd Error (Invalid Base64)]</span>
                            @endif
                        @endif
                    </div>
                    <div class="w-full">
                        <p class="font-black text-xs underline text-slate-950 dark:text-white">{{ $req->requestor }}</p>
                        <p class="text-[8px] text-slate-400 font-bold mt-0.5">PRODUCTION DEPARTMENT</p>
                    </div>
                </div>

                <div class="border-r border-slate-950 dark:border-slate-700 p-2 flex flex-col justify-between items-center bg-indigo-50/10 relative">
                    
                    <div class="w-full flex-1 flex flex-col items-center justify-center relative rounded-lg border border-dashed border-indigo-300 dark:border-indigo-900 bg-white dark:bg-form-input overflow-hidden shadow-inner">
                        <canvas id="engSignaturePad" class="w-full h-full block touch-none cursor-crosshair bg-white"></canvas>
                        <button type="button" id="clearPadBtn" class="absolute bottom-1 right-1 px-1.5 py-0.5 bg-slate-100 hover:bg-slate-200 dark:bg-meta-4 text-slate-700 dark:text-white font-black text-[8px] uppercase rounded border transition-all shadow-sm z-10">
                            Clear
                        </button>
                    </div>
                    
                    <div class="w-full mt-2">
                        <p class="font-black text-xs text-indigo-600 dark:text-indigo-400">
                            ( <span class="underline inline-block px-2">{{ auth()->check() ? auth()->user()->name : 'STAFF ENGINEERING' }}</span> )
                        </p>
                        <p class="text-[8px] text-slate-400 font-bold mt-0.5">STAFF ENGINEERING</p>
                    </div>
                </div>

                <div class="p-2 flex flex-col justify-between items-center bg-slate-50/50 dark:bg-meta-4/10 opacity-50">
                    <div class="flex items-center justify-center flex-1">
                        <span class="text-[8px] font-bold text-slate-400 italic tracking-wide">( Waiting SPV Approval )</span>
                    </div>
                    <div class="w-full">
                        <p class="font-black text-xs text-slate-400">( ___________________ )</p>
                        <p class="text-[8px] text-slate-400 font-bold mt-0.5">SPV ENGINEERING</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t border-slate-200 dark:border-strokedark pt-4">
            
            <form action="{{ route('eng.approval.reject', $req->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK request form ini?');">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-black text-[10px] uppercase tracking-widest rounded-xl border border-rose-200 transition-all">
                    Reject Form
                </button>
            </form>

            <form id="submitApproveForm" action="{{ route('eng.approval.approve', $req->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Approved">
                <input type="hidden" name="approved_by" value="{{ auth()->check() ? auth()->user()->name : 'STAFF ENGINEERING' }}">
                <input type="hidden" id="rawSignatureData" name="signature_image">
                
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] uppercase tracking-widest rounded-xl shadow-md border border-emerald-600 transition-all">
                    Approve & Sign Form
                </button>
            </form>

        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const canvas = document.getElementById("engSignaturePad");
        const clearBtn = document.getElementById("clearPadBtn");
        const mainForm = document.getElementById("submitApproveForm");
        const hiddenInput = document.getElementById("rawSignatureData");
        
        const ctx = canvas.getContext("2d");
        
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        
        ctx.strokeStyle = "#000000"; 
        ctx.lineWidth = 2.5;
        ctx.lineCap = "round";
        
        let isDrawing = false;

        function getCoordinates(e) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (e.clientX || e.touches[0].clientX) - rect.left,
                y: (e.clientY || e.touches[0].clientY) - rect.top
            };
        }

        function startDrawing(e) {
            isDrawing = true;
            const coords = getCoordinates(e);
            ctx.beginPath();
            ctx.moveTo(coords.x, coords.y);
            e.preventDefault();
        }

        function draw(e) {
            if (!isDrawing) return;
            const coords = getCoordinates(e);
            ctx.lineTo(coords.x, coords.y);
            ctx.stroke();
            e.preventDefault();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        canvas.addEventListener("mousedown", startDrawing);
        canvas.addEventListener("mousemove", draw);
        canvas.addEventListener("mouseup", stopDrawing);

        canvas.addEventListener("touchstart", startDrawing, { passive: false });
        canvas.addEventListener("touchmove", draw, { passive: false });
        canvas.addEventListener("touchend", stopDrawing);

        clearBtn.addEventListener("click", function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        mainForm.addEventListener("submit", function (e) {
            const checkCanvas = document.createElement('canvas');
            checkCanvas.width = canvas.width;
            checkCanvas.height = canvas.height;
            
            if (canvas.toDataURL() === checkCanvas.toDataURL()) {
                e.preventDefault();
                alert("🚨 Kolom 'CHECKED BY' belum ditandatangani! Harap berikan tanda tangan Engineering sebelum menekan tombol Approve.");
                return;
            }

            hiddenInput.value = canvas.toDataURL();
        });
    });
</script>
@endsection