@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-4xl pb-10">
    <div class="mb-6 px-4">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
            <span class="h-6 w-1.5 bg-success rounded-full"></span>
            Stock In Production
        </h2>
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Penerimaan Barang dari Engineering</p>
    </div>

    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('prod.transaction.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="IN">
            
            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                <div class="md:col-span-2 space-y-2 group">
                    <label class="text-[10px] font-black uppercase text-slate-500 transition-colors group-focus-within:text-success">Scan Sparepart / SAP Code</label>
                    <div class="relative">
                        <input type="text" name="sap_code" placeholder="Scan Barcode Masuk..." class="w-full rounded-lg border-[1.5px] border-success bg-success/5 py-4 pl-12 pr-5 text-sm font-bold text-success outline-none focus:border-success transition dark:bg-meta-4" autofocus required>
                        <i data-feather="maximize" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-success"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-500">Nama Penerima</label>
                    <input type="text" name="pic" placeholder="Nama Anda" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none focus:border-success dark:border-strokedark dark:bg-form-input dark:text-white" required>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-500">Jumlah Masuk (Qty)</label>
                    <input type="number" name="qty" placeholder="0" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none focus:border-success dark:border-strokedark dark:bg-form-input dark:text-white" required>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-500">Keterangan / Remark</label>
                    <textarea name="remark" rows="2" placeholder="Contoh: Barang sudah dicek" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none focus:border-success dark:border-strokedark dark:bg-form-input dark:text-white"></textarea>
                </div>

                <div class="md:col-span-2 pt-4">
                    <button type="submit" class="w-full rounded-lg bg-success py-3.5 font-black uppercase text-white hover:bg-opacity-90 shadow-lg shadow-success/20 transition-all active:scale-95">
                        Confirm Stock In
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection