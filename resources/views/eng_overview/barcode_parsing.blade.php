@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-screen">
    <div class="p-10">
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black uppercase text-slate-800 dark:text-white">Barcode Customizer</h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Engineering / Configuration</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 flex flex-col gap-6">
                <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm p-6">
                    <h2 class="text-sm font-black uppercase mb-6 text-slate-800 dark:text-white">Barcode Builder</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 mb-2">Character Type</label>
                            <select id="char_type" class="w-full bg-slate-50 dark:bg-meta-4 border border-slate-200 dark:border-strokedark rounded-lg px-4 py-3 font-bold text-sm outline-none focus:border-indigo-500">
                                <option value="TEXT">TEXT (A-Z, a-z)</option>
                                <option value="NUMBER">NUMBER (0-9)</option>
                                <option value="CODE">CODE (!@#$%^&*())</option>
                                <option value="EVERYTHING">EVERYTHING (Combination)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 mb-2">Character Length (1-999)</label>
                            <input type="number" id="char_length" min="1" max="999" value="5" class="w-full bg-slate-50 dark:bg-meta-4 border border-slate-200 dark:border-strokedark rounded-lg px-4 py-3 font-bold text-sm outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 mb-2">Value Input</label>
                            <input type="text" id="char_value" placeholder="Type here..." class="w-full bg-slate-50 dark:bg-meta-4 border border-slate-200 dark:border-strokedark rounded-lg px-4 py-3 font-bold text-sm outline-none focus:border-indigo-500">
                        </div>

                        <button type="button" onclick="addComponent()" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-black py-3.5 rounded-xl transition-all uppercase text-xs tracking-widest border-none outline-none">
                            + Add To Composite
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm p-6">
                    <h2 class="text-sm font-black uppercase mb-6 text-slate-800 dark:text-white">Final Settings</h2>
                    
                    <form id="barcodeConfigForm" class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 mb-2">Barcode Type</label>
                            <select id="barcode_type" class="w-full bg-slate-50 dark:bg-meta-4 border border-slate-200 dark:border-strokedark rounded-lg px-4 py-3 font-bold text-sm outline-none focus:border-indigo-500">
                                <option value="QR CODE">QR CODE</option>
                                <option value="2D CODE">2D CODE (PDF417)</option>
                                <option value="3D CODE">3D CODE (Color Barcode)</option>
                                <option value="DATA MATRIX">DATA MATRIX</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 mb-2">SIZE BARCODE</label>
                            <select id="barcode_size" class="w-full bg-slate-50 dark:bg-meta-4 border border-slate-200 dark:border-strokedark rounded-lg px-4 py-3 font-bold text-sm outline-none focus:border-indigo-500">
                                <option value="1">Size 1mm x 1mm</option>
                                <option value="2">Size 2mm x 2mm</option>
                                <option value="3">Size 3mm x 3mm</option>
                                <option value="4">Size 4mm x 4mm</option>
                                <option value="5">Size 5mm x 5mm</option>
                                <option value="8">Size 8mm x 8mm</option>
                                <option value="10" selected>Size 10mm x 10mm</option>
                                <option value="15">Size 15mm x 15mm</option>
                                <option value="20">Size 20mm x 20mm</option>
                                <option value="25">Size 25mm x 25mm</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 mb-2">Final Composite Content</label>
                            <textarea id="barcode_content" rows="3" readonly
                                class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-strokedark rounded-lg px-4 py-3 font-black text-sm text-indigo-600 outline-none cursor-not-allowed"
                                placeholder="Composite result will appear here..."></textarea>
                        </div>

                        <div class="flex items-center justify-center gap-1.5 pt-2">
                            <button type="button" onclick="clearComposite()" class="flex-1 bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 hover:from-red-600 hover:via-orange-600 hover:to-yellow-600 text-white font-black py-2.5 rounded-xl transition-all uppercase text-[10px] tracking-wider border-none outline-none text-center shadow-sm">
                                Clear
                            </button>
                            <button type="button" onclick="triggerPreviewOnly()" class="flex-1 bg-gradient-to-r from-blue-700 to-indigo-800 hover:from-blue-800 hover:to-indigo-950 text-white font-black py-2.5 rounded-xl transition-all uppercase text-[10px] tracking-wider border-none outline-none text-center shadow-sm">
                                Preview
                            </button>
                            <button type="button" onclick="submitToDatabase()" class="flex-1 bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-700 hover:to-green-800 text-white font-black py-2.5 rounded-xl transition-all uppercase text-[10px] tracking-wider border-none outline-none text-center shadow-sm">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 flex flex-col gap-6">
                <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-black uppercase text-slate-800 dark:text-white">Composite Structure List</h2>
                        
                        <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-black px-3.5 py-2 rounded-lg transition-all uppercase text-[10px] tracking-wider border-none outline-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12l4.5 4.5m0 0l4.5-4.5M12 16.5V3"></path>
                            </svg>
                            Import Config
                        </button>
                    </div>
                    
                    <div id="composite_list" class="flex flex-wrap gap-2 min-h-[50px] p-3 bg-slate-50 dark:bg-meta-4 rounded-xl border border-dashed border-slate-200 dark:border-strokedark items-center">
                        <p id="empty_list_msg" class="text-[10px] font-black uppercase text-slate-400 mx-auto">No components added yet</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm flex flex-col items-center justify-center p-10 min-h-[420px] grow">
                    <div id="barcode_preview_area" class="bg-slate-50 dark:bg-meta-4 p-8 rounded-2xl border-2 border-dashed border-slate-200 dark:border-strokedark flex flex-col items-center justify-center min-w-[200px] min-h-[200px]">
                        <p class="text-slate-400 font-black uppercase text-[10px]">No Barcode Generated</p>
                    </div>
                    
                    <div id="barcode_info" class="mt-6 text-center hidden">
                        <p id="info_type" class="text-indigo-600 font-black text-xs uppercase mb-1"></p>
                        <p id="info_content" class="text-slate-500 font-bold text-sm break-all max-w-md mb-6"></p>
                        
                        <button type="button" onclick="downloadBarcode()" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-amber-400 hover:from-emerald-600 hover:to-amber-500 text-white font-black px-6 py-3.5 rounded-xl transition-all uppercase text-xs tracking-widest border-none outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                            </svg>
                            Download Barcode (.PNG)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="importModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark w-full max-w-lg p-6 shadow-xl mx-4">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-strokedark">
            <h3 class="text-sm font-black uppercase text-slate-800 dark:text-white">Import Composite Configuration</h3>
            <button onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 font-black text-xl">&times;</button>
        </div>
        <div class="max-h-[300px] overflow-y-auto space-y-2" id="modal_config_list">
            <p class="text-xs text-slate-400 text-center py-4">Loading configurations...</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bwip-js@3.0.4/dist/bwip-js-min.js"></script>

<script>
    let compositeComponents = [];

    function addComponent() {
        const charType = document.getElementById('char_type').value;
        const charLength = parseInt(document.getElementById('char_length').value) || 0;
        let charValue = document.getElementById('char_value').value;

        if (charLength < 1 || charLength > 999) { 
            return Swal.fire({ icon: 'error', title: 'Batas Karakter', text: 'PANJANG KARAKTER HARUS ANTARA 1 - 999!' }); 
        }
        if (!charValue.trim()) { 
            return Swal.fire({ icon: 'warning', title: 'Input Kosong', text: 'INPUT VALUE TIDAK BOLEH KOSONG!' }); 
        }

        let regex;
        let errorMsg = "";
        if (charType === "TEXT") { regex = /^[A-Za-z\s]+$/; errorMsg = "Hanya karakter HURUF/TEXT!"; }
        else if (charType === "NUMBER") { regex = /^[0-9]+$/; errorMsg = "Hanya karakter ANGKA/NUMBER!"; }
        else if (charType === "CODE") { regex = /^[\!@#\$%\^&\*\(\)\-\_\+=\[\]\{\};':",\./<>\?\\\|`~]+$/; errorMsg = "Hanya karakter SYMBOL/CODE!"; }

        if (regex && !regex.test(charValue)) { 
            return Swal.fire({ icon: 'error', title: 'Format Salah', text: errorMsg }); 
        }

        if (charValue.length > charLength) {
            Swal.fire({ icon: 'info', title: 'Informasi', text: `Value dipotong otomatis menjadi ${charLength} karakter!` });
            charValue = charValue.substring(0, charLength);
        }

        compositeComponents.push({ type: charType, length: charLength, value: charValue });
        document.getElementById('char_value').value = '';
        renderComposite();
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Komponen berhasil ditambahkan ke list composite!',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function renderComposite() {
        const listArea = document.getElementById('composite_list');
        const emptyMsg = document.getElementById('empty_list_msg');
        const textareaContent = document.getElementById('barcode_content');

        listArea.innerHTML = '';
        if (compositeComponents.length === 0) {
            listArea.appendChild(emptyMsg);
            textareaContent.value = '';
            return;
        }

        let finalString = "";
        compositeComponents.forEach((comp, index) => {
            finalString += comp.value;
            const badge = document.createElement('div');
            badge.className = "flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 font-black text-[10px] px-3 py-1.5 rounded-lg border border-indigo-100 dark:border-indigo-800 uppercase";
            badge.innerHTML = `
                <span>[${comp.type} | len:${comp.length}] ${comp.value}</span>
                <button type="button" onclick="removeComponent(${index})" class="text-rose-500 hover:text-rose-700 font-bold ml-1">×</button>
            `;
            listArea.appendChild(badge);
        });
        textareaContent.value = finalString;
    }

    function removeComponent(index) {
        compositeComponents.splice(index, 1);
        renderComposite();
    }

    function clearComposite() {
        compositeComponents = [];
        renderComposite();
        document.getElementById('barcode_preview_area').innerHTML = '<p class="text-slate-400 font-black uppercase text-[10px]">No Barcode Generated</p>';
        document.getElementById('barcode_info').classList.add('hidden');
    }

    function triggerPreviewOnly() {
        const content = document.getElementById('barcode_content').value;
        if (!content.trim() || compositeComponents.length === 0) {
            return Swal.fire({ icon: 'error', title: 'Preview Gagal', text: 'Struktur composite masih kosong! Tambahkan komponen dulu.' });
        }
        generatePreview();
    }

    async function submitToDatabase() {
        const type = document.getElementById('barcode_type').value;
        const content = document.getElementById('barcode_content').value;
        
        const sizeSelect = document.getElementById('barcode_size');
        const sizeText = sizeSelect.options[sizeSelect.selectedIndex].text;

        if (!content.trim() || compositeComponents.length === 0) {
            return Swal.fire({ icon: 'error', title: 'Simpan Gagal', text: 'Struktur composite masih kosong! Tidak ada data untuk disimpan.' });
        }

        const resultConfirm = await Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Silahkan check lagi data composite anda sebelum menekan tombol simpan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Cek Lagi'
        });

        if (!resultConfirm.isConfirmed) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch('/eng-overview/barcode-parsing/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    barcode_type: type,
                    barcode_size: sizeText, 
                    final_content: content,
                    components: compositeComponents
                })
            });

            if (response.redirected) {
                await Swal.fire({ icon: 'success', title: 'Sukses', text: 'Barcode Berhasil Dibuat dan Disimpan!' });
                generatePreview();
                return;
            }

            const result = await response.json();
            if (result.success || response.status === 200) {
                await Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data berhasil di-generate dan masuk ke database!' });
                generatePreview();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal Validasi', text: result.message || 'Periksa log validasi controller!' });
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error Sistem', text: 'Terjadi kegagalan saat menghubungi server database.' });
            generatePreview();
        }
    }

    async function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
        const modalList = document.getElementById('modal_config_list');
        modalList.innerHTML = '<p class="text-xs text-slate-400 text-center py-4">Loading configurations...</p>';

        try {
            const response = await fetch('/eng-overview/barcode-parsing/get-configs');
            const configs = await response.json();

            modalList.innerHTML = '';
            if (configs.length === 0) {
                modalList.innerHTML = '<p class="text-xs text-slate-400 text-center py-4">Belum ada template konfigurasi di DB.</p>';
                return;
            }

            configs.forEach(config => {
                const row = document.createElement('div');
                row.className = "flex items-center justify-between p-3 bg-slate-50 dark:bg-meta-4 rounded-xl border border-slate-100 dark:border-strokedark hover:border-indigo-500 transition-all cursor-pointer";
                row.onclick = () => applyConfig(config.components_json);
                row.innerHTML = `
                    <div class="text-left">
                        <p class="text-xs font-black text-slate-700 dark:text-white uppercase">[${config.char_type}] Length: ${config.char_length}</p>
                        <p class="text-[10px] text-slate-400 font-bold break-all">Sample Val: ${config.char_value}</p>
                    </div>
                    <span class="text-[9px] bg-indigo-100 text-indigo-600 font-black px-2 py-1 rounded">SELECT</span>
                `;
                modalList.appendChild(row);
            });
        } catch (error) {
            modalList.innerHTML = '<p class="text-xs text-rose-500 text-center py-4">Gagal memuat data!</p>';
        }
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function applyConfig(componentsJson) {
        compositeComponents = JSON.parse(componentsJson);
        renderComposite();
        closeImportModal();
        Swal.fire({ icon: 'success', title: 'Import Sukses', text: 'Konfigurasi struktur berhasil di-import!', timer: 1500, showConfirmButton: false });
    }

    function generatePreview() {
        const type = document.getElementById('barcode_type').value;
        const content = document.getElementById('barcode_content').value;
        const sizeMm = parseInt(document.getElementById('barcode_size').value) || 10;
        const previewArea = document.getElementById('barcode_preview_area');
        const infoArea = document.getElementById('barcode_info');

        previewArea.innerHTML = ''; 
        infoArea.classList.remove('hidden');
        document.getElementById('info_type').innerText = `TYPE: ${type} (PCB SIZE: ${sizeMm}x${sizeMm} mm)`;
        document.getElementById('info_content').innerText = content;

        let renderPixelSize = 100 + ((sizeMm - 1) * 10.5);

        if (type === 'QR CODE') {
            new QRCode(previewArea, { 
                text: content, 
                width: renderPixelSize, 
                height: renderPixelSize, 
                colorDark : "#000000", 
                colorLight : "#ffffff", 
                correctLevel : QRCode.CorrectLevel.H 
            });
        } else if (type === 'DATA MATRIX') {
            const canvas = document.createElement('canvas');
            previewArea.appendChild(canvas);
            try {
                let targetScale = sizeMm <= 3 ? 2 : (sizeMm <= 8 ? 4 : 6);
                bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: content, scale: targetScale, include0: true });
            } catch (e) { previewArea.innerHTML = `<span class="text-xs font-bold text-rose-500">${e.message}</span>`; }
        } else if (type === '2D CODE') {
            const canvas = document.createElement('canvas');
            previewArea.appendChild(canvas);
            try {
                bwipjs.toCanvas(canvas, { bcid: 'pdf417', text: content, scale: sizeMm <= 4 ? 1 : 2, height: sizeMm <= 4 ? 8 : 12, columns: 3 });
                canvas.style.width = `${renderPixelSize}px`;
            } catch (e) { previewArea.innerHTML = `<span class="text-xs font-bold text-rose-500">${e.message}</span>`; }
        } else if (type === '3D CODE') {
            const canvas = document.createElement('canvas');
            canvas.width = renderPixelSize; canvas.height = renderPixelSize;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = "#4f46e5"; ctx.fillRect(0, 0, renderPixelSize, renderPixelSize);
            ctx.fillStyle = "#10b981"; ctx.fillRect(renderPixelSize*0.15, renderPixelSize*0.15, renderPixelSize*0.7, renderPixelSize*0.7);
            ctx.fillStyle = "#f59e0b"; ctx.fillRect(renderPixelSize*0.3, renderPixelSize*0.3, renderPixelSize*0.4, renderPixelSize*0.4);
            ctx.fillStyle = "#000000"; ctx.fillRect(renderPixelSize*0.4, renderPixelSize*0.4, renderPixelSize*0.2, renderPixelSize*0.2);
            previewArea.appendChild(canvas);
        }
    }

    function downloadBarcode() {
        const previewArea = document.getElementById('barcode_preview_area');
        const contentText = document.getElementById('barcode_content').value;
        const barcodeType = document.getElementById('barcode_type').value;
        const canvas = previewArea.querySelector('canvas');
        const img = previewArea.querySelector('img');

        if (canvas) {
            triggerDownload(canvas.toDataURL("image/png"), barcodeType, contentText);
        } else if (img && img.src) {
            triggerDownload(img.src, barcodeType, contentText);
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal mengunduh barcode!' });
        }
    }

    function triggerDownload(url, type, content) {
        const link = document.createElement('a');
        const safeName = content.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        link.href = url;
        link.download = `barcode_${type.replace(/\s+/g, '_').toLowerCase()}_${safeName}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection