@extends('admin')

@section('content')
<!-- ApexCharts CDN & Feather Icons -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="flex flex-col gap-5 font-nunito p-1">

    {{-- HEADER TITLE SECTION --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Engineering & Sparepart Control</h2>
            <p class="text-[11px] font-bold text-slate-500">PT SIIX EMS INDONESIA — REAL-TIME SPAREPART & INVENTORY ANALYTICS</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-blue-50 border border-blue-900/30 text-blue-950 text-[11px] font-black shadow-[2px_2px_0px_0px_#1e3a8a]">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                System Live Data
            </span>
        </div>
    </div>

    {{-- 1. TOP 3 KPI CARDS (TEMA COSTING & PRODUCTION DASHBOARD) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">

        <!-- CARD 1: TOTAL SPAREPART MASTER -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#54e3be] to-[#29cc97] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Total Master Spareparts</span>
                    <span class="text-[10px] text-white/80 block font-medium">All Registered Part Numbers</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="package" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($stats['total_part'] ?? 0) }} <span class="text-xs font-bold opacity-80">Items</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">{{ $stats['safe'] ?? 0 }} Safe Stock</span>
            </div>
        </div>

        <!-- CARD 2: TOTAL TRANSACTIONS / MOVEMENT LOGS -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#52b1ff] to-[#268fff] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Stock Movement Logs</span>
                    <span class="text-[10px] text-white/80 block font-medium">Total In & Out Transactions</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="refresh-cw" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format(($stats['tx_in_total'] ?? 0) + ($stats['tx_out_total'] ?? 0) + ($stats['tx_return_total'] ?? 0) + ($stats['tx_disposal_total'] ?? 0)) }} <span class="text-xs font-bold opacity-80">Logs</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">{{ $stats['tx_in_total'] ?? 0 }} Stock IN</span>
            </div>
        </div>

        <!-- CARD 3: CRITICAL STOCK ALERT -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#ff869a] to-[#ff6078] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Critical Stock Alert</span>
                    <span class="text-[10px] text-white/80 block font-medium">Qty Kosong / Habis</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="alert-circle" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($stats['critical'] ?? 0) }} <span class="text-xs font-bold opacity-80">Items</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">Action Required</span>
            </div>
        </div>

        {{-- CARD 4: APPROVAL ENGINEERING YANG MASIH MENUNGGU --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#8b7cff] to-[#6253e8] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Approval Engineering</span>
                    <span class="text-[10px] text-white/80 block font-medium">Pending & Checked by Staff</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="check-square" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($stats['approval_total'] ?? 0) }} <span class="text-xs font-bold opacity-80">Docs</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">Staff {{ $stats['approval_staff'] ?? 0 }} / SPV {{ $stats['approval_supervisor'] ?? 0 }}</span>
            </div>
        </div>

        {{-- CARD 5: MATERIAL RECEIVING ENGINEERING YANG MASIH TERBUKA --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#f7b955] to-[#ee8d2f] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Material Receiving</span>
                    <span class="text-[10px] text-white/80 block font-medium">Awaiting Engineering Action</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="truck" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($stats['material_receiving'] ?? 0) }} <span class="text-xs font-bold opacity-80">Docs</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-orange-900/30 border border-white/20">Pending {{ $stats['material_pending'] ?? 0 }} / Checked {{ $stats['material_checked'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- 2. MAIN CONTENT GRID (SISI KIRI GRAFIK & SISI KANAN 2 CARD) --}}
    <div class="grid grid-cols-12 gap-5">

        {{-- SISI KIRI: 2 GRAFIK PROPORSI (7 COLUMNS) --}}
        <div class="col-span-12 xl:col-span-7 flex flex-col gap-4">

            <!-- CHART 1: DAILY SPAREPART MOVEMENT ANALYTICS -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="bar-chart-2" class="w-3.5 h-3.5 text-blue-900"></i>
                            Sparepart Transaction Dynamics
                        </h3>
                    </div>
                    <select id="engTxFilterSelect" class="bg-slate-50 dark:bg-slate-800 border border-blue-900/40 text-blue-950 dark:text-white text-[10px] font-black rounded-lg px-2 py-1 focus:outline-none cursor-pointer">
                        <option value="daily" selected>Per Hari</option>
                        <option value="monthly">Per Bulan</option>
                        <option value="yearly">Per Tahun</option>
                    </select>
                </div>

                <div class="w-full">
                    <div id="engTxMovementChart"></div>
                </div>
            </div>

            <!-- CHART 2: STOCK LEVEL & HEALTH DISTRIBUTION -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="pie-chart" class="w-3.5 h-3.5 text-blue-900"></i>
                            Stock Health & Risk Status Breakdown
                        </h3>
                    </div>
                    <span class="text-[9px] font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Realtime Audit</span>
                </div>

                <div class="w-full">
                    <div id="stockHealthChart"></div>
                </div>
            </div>

        </div>

        {{-- SISI KANAN: 2 CARD (ALERT PENGINGAT & SUMMARY AUDIT CARD) (5 COLUMNS) --}}
        <div class="col-span-12 xl:col-span-5 flex flex-col gap-4">

            <!-- CARD 1 (KANAN ATAS): ALERT BOX PENGINGAT CRITICAL STOCK / PURCHASE REQUEST -->
            <div class="bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-500/80 shadow-[3px_3px_0px_0px_#d97706] p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            Restock Action Required
                        </span>
                        <span class="text-[9px] font-mono font-bold text-amber-700 dark:text-amber-400 bg-amber-200/50 px-2 py-0.5 rounded-md">
                            PurchaseRequestEngController
                        </span>
                    </div>

                    <div class="flex items-start gap-3 my-2">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white font-black shadow-sm">
                            <i data-feather="shopping-bag" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-amber-950 dark:text-amber-100 leading-tight">Pengingat PR Sparepart Critical</h4>
                            <p class="text-[11px] font-bold text-amber-800 dark:text-amber-300/90 mt-1 leading-snug">
                                Ada <span class="font-black text-rose-600 underline text-xs">{{ $stats['critical'] ?? 0 }} Sparepart Critical</span> di bawah batas minimum stok yang butuh dibuatkan Purchase Request (PR).
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-2.5 border-t border-amber-200 dark:border-amber-900/60 flex items-center justify-between">
                    <span class="text-[10px] font-bold text-amber-800 dark:text-amber-400">Status: Critical Alert</span>
                    <a href="{{ route('purchase.request.index') }}" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-xl text-xs font-black uppercase transition-all shadow-[2px_2px_0px_0px_#92400e] no-underline">
                        Buat PR Baru <i data-feather="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- CARD 2 (KANAN BAWAH): SUMMARY INVENTORY HEALTH -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4 flex flex-col justify-between flex-1">
                <div>
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="database" class="w-3.5 h-3.5 text-blue-900"></i>
                            Engineering Inventory Summary
                        </h4>
                        <span class="text-[9px] font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Live Sync</span>
                    </div>

                    {{-- RINGKASAN DATA 1: STOCK STATUS BREAKDOWN --}}
                    <div class="my-3 space-y-2">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Status Kesehatan Stok</span>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-emerald-50 dark:bg-emerald-950/30 p-2 rounded-xl border border-emerald-200 dark:border-emerald-900">
                                <span class="text-[9px] font-bold text-emerald-700 dark:text-emerald-400 block uppercase">Safe</span>
                                <span class="text-base font-black text-emerald-600">{{ $stats['safe'] ?? 0 }}</span>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-950/30 p-2 rounded-xl border border-amber-200 dark:border-amber-900">
                                <span class="text-[9px] font-bold text-amber-700 dark:text-amber-400 block uppercase">Warning</span>
                                <span class="text-base font-black text-amber-600">{{ $stats['warning'] ?? 0 }}</span>
                            </div>
                            <div class="bg-rose-50 dark:bg-rose-950/30 p-2 rounded-xl border border-rose-200 dark:border-rose-900">
                                <span class="text-[9px] font-bold text-rose-700 dark:text-rose-400 block uppercase">Critical</span>
                                <span class="text-base font-black text-rose-600">{{ $stats['critical'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN DATA 2: LOG TRANSAKSI MASUK & KELUAR --}}
                    <div class="my-3 space-y-2">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Pergerakan Stok Engineering</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-blue-50 dark:bg-blue-950/30 p-2.5 rounded-xl border border-blue-200 dark:border-blue-900">
                                <span class="text-[9px] font-bold text-blue-700 dark:text-blue-400 block uppercase">Stock IN Logs</span>
                                <span class="text-base font-black text-blue-600">{{ $stats['tx_in_total'] ?? 0 }} <span class="text-[10px] text-slate-400 font-bold">Logs</span></span>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-950/30 p-2.5 rounded-xl border border-purple-200 dark:border-purple-900">
                                <span class="text-[9px] font-bold text-purple-700 dark:text-purple-400 block uppercase">Stock OUT Logs</span>
                                <span class="text-base font-black text-purple-600">{{ $stats['tx_out_total'] ?? 0 }} <span class="text-[10px] text-slate-400 font-bold">Logs</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN DATA 3: NORMAL STOCK RATIO --}}
                    <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between items-center text-[10px] font-black text-slate-600 dark:text-slate-300">
                            <span>Rasio Stok Aman (Safe / Total)</span>
                            <span class="text-blue-900 dark:text-blue-400 font-extrabold">{{ $stats['safe'] ?? 0 }} / {{ $stats['total_part'] ?? 1 }} Items</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-1.5 overflow-hidden">
                            <div class="bg-blue-900 h-full rounded-full transition-all duration-500" style="width: {{ ($stats['total_part'] ?? 0) > 0 ? (($stats['safe'] ?? 0) / $stats['total_part']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="text-[9px] font-bold text-slate-400 text-center mt-3 pt-2 border-t dark:border-slate-800">
                    Diperbarui secara otomatis berdasarkan log database
                </div>
            </div>

        </div>

    </div>

    {{-- 3. SPAREPART STOCK MASTER TABLE --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                    <i data-feather="layers" class="w-4 h-4 text-blue-900"></i> Sparepart Stock Master Monitoring
                </h4>
                <p class="text-[10px] text-slate-400 font-bold">Monitoring level stok untuk SPV & Staff Engineering</p>
            </div>
            <a href="{{ route('list-sparepart.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-3 py-1.5 rounded-xl text-xs font-black uppercase shadow-[2px_2px_0px_0px_#1e3a8a] flex items-center gap-1.5 no-underline">
                <i data-feather="plus" class="w-3.5 h-3.5"></i> Item Baru
            </a>
        </div>
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-left text-xs font-bold">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 uppercase text-[10px] font-black border-b border-slate-100 dark:border-slate-800">
                        <th class="px-5 py-3">Part & Rack</th>
                        <th class="px-5 py-3">SAP Code</th>
                        <th class="px-5 py-3 min-w-[180px]">Stock Level</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Last Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                    @forelse($parts as $part)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-7 w-12 items-center justify-center rounded-lg bg-blue-50 dark:bg-slate-800 text-blue-900 dark:text-blue-300 font-mono text-[10px] font-black border border-blue-200 dark:border-slate-700">
                                    {{ $part->rack_position }}
                                </span>
                                <span class="font-black text-slate-800 dark:text-white uppercase text-xs">{{ $part->part_name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-mono text-indigo-600 dark:text-indigo-400 font-black">
                            {{ $part->sap_code }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $minT = $part->min_stock_threshold > 0 ? $part->min_stock_threshold : 1;
                                $percent = min(100, ($part->current_stock / ($minT * 2)) * 100);
                                $barColor = $part->status == 'Critical' ? 'bg-rose-500' : ($part->status == 'Warning' ? 'bg-amber-500' : 'bg-emerald-500');
                            @endphp
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                <div class="{{ $barColor }} h-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-[9px] font-extrabold text-slate-500">
                                <span>{{ $part->current_stock }} Pcs</span>
                                <span>Min: {{ $part->min_stock_threshold }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border
                                {{ $part->status == 'Critical' ? 'border-rose-200 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' :
                                  ($part->status == 'Warning' ? 'border-amber-200 bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' :
                                  'border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400') }}">
                                {{ $part->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center text-[11px] font-bold text-slate-500">
                            {{ $part->updated_at ? \Carbon\Carbon::parse($part->updated_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400 italic font-bold">
                            Belum ada master data sparepart tercatat di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. ENGINEERING TRANSACTION PROCESS TABLE --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                    <i data-feather="shuffle" class="w-4 h-4 text-blue-900"></i> Engineering Transaction Process
                </h4>
                <p class="text-[10px] text-slate-400 font-bold">Riwayat real-time khusus Engineering: IN, OUT, RETURN, dan DISPOSAL</p>
            </div>
            <div class="flex flex-wrap items-center gap-1.5">
                @foreach(['all' => 'ALL', 'in' => 'IN', 'out' => 'OUT', 'return' => 'RETURN', 'disposal' => 'DISPOSAL'] as $value => $label)
                    <a href="{{ url()->current() . '?tx_filter=' . $value }}"
                       class="rounded-lg border px-3 py-1.5 text-[10px] font-black tracking-wide no-underline transition {{ ($transactionFilter ?? 'all') === $value ? 'border-blue-900 bg-blue-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-blue-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[1250px] table-fixed text-center text-[11px] font-bold font-nunito">
                <thead>
                    <tr class="bg-blue-900 text-white uppercase text-[10px] tracking-wide">
                        <th class="px-3 py-3 w-[180px]">Transaction ID</th>
                        <th class="px-3 py-3 w-[130px]">Type</th>
                        <th class="px-3 py-3 w-[140px]">Barcode ID</th>
                        <th class="px-3 py-3 w-[120px]">Sparepart</th>
                        <th class="px-3 py-3 w-[100px]">Rak</th>
                        <th class="px-3 py-3 w-[80px]">Qty</th>
                        <th class="px-3 py-3 w-[160px]">Current Life Cycle</th>
                        <th class="px-3 py-3 w-[110px]">Status</th>
                        <th class="px-3 py-3 w-[230px] text-left">Remark</th>
                        <th class="px-3 py-3 w-[130px]">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-black">
                    @forelse($transactions as $transaction)
                        @php
                            $transactionType = strtoupper($transaction->tx_type ?? '-');
                            $incoming = in_array(strtolower($transaction->tx_type ?? ''), ['in', 'return'], true);
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-3 py-3 font-mono font-black whitespace-nowrap">{{ $transaction->tx_id }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-lg border px-2 py-1 text-[9px] font-black uppercase {{ $incoming ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700' }}">
                                    {{ $transactionType }}
                                </span>
                            </td>
                            <td class="px-3 py-3 font-mono whitespace-nowrap">{{ $transaction->barcode_id ?? '-' }}</td>
                            <td class="px-3 py-3 font-mono whitespace-nowrap">{{ $transaction->sparepart_code ?? '-' }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ $transaction->nama_rak ?? '-' }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ $incoming ? '+' : '-' }}{{ number_format($transaction->qty_transaction ?? 0) }} Pcs</td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @include('partials.lifecycle-badge', ['lifecycle' => $transaction->barcode_lifecycle ?? 'UNKNOWN'])
                            </td>
                            <td class="px-3 py-3 uppercase">{{ $transaction->status ?? '-' }}</td>
                            <td class="px-3 py-3 text-left max-w-[230px]">
                                @include('partials.transaction-remark', ['remark' => $transaction->remark, 'transactionType' => $transactionType])
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                {{ $transaction->created_at ? \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-8 text-center text-slate-400 italic">Belum ada transaksi Engineering.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex justify-end border-t border-slate-100 dark:border-slate-800 px-5 py-3">
            {{ $transactions->appends(['tx_filter' => $transactionFilter])->links() }}
        </div>
    </div>

</div>

{{-- APEXCHARTS SCRIPT INITIALIZATION --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // ==========================================
        // 1. DATASET GRAFIK MOVEMENT (IN & OUT)
        // ==========================================
        const engTxDataSets = {
            daily: {
                categories: @json($chartDates ?? []),
                in: @json($chartStockIn ?? []),
                out: @json($chartStockOut ?? []),
                return: @json($chartStockReturn ?? []),
                disposal: @json($chartStockDisposal ?? [])
            },
            monthly: {
                categories: @json($chartMonthlyDates ?? []),
                in: @json($chartMonthlyIn ?? []),
                out: @json($chartMonthlyOut ?? []),
                return: @json($chartMonthlyReturn ?? []),
                disposal: @json($chartMonthlyDisposal ?? [])
            },
            yearly: {
                categories: @json($chartYearlyDates ?? []),
                in: @json($chartYearlyIn ?? []),
                out: @json($chartYearlyOut ?? []),
                return: @json($chartYearlyReturn ?? []),
                disposal: @json($chartYearlyDisposal ?? [])
            }
        };

        var engTxOptions = {
            series: [
                { name: 'IN', data: engTxDataSets.daily.in },
                { name: 'OUT', data: engTxDataSets.daily.out },
                { name: 'RETURN', data: engTxDataSets.daily.return },
                { name: 'DISPOSAL', data: engTxDataSets.daily.disposal }
            ],
            chart: {
                height: 200,
                type: 'bar',
                toolbar: { show: false },
                fontFamily: 'Nunito, sans-serif'
            },
            colors: ['#3b82f6', '#10b981', '#6366f1', '#f43f5e'],
            plotOptions: {
                bar: { horizontal: false, columnWidth: '40%', borderRadius: 3 }
            },
            dataLabels: { enabled: false },
            xaxis: { categories: engTxDataSets.daily.categories, labels: { style: { fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 } } },
            legend: { position: 'top', horizontalAlign: 'left', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 }
        };

        var engTxChart = new ApexCharts(document.querySelector("#engTxMovementChart"), engTxOptions);
        engTxChart.render();

        document.getElementById('engTxFilterSelect').addEventListener('change', function(e) {
            const selected = e.target.value;
            const targetData = engTxDataSets[selected];
            engTxChart.updateOptions({
                xaxis: { categories: targetData.categories },
                series: [
                    { name: 'IN', data: targetData.in },
                    { name: 'OUT', data: targetData.out },
                    { name: 'RETURN', data: targetData.return },
                    { name: 'DISPOSAL', data: targetData.disposal }
                ]
            });
        });

        // ==========================================
        // 2. DATASET GRAFIK STOCK HEALTH (DONUT / BAR)
        // ==========================================
        var stockHealthOptions = {
            series: [{{ $stats['safe'] ?? 0 }}, {{ $stats['warning'] ?? 0 }}, {{ $stats['critical'] ?? 0 }}],
            chart: {
                height: 200,
                type: 'donut',
                fontFamily: 'Nunito, sans-serif'
            },
            labels: ['Safe Stock', 'Warning Level', 'Critical Stock'],
            colors: ['#10b981', '#f59e0b', '#f43f5e'],
            legend: { position: 'bottom', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 },
            dataLabels: { enabled: true, style: { fontSize: '9px', fontWeight: 'bold' } }
        };

        var stockHealthChart = new ApexCharts(document.querySelector("#stockHealthChart"), stockHealthOptions);
        stockHealthChart.render();
    });
</script>
@endsection
