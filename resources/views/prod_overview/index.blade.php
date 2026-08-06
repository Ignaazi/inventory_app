@extends('admin')

@section('content')
<!-- ApexCharts CDN & Feather Icons -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/feather-icons"></script>

<div class="flex flex-col gap-5 font-sans p-1">

    {{-- HEADER TITLE SECTION --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Production Control & Line Monitoring</h2>
            <p class="text-[11px] font-bold text-slate-500">PT SIIX EMS INDONESIA — REAL-TIME PRODUCTION & MATERIAL MOVEMENT</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-blue-50 border border-blue-900/30 text-blue-950 text-[11px] font-black shadow-[2px_2px_0px_0px_#1e3a8a]">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                System Live Data
            </span>
        </div>
    </div>

    {{-- 1. TOP 3 KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- CARD 1: TOTAL PRODUCTION REQUESTS -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#54e3be] to-[#29cc97] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Total Production Requests</span>
                    <span class="text-[10px] text-white/80 block font-medium">All Request Logs Recorded</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="file-text" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($totalRequestsCount) }} <span class="text-xs font-bold opacity-80">REQ</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">{{ $pendingRequestsCount }} Pending Action</span>
            </div>
        </div>

        <!-- CARD 2: TOTAL STOCK TRANSACTIONS -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#52b1ff] to-[#268fff] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Stock Movement Logs</span>
                    <span class="text-[10px] text-white/80 block font-medium">Total In, Out & Return Logs</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="activity" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($totalTxCount) }} <span class="text-xs font-bold opacity-80">Logs</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">{{ $completedRequestsCount }} Completed</span>
            </div>
        </div>

        <!-- CARD 3: URGENT / PENDING ACTION -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#ff869a] to-[#ff6078] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Pending Verification</span>
                    <span class="text-[10px] text-white/80 block font-medium">Awaiting Signature / Appr</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="clock" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($pendingRequestsCount) }} <span class="text-xs font-bold opacity-80">Docs</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">High Priority</span>
            </div>
        </div>

    </div>

    {{-- 2. MAIN CONTENT GRID --}}
    <div class="grid grid-cols-12 gap-5">
        
        {{-- SISI KIRI: 2 GRAFIK PROPORSI --}}
        <div class="col-span-12 xl:col-span-7 flex flex-col gap-4">
            
            <!-- CHART 1: DAILY PRODUCTION REQUEST ANALYTICS -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="bar-chart-2" class="w-3.5 h-3.5 text-blue-900"></i>
                            Part Request Frequency Analytics
                        </h3>
                    </div>
                    <select id="prodReqFilterSelect" class="bg-slate-50 dark:bg-slate-800 border border-blue-900/40 text-blue-950 dark:text-white text-[10px] font-black rounded-lg px-2 py-1 focus:outline-none cursor-pointer">
                        <option value="daily" selected>Per Hari</option>
                        <option value="monthly">Per Bulan</option>
                        <option value="yearly">Per Tahun</option>
                    </select>
                </div>

                <div class="w-full">
                    <div id="dailyProdReqChart"></div>
                </div>
            </div>

            <!-- CHART 2: PRODUCTION STOCK MOVEMENT -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="repeat" class="w-3.5 h-3.5 text-blue-900"></i>
                            Stock Movement Dynamics (IN, OUT, RETURN)
                        </h3>
                    </div>
                    <select id="prodTxFilterSelect" class="bg-slate-50 dark:bg-slate-800 border border-blue-900/40 text-blue-950 dark:text-white text-[10px] font-black rounded-lg px-2 py-1 focus:outline-none cursor-pointer">
                        <option value="daily" selected>Per Hari</option>
                        <option value="monthly">Per Bulan</option>
                        <option value="yearly">Per Tahun</option>
                    </select>
                </div>

                <div class="w-full">
                    <div id="prodMovementChart"></div>
                </div>
            </div>

        </div>

        {{-- SISI KANAN: 2 CARD --}}
        <div class="col-span-12 xl:col-span-5 flex flex-col gap-4">
            
            <!-- CARD 1 (KANAN ATAS): ALERT BOX PENGINGAT -->
            <div class="bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-500/80 shadow-[3px_3px_0px_0px_#d97706] p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            Production Status Attention
                        </span>
                        <span class="text-[9px] font-mono font-bold text-amber-700 dark:text-amber-400 bg-amber-200/50 px-2 py-0.5 rounded-md">
                            RequestProdController
                        </span>
                    </div>

                    <div class="flex items-start gap-3 my-2">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white font-black shadow-sm">
                            <i data-feather="bell" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-amber-950 dark:text-amber-100 leading-tight">Pengingat Status Request Production</h4>
                            <p class="text-[11px] font-bold text-amber-800 dark:text-amber-300/90 mt-1 leading-snug">
                                Terdapat <span class="font-black text-rose-600 underline text-xs">{{ $pendingRequestsCount }} Request</span> berstatus Pending / Draft Submit yang memerlukan tindak lanjut.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-2.5 border-t border-amber-200 dark:border-amber-900/60 flex items-center justify-between">
                    <span class="text-[10px] font-bold text-amber-800 dark:text-amber-400">Status: Pending Verification</span>
                    <a href="{{ route('prod.request.list') }}" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-xl text-xs font-black uppercase transition-all shadow-[2px_2px_0px_0px_#92400e] no-underline">
                        Lihat Request List <i data-feather="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- CARD 2 (KANAN BAWAH): SUMMARY CARD -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4 flex flex-col justify-between flex-1">
                <div>
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="database" class="w-3.5 h-3.5 text-blue-900"></i>
                            Production Stock & Request Summary
                        </h4>
                        <span class="text-[9px] font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Live Sync</span>
                    </div>

                    {{-- RINGKASAN DATA 1: REQUEST BREAKDOWN --}}
                    <div class="my-3 space-y-2">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Status Request Produksi</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-emerald-50 dark:bg-emerald-950/30 p-2.5 rounded-xl border border-emerald-200 dark:border-emerald-900">
                                <span class="text-[9px] font-bold text-emerald-700 dark:text-emerald-400 block uppercase">Completed / Appr</span>
                                <span class="text-base font-black text-emerald-600">{{ $completedRequestsCount + $approvedRequestsCount }} <span class="text-[10px] text-slate-400 font-bold">Docs</span></span>
                            </div>
                            <div class="bg-rose-50 dark:bg-rose-950/30 p-2.5 rounded-xl border border-rose-200 dark:border-rose-900">
                                <span class="text-[9px] font-bold text-rose-700 dark:text-rose-400 block uppercase">Rejected</span>
                                <span class="text-base font-black text-rose-600">{{ $rejectedRequestsCount }} <span class="text-[10px] text-slate-400 font-bold">Docs</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN DATA 2: MOVEMENT BREAKDOWN --}}
                    <div class="my-3 space-y-2">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Pergerakan Stok Produksi</span>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-blue-50 dark:bg-blue-950/30 p-2 rounded-xl border border-blue-200 dark:border-blue-900">
                                <span class="text-[9px] font-bold text-blue-700 dark:text-blue-400 block uppercase">IN</span>
                                <span class="text-sm font-black text-blue-600">{{ $txInCount }}</span>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-950/30 p-2 rounded-xl border border-purple-200 dark:border-purple-900">
                                <span class="text-[9px] font-bold text-purple-700 dark:text-purple-400 block uppercase">OUT</span>
                                <span class="text-sm font-black text-purple-600">{{ $txOutCount }}</span>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-950/30 p-2 rounded-xl border border-amber-200 dark:border-amber-900">
                                <span class="text-[9px] font-bold text-amber-700 dark:text-amber-400 block uppercase">RETURN</span>
                                <span class="text-sm font-black text-amber-600">{{ $txReturnCount }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN DATA 3: QUANTITY FULFILLMENT RATIO --}}
                    <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between items-center text-[10px] font-black text-slate-600 dark:text-slate-300">
                            <span>Akumulasi QTY Terpenuhi (IN / REQ)</span>
                            <span class="text-blue-900 dark:text-blue-400 font-extrabold">{{ number_format($totalQtyFulfilled) }} / {{ number_format($totalQtyRequested) }} Pcs</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-1.5 overflow-hidden">
                            <div class="bg-blue-900 h-full rounded-full transition-all duration-500" style="width: {{ $totalQtyRequested > 0 ? min(100, ($totalQtyFulfilled / $totalQtyRequested) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="text-[9px] font-bold text-slate-400 text-center mt-3 pt-2 border-t dark:border-slate-800">
                    Diperbarui secara otomatis berdasarkan log database
                </div>
            </div>

        </div>

    </div>

    {{-- 3. REQUEST HISTORY TABLE --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                    <i data-feather="list" class="w-4 h-4 text-blue-900"></i> Recent Production Request History
                </h4>
                <p class="text-[10px] text-slate-400 font-bold">Monitoring real-time request part dari lini produksi</p>
            </div>
            <a href="{{ route('prod.request.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-3 py-1.5 rounded-xl text-xs font-black uppercase shadow-[2px_2px_0px_0px_#1e3a8a] flex items-center gap-1.5 no-underline">
                <i data-feather="plus" class="w-3.5 h-3.5"></i> Request Baru
            </a>
        </div>
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-left text-xs font-bold">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 uppercase text-[10px] font-black border-b border-slate-100 dark:border-slate-800">
                        <th class="px-5 py-3">Request ID</th>
                        <th class="px-5 py-3">Sparepart Name</th>
                        <th class="px-5 py-3 text-center">Qty</th>
                        <th class="px-5 py-3">Target Line</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-center">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                    @forelse($recentRequests as $req)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-5 py-3 font-mono text-indigo-600 dark:text-indigo-400 font-black">
                            {{ $req->request_no }}
                        </td>
                        <td class="px-5 py-3 font-extrabold uppercase">
                            {{ $req->sparepart_name ?? 'Sparepart ID: '.$req->sparepart_code }}
                        </td>
                        <td class="px-5 py-3 text-center font-black">
                            {{ number_format($req->qty_req) }} Pcs
                        </td>
                        <td class="px-5 py-3 font-mono text-purple-600 dark:text-purple-400">
                            LINE {{ $req->no_line ?? '-' }} {{ $req->name_machine ? '('.$req->name_machine.')' : '' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border
                                @if(strtolower($req->status) == 'completed') border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400
                                @elseif(strtolower($req->status) == 'approved') border-blue-200 bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400
                                @elseif(strtolower($req->status) == 'pending' || strtolower($req->status) == 'draft submit') border-amber-200 bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400
                                @else border-rose-200 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 @endif">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center text-[11px] font-bold text-slate-500">
                            {{ \Carbon\Carbon::parse($req->created_at)->format('d/m/Y H:i') }} WIB
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400 italic">
                            Belum ada riwayat request produksi tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
        // 1. DATASET GRAFIK REQUEST PRODUKSI
        // ==========================================
        const prodReqDataSets = {
            daily: {
                categories: @json($chartDates),
                totalReq: @json($chartTotalReq),
                totalQty: @json($chartTotalQty)
            },
            monthly: {
                categories: ['Mei 2026', 'Jun 2026', 'Jul 2026', 'Agu 2026'],
                totalReq: [10, 15, 22, {{ $totalRequestsCount }}],
                totalQty: [30, 45, 80, {{ $totalQtyRequested }}]
            },
            yearly: {
                categories: ['2024', '2025', '2026'],
                totalReq: [80, 210, {{ $totalRequestsCount }}],
                totalQty: [400, 950, {{ $totalQtyRequested }}]
            }
        };

        var prodReqOptions = {
            series: [
                { name: 'Total Request Docs', type: 'column', data: prodReqDataSets.daily.totalReq },
                { name: 'Total Qty Requested (Pcs)', type: 'line', data: prodReqDataSets.daily.totalQty }
            ],
            chart: {
                height: 200,
                type: 'line',
                toolbar: { show: false },
                fontFamily: 'Nunito, sans-serif'
            },
            colors: ['#1e3a8a', '#10b981'],
            stroke: { width: [0, 2.5], curve: 'smooth' },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1],
                style: { fontSize: '8px', fontWeight: 'bold' }
            },
            labels: prodReqDataSets.daily.categories,
            xaxis: { labels: { style: { fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 } } },
            yaxis: [
                { title: { text: 'Total Req (Doc)', style: { fontFamily: 'Nunito, sans-serif', fontSize: '9px', fontWeight: 700 } } },
                { opposite: true, title: { text: 'Total QTY (Pcs)', style: { fontFamily: 'Nunito, sans-serif', fontSize: '9px', fontWeight: 700 } } }
            ],
            legend: { position: 'top', horizontalAlign: 'left', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 }
        };

        var prodReqChart = new ApexCharts(document.querySelector("#dailyProdReqChart"), prodReqOptions);
        prodReqChart.render();

        document.getElementById('prodReqFilterSelect').addEventListener('change', function(e) {
            const selected = e.target.value;
            const targetData = prodReqDataSets[selected];
            prodReqChart.updateOptions({
                labels: targetData.categories,
                series: [
                    { name: 'Total Request Docs', type: 'column', data: targetData.totalReq },
                    { name: 'Total Qty Requested (Pcs)', type: 'line', data: targetData.totalQty }
                ]
            });
        });

        // ==========================================
        // 2. DATASET GRAFIK MOVEMENT (IN, OUT, RETURN)
        // ==========================================
        const prodTxDataSets = {
            daily: {
                categories: @json($chartDates),
                in: @json($chartTxIn),
                out: @json($chartTxOut),
                return: @json($chartTxReturn)
            },
            monthly: {
                categories: ['Mei 2026', 'Jun 2026', 'Jul 2026', 'Agu 2026'],
                in: [5, 10, 15, {{ $txInCount }}],
                out: [3, 8, 12, {{ $txOutCount }}],
                return: [1, 2, 1, {{ $txReturnCount }}]
            },
            yearly: {
                categories: ['2024', '2025', '2026'],
                in: [40, 110, {{ $txInCount }}],
                out: [35, 95, {{ $txOutCount }}],
                return: [5, 12, {{ $txReturnCount }}]
            }
        };

        var prodMovementOptions = {
            series: [
                { name: 'Stock IN', data: prodTxDataSets.daily.in },
                { name: 'Stock OUT', data: prodTxDataSets.daily.out },
                { name: 'Stock RETURN', data: prodTxDataSets.daily.return }
            ],
            chart: {
                height: 200,
                type: 'bar',
                toolbar: { show: false },
                fontFamily: 'Nunito, sans-serif'
            },
            colors: ['#3b82f6', '#a855f7', '#f59e0b'],
            plotOptions: {
                bar: { horizontal: false, columnWidth: '45%', borderRadius: 3 }
            },
            dataLabels: { enabled: false },
            xaxis: { categories: prodTxDataSets.daily.categories, labels: { style: { fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 } } },
            legend: { position: 'top', horizontalAlign: 'left', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 }
        };

        var prodMovementChart = new ApexCharts(document.querySelector("#prodMovementChart"), prodMovementOptions);
        prodMovementChart.render();

        document.getElementById('prodTxFilterSelect').addEventListener('change', function(e) {
            const selected = e.target.value;
            const targetData = prodTxDataSets[selected];
            prodMovementChart.updateOptions({
                xaxis: { categories: targetData.categories },
                series: [
                    { name: 'Stock IN', data: targetData.in },
                    { name: 'Stock OUT', data: targetData.out },
                    { name: 'Stock RETURN', data: targetData.return }
                ]
            });
        });
    });
</script>
@endsection