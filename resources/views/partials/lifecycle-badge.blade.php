@php
    $lifecycle = strtoupper((string) ($lifecycle ?? 'UNKNOWN'));
    $lifecycleDescriptions = [
        'AVAILABLE' => 'Barcode terdaftar dan belum dipakai pada transaksi.',
        'USED_IN' => 'Barcode sudah tercatat masuk pada Engineering atau Production.',
        'USED_OUT' => 'Barcode sudah tercatat keluar dan siap diproses berikutnya.',
        'RETURNED' => 'Barcode sudah dikembalikan dan tidak dapat dipakai ulang.',
        'DISPOSAL' => 'Barcode sudah dimusnahkan dan tidak dapat dipakai lagi.',
    ];
    $lifecycleClasses = [
        'AVAILABLE' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-500/10 dark:text-emerald-400',
        'USED_IN' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900/50 dark:bg-blue-500/10 dark:text-blue-400',
        'USED_OUT' => 'border-orange-200 bg-orange-50 text-orange-700 dark:border-orange-900/50 dark:bg-orange-500/10 dark:text-orange-400',
        'RETURNED' => 'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-900/50 dark:bg-indigo-500/10 dark:text-indigo-400',
        'DISPOSAL' => 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/50 dark:bg-rose-500/10 dark:text-rose-400',
    ];
@endphp

<span
    class="lifecycle-badge inline-flex items-center justify-center rounded-lg border px-2.5 py-1 text-[10px] font-black tracking-tight {{ $lifecycleClasses[$lifecycle] ?? 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300' }}"
    title="{{ $lifecycleDescriptions[$lifecycle] ?? 'Status lifecycle barcode belum dikenali.' }}"
    aria-label="{{ $lifecycle }}: {{ $lifecycleDescriptions[$lifecycle] ?? 'Status lifecycle barcode belum dikenali.' }}"
>
    {{ $lifecycle }}
</span>
