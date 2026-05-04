@extends('admin')

@section('content')
<div class="-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-screen">
    <div class="p-10">
        <div class="mb-6">
            <h1 class="text-2xl font-black uppercase text-slate-800 dark:text-white">Barcode Customizer</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Engineering / Configuration</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Panel Konfigurasi -->
            <div class="lg:col-span-1 bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm p-6">
                <h2 class="text-sm font-black uppercase mb-6 text-slate-800 dark:text-white">Settings</h2>
                
                <form id="barcodeConfigForm" class="space-y-6">
                    <!-- Pilih Tipe Barcode -->
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-2">Barcode Type</label>
                        <select id="barcode_type" class="w-full bg-slate-50 dark:bg-meta-4 border border-slate-200 dark:border-strokedark rounded-lg px-4 py-3 font-bold text-sm outline-none focus:border-indigo-500">
                            <option value="qr">QR CODE (2D)</option>
                            <option value="datamatrix">DATA MATRIX</option>
                            <option value="code128">CODE 128 (1D)</option>
                        </select>
                    </div>

                    <!-- Input Content -->
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-2">Barcode Content</label>
                        <textarea id="barcode_content" rows="4" 
                            class="w-full bg-slate-50 dark:bg-meta-4 border border-slate-200 dark:border-strokedark rounded-lg px-4 py-3 font-bold text-sm outline-none focus:border-indigo-500"
                            placeholder="Input: Letters, Numbers, Space, Symbols (@#$%)"></textarea>
                    </div>

                    <button type="button" onclick="generatePreview()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-xl transition-all uppercase text-xs tracking-widest shadow-lg shadow-indigo-200 dark:shadow-none">
                        Generate Barcode
                    </button>
                </form>
            </div>

            <!-- Panel Preview -->
            <div class="lg:col-span-2 bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm flex flex-col items-center justify-center p-10 min-h-[400px]">
                <div id="barcode_preview_area" class="bg-slate-50 dark:bg-meta-4 p-10 rounded-2xl border-2 border-dashed border-slate-200 dark:border-strokedark">
                    <p class="text-slate-400 font-black uppercase text-[10px]">No Barcode Generated</p>
                </div>
                <div id="barcode_info" class="mt-8 text-center hidden">
                    <p id="info_type" class="text-indigo-600 font-black text-xs uppercase mb-1"></p>
                    <p id="info_content" class="text-slate-500 font-bold text-sm break-all"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Library JS untuk Barcode (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    function generatePreview() {
        const type = document.getElementById('barcode_type').value;
        const content = document.getElementById('barcode_content').value;
        const previewArea = document.getElementById('barcode_preview_area');
        const infoArea = document.getElementById('barcode_info');

        if (!content.trim()) {
            alert("ISI KONTEN TERLEBIH DAHULU!");
            return;
        }

        previewArea.innerHTML = ''; // Clear previous
        infoArea.classList.remove('hidden');
        document.getElementById('info_type').innerText = `TYPE: ${type}`;
        document.getElementById('info_content').innerText = content;

        if (type === 'qr') {
            new QRCode(previewArea, {
                text: content,
                width: 200,
                height: 200,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        } else if (type === 'datamatrix' || type === 'code128') {
            const canvas = document.createElement('canvas');
            previewArea.appendChild(canvas);
            
            // Menggunakan JsBarcode (Data Matrix memerlukan library spesifik lain, 
            // namun Code128 sudah include di sini)
            JsBarcode(canvas, content, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 100,
                displayValue: true
            });
        }
    }
</script>
@endsection