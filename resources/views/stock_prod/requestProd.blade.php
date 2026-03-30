@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-5xl pb-10">
    <div class="mb-6 px-4 sm:px-0">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
            <span class="h-6 w-1.5 bg-primary rounded-full"></span>
            New Sparepart Request
        </h2>
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Production Department Form</p>
    </div>

    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-sm overflow-hidden">
        <form action="#" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="p-4 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-8">
                    
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Nama Lengkap</label>
                        <input type="text" name="nama" placeholder="Masukkan Nama" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white" required>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">NIK (Nomor Induk Karyawan)</label>
                        <input type="text" name="nik" placeholder="Contoh: 20240101" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white" required>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Shift Kerja</label>
                        <select name="shift" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white">
                            <option value="1">Shift 1 (Pagi)</option>
                            <option value="2">Shift 2 (Sore)</option>
                            <option value="3">Shift 3 (Malam)</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Target Line</label>
                        <input type="text" name="line" placeholder="Contoh: Line 04 - SMT" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white" required>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Sparepart Type / SAP Code</label>
                        <input type="text" name="sparepart_type" placeholder="Scan or Type SAP Code" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-bold text-primary outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input" required>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Quantity (Jumlah)</label>
                        <input type="number" name="qty" placeholder="0" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white" required>
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Update File / Evidence (Opsional)</label>
                        <input type="file" name="file" class="w-full cursor-pointer rounded-lg border-[1.5px] border-stroke bg-transparent font-medium outline-none transition file:mr-5 file:border-collapse file:cursor-pointer file:border-0 file:border-r file:border-solid file:border-stroke file:bg-whiter file:py-3 file:px-5 file:hover:bg-primary file:hover:bg-opacity-10 focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:file:border-strokedark dark:file:bg-white/5 dark:file:text-white dark:focus:border-primary" />
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Remark / Alasan Permintaan</label>
                        <textarea rows="3" name="remark" placeholder="Contoh: Nozzle patah saat pemasangan" class="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-strokedark dark:bg-form-input dark:text-white"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button type="reset" class="flex justify-center rounded-lg border border-stroke py-3 px-8 text-sm font-bold uppercase text-slate-600 hover:bg-slate-50 dark:border-strokedark dark:text-white">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection