@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-4xl pb-10">
    <div class="mb-6 px-4">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
            <span class="h-6 w-1.5 bg-danger rounded-full"></span>
            Stock Out Production
        </h2>
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Pemakaian Sparepart di Line Produksi</p>
    </div>

    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('prod.transaction.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="OUT">
            
            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                <div class="md:col-span-2 space-y-2 group">
                    <label class="text-[10px] font-black uppercase text-slate-500 transition-colors group-focus-within:text-danger">Scan Sparepart / SAP Code</label>
                    <div class="relative">
                        <input type="text" name="sap_code" placeholder="Scan Barcode Keluar..." class="w-full rounded-lg border-[1.5px] border-danger bg-danger/5 py-4 pl-12 pr-5 text-sm font-bold text-danger outline-none focus:border-danger transition dark:bg-meta-4" autofocus required>
                        <i data-feather="minimize" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-danger"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-500">Operator / PIC</label>
                    <input type="text" name="pic" placeholder="Nama Operator" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none focus:border-danger dark:border-strokedark dark:bg-form-input dark:text-white" required>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-500">Jumlah Pakai (Qty)</label>
                    <input type="number" name="qty" placeholder="0" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none focus:border-danger dark:border-strokedark dark:bg-form-input dark:text-white" required>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-500">Alasan Pemakaian</label>
                    <select name="reason" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none focus:border-danger dark:border-strokedark dark:bg-form-input dark:text-white">
                        <option value="Replacement">Regular Replacement</option>
                        <option value="Broken">Broken / Damage</option>
                        <option value="Maintenance">Scheduled Maintenance</option>
                    </select>
                </div>

                <div class="md:col-span-2 pt-4">
                    <button type="submit" class="w-full rounded-lg bg-danger py-3.5 font-black uppercase text-white hover:bg-opacity-90 shadow-lg shadow-danger/20 transition-all active:scale-95">
                        Confirm Stock Out
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