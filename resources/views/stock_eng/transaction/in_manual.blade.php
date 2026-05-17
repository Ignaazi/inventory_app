@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 md:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
        
        <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Create Manual IN</h2>
            </div>
            
            <a href="{{ route('eng.in') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 px-4 py-2.5 text-xs font-bold text-white shadow-md transition-all">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <form action="{{ route('eng.in.store') }}" method="POST">
            @csrf
            <input type="hidden" name="source" value="manual">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">No Nozzle</label>
                    <div class="relative">
                        <select name="stock_eng_id" id="select-manual" required 
                                class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 appearance-none">
                            <option value="">-- Nozzle Type --</option>
                            @foreach($stocks as $item)
                                <option value="{{ $item->id }}">{{ $item->no_nozzle }} | {{ $item->sap_code }} | Stock: {{ $item->qty }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    @error('stock_eng_id')
                        <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Amount Of Incoming Stock <s></s></label>
                    <input type="number" name="qty_in" required min="1" placeholder="Enter The Quantity"
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @error('qty_in')
                        <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Remark</label>
                    <textarea name="remark" rows="3" placeholder="Receipt Info (optional)" 
                              class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                </div>

            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-all uppercase tracking-wider">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection