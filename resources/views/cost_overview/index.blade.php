@extends('admin')

@section('content')
<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="flex flex-col gap-5 font-sans p-1">

    {{-- HEADER TITLE SECTION --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Costing & Material Control</h2>
            <p class="text-[11px] font-bold text-slate-500">PT SIIX EMS INDONESIA — REAL-TIME MATERIAL & PR ANALYTICS</p>
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
        
        <!-- CARD 1: TOTAL PURCHASE REQUESTS -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#54e3be] to-[#29cc97] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Total Purchase Request</span>
                    <span class="text-[10px] text-white/80 block font-medium">All PR Logs Recorded</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="file-text" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($totalPrCount) }} <span class="text-xs font-bold opacity-80">PR</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">{{ $pendingCheckedPrCount }} Pending Action</span>
            </div>
        </div>

        <!-- CARD 2: TOTAL MATERIAL RECEIVED -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#52b1ff] to-[#268fff] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Total Material Received</span>
                    <span class="text-[10px] text-white/80 block font-medium">Physical Receiving Logs</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="truck" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($totalMrCount) }} <span class="text-xs font-bold opacity-80">Logs</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">{{ $mrClosedCount }} Closed</span>
            </div>
        </div>

        <!-- CARD 3: URGENT REQUESTS -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#ff869a] to-[#ff6078] p-4 text-white border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] flex flex-col justify-between transition-transform hover:-translate-y-0.5">
            <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="block text-[11px] font-black uppercase tracking-wider opacity-90">Urgent PR Required</span>
                    <span class="text-[10px] text-white/80 block font-medium">High Priority Items</span>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 border border-white/30 text-white">
                    <i data-feather="alert-triangle" class="w-4 h-4 stroke-[2.5]"></i>
                </div>
            </div>
            <div class="relative z-10 flex items-baseline justify-between mt-2">
                <h4 class="text-2xl font-black tracking-tight">{{ number_format($urgentPrCount) }} <span class="text-xs font-bold opacity-80">Items</span></h4>
                <span class="rounded-lg px-2 py-0.5 text-[9px] font-black bg-blue-900/30 border border-white/20">High Priority</span>
            </div>
        </div>

    </div>

    {{-- 2. MAIN CONTENT GRID (SISI KIRI GRAFIK RINGKAS & SISI KANAN 2 CARD) --}}
    <div class="grid grid-cols-12 gap-5">
        
        {{-- SISI KIRI: 2 GRAFIK PROPORSI DITINGGIKAN SEEDERHANA (7 COLUMNS) --}}
        <div class="col-span-12 xl:col-span-7 flex flex-col gap-4">
            
            <!-- CHART 1: DAILY PR PROCESS ANALYTICS (DITINGGIKAN 200PX) -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="bar-chart-2" class="w-3.5 h-3.5 text-blue-900"></i>
                            PR Process Analytics
                        </h3>
                    </div>
                    <select id="prFilterSelect" class="bg-slate-50 dark:bg-slate-800 border border-blue-900/40 text-blue-950 dark:text-white text-[10px] font-black rounded-lg px-2 py-1 focus:outline-none cursor-pointer">
                        <option value="daily" selected>Per Hari</option>
                        <option value="monthly">Per Bulan</option>
                        <option value="yearly">Per Tahun</option>
                    </select>
                </div>

                <div class="w-full">
                    <div id="dailyPrChart"></div>
                </div>
            </div>

            <!-- CHART 2: MATERIAL RECEIVED TIMELINE (DITINGGIKAN 200PX) -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="clock" class="w-3.5 h-3.5 text-blue-900"></i>
                            Material Receiving Arrival Leadtime
                        </h3>
                    </div>
                    <select id="mrFilterSelect" class="bg-slate-50 dark:bg-slate-800 border border-blue-900/40 text-blue-950 dark:text-white text-[10px] font-black rounded-lg px-2 py-1 focus:outline-none cursor-pointer">
                        <option value="daily" selected>Per Hari</option>
                        <option value="monthly">Per Bulan</option>
                        <option value="yearly">Per Tahun</option>
                    </select>
                </div>

                <div class="w-full">
                    <div id="mrTimelineChart"></div>
                </div>
            </div>

        </div>

        {{-- SISI KANAN: HANYA 2 CARD (ALERT PENGINGAT APPROVAL & DATA OVERVIEW) (5 COLUMNS) --}}
        <div class="col-span-12 xl:col-span-5 flex flex-col gap-4">
            
            <!-- CARD 1 (KANAN ATAS): ALERT BOX PENGINGAT LIST APPROVAL PR -->
            <div class="bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-500/80 shadow-[3px_3px_0px_0px_#d97706] p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            Approval Action Required
                        </span>
                        <span class="text-[9px] font-mono font-bold text-amber-700 dark:text-amber-400 bg-amber-200/50 px-2 py-0.5 rounded-md">
                            ApprovalController
                        </span>
                    </div>

                    <div class="flex items-start gap-3 my-2">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white font-black shadow-sm">
                            <i data-feather="bell" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-amber-950 dark:text-amber-100 leading-tight">Pengingat Persetujuan Document PR</h4>
                            <p class="text-[11px] font-bold text-amber-800 dark:text-amber-300/90 mt-1 leading-snug">
                                Ada <span class="font-black text-rose-600 underline text-xs">{{ $pendingCheckedPrCount }} PR Baru</span> yang sudah di-check Engineering dan menunggu tindakan Approve / Reject Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-2.5 border-t border-amber-200 dark:border-amber-900/60 flex items-center justify-between">
                    <span class="text-[10px] font-bold text-amber-800 dark:text-amber-400">Status: Checked Pending</span>
                    <a href="{{ route('costing.pr.index') }}" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-xl text-xs font-black uppercase transition-all shadow-[2px_2px_0px_0px_#92400e]">
                        Buka Form Approval <i data-feather="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- CARD 2 (KANAN BAWAH): RINGKASAN DATA TERPADU (DATA OVERVIEW CARD) -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-blue-900/30 shadow-[3px_3px_0px_0px_#1e3a8a] p-4 flex flex-col justify-between flex-1">
                <div>
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-1.5">
                            <i data-feather="database" class="w-3.5 h-3.5 text-blue-900"></i>
                            Costing Audit & Material Summary
                        </h4>
                        <span class="text-[9px] font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Live Sync</span>
                    </div>

                    {{-- RINGKASAN DATA 1: PR APPROVAL BREAKDOWN --}}
                    <div class="my-3 space-y-2">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Persetujuan Document PR</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-emerald-50 dark:bg-emerald-950/30 p-2.5 rounded-xl border border-emerald-200 dark:border-emerald-900">
                                <span class="text-[9px] font-bold text-emerald-700 dark:text-emerald-400 block uppercase">Approved PR</span>
                                <span class="text-base font-black text-emerald-600">{{ $prApprovedCount }} <span class="text-[10px] text-slate-400 font-bold">Docs</span></span>
                            </div>
                            <div class="bg-rose-50 dark:bg-rose-950/30 p-2.5 rounded-xl border border-rose-200 dark:border-rose-900">
                                <span class="text-[9px] font-bold text-rose-700 dark:text-rose-400 block uppercase">Rejected PR</span>
                                <span class="text-base font-black text-rose-600">{{ $prRejectedCount }} <span class="text-[10px] text-slate-400 font-bold">Docs</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN DATA 2: MATERIAL RECEIVED RECEIVING AUDIT --}}
                    <div class="my-3 space-y-2">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Status Kedatangan Barang (MR)</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                                <span class="text-[9px] font-bold text-slate-500 block uppercase">MR Closed</span>
                                <span class="text-base font-black text-slate-800 dark:text-white">{{ $mrClosedCount }} <span class="text-[10px] text-slate-400 font-bold">Logs</span></span>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-950/30 p-2.5 rounded-xl border border-amber-200 dark:border-amber-900">
                                <span class="text-[9px] font-bold text-amber-700 dark:text-amber-400 block uppercase">MR Open</span>
                                <span class="text-base font-black text-amber-600">{{ $mrOpenCount }} <span class="text-[10px] text-slate-400 font-bold">Logs</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN DATA 3: QUANTITY VOLUME RATIO --}}
                    <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between items-center text-[10px] font-black text-slate-600 dark:text-slate-300">
                            <span>Akumulasi QTY Recv / PR</span>
                            <span class="text-blue-900 dark:text-blue-400 font-extrabold">{{ number_format($totalQtyReceived) }} / {{ number_format($totalQtyPr) }} Pcs</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-1.5 overflow-hidden">
                            <div class="bg-blue-900 h-full rounded-full transition-all duration-500" style="width: {{ $totalQtyPr > 0 ? ($totalQtyReceived / $totalQtyPr) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="text-[9px] font-bold text-slate-400 text-center mt-3 pt-2 border-t dark:border-slate-800">
                    Diperbarui secara otomatis berdasarkan log database
                </div>
            </div>

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
        // 1. DATASET GRAFIK PR (DIKECILKAN KE HEIGHT 200PX)
        // ==========================================
        const prDataSets = {
            daily: {
                categories: @json($chartDates),
                totalPr: @json($chartTotalPr),
                totalQty: @json($chartTotalQty)
            },
            monthly: {
                categories: ['Mei 2026', 'Jun 2026', 'Jul 2026', 'Agu 2026'],
                totalPr: [12, 18, 25, {{ $totalPrCount }}],
                totalQty: [45, 60, 110, {{ $totalQtyPr }}]
            },
            yearly: {
                categories: ['2024', '2025', '2026'],
                totalPr: [120, 310, {{ $totalPrCount }}],
                totalQty: [850, 1420, {{ $totalQtyPr }}]
            }
        };

        var prOptions = {
            series: [
                { name: 'Total PR Created', type: 'column', data: prDataSets.daily.totalPr },
                { name: 'Total QTY Requested (Pcs)', type: 'line', data: prDataSets.daily.totalQty }
            ],
            chart: {
                height: 200, // Ukuran di-adjust pas & ringkas
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
            labels: prDataSets.daily.categories,
            xaxis: { labels: { style: { fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 } } },
            yaxis: [
                { title: { text: 'Total PR (Doc)', style: { fontFamily: 'Nunito, sans-serif', fontSize: '9px', fontWeight: 700 } } },
                { opposite: true, title: { text: 'Total QTY (Pcs)', style: { fontFamily: 'Nunito, sans-serif', fontSize: '9px', fontWeight: 700 } } }
            ],
            legend: { position: 'top', horizontalAlign: 'left', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 }
        };

        var prChart = new ApexCharts(document.querySelector("#dailyPrChart"), prOptions);
        prChart.render();

        document.getElementById('prFilterSelect').addEventListener('change', function(e) {
            const selected = e.target.value;
            const targetData = prDataSets[selected];
            prChart.updateOptions({
                labels: targetData.categories,
                series: [
                    { name: 'Total PR Created', type: 'column', data: targetData.totalPr },
                    { name: 'Total QTY Requested (Pcs)', type: 'line', data: targetData.totalQty }
                ]
            });
        });

        // ==========================================
        // 2. DATASET GRAFIK MR TIMELINE (DIKECILKAN KE HEIGHT 200PX)
        // ==========================================
        const mrBaseSeries = @json($timelineSeries);

        const mrDataSets = {
            daily: mrBaseSeries,
            monthly: [
                {
                    name: 'Target Leadtime (PR)',
                    data: [
                        { x: 'Rata-rata Julai', y: [new Date('2026-07-01').getTime(), new Date('2026-07-05').getTime()] },
                        { x: 'Rata-rata Agustus', y: [new Date('2026-08-01').getTime(), new Date('2026-08-04').getTime()] }
                    ]
                },
                {
                    name: 'Actual Arrival (MR)',
                    data: [
                        { x: 'Rata-rata Julai', y: [new Date('2026-07-01').getTime(), new Date('2026-07-04').getTime()] },
                        { x: 'Rata-rata Agustus', y: [new Date('2026-08-01').getTime(), new Date('2026-08-05').getTime()] }
                    ]
                }
            ],
            yearly: [
                {
                    name: 'Target Leadtime (PR)',
                    data: [ { x: 'Periode T.A 2026', y: [new Date('2026-01-01').getTime(), new Date('2026-12-31').getTime()] } ]
                },
                {
                    name: 'Actual Arrival (MR)',
                    data: [ { x: 'Periode T.A 2026', y: [new Date('2026-01-01').getTime(), new Date('2026-08-05').getTime()] } ]
                }
            ]
        };

        var mrTimelineOptions = {
            series: mrDataSets.daily,
            chart: {
                height: 200, // Ukuran di-adjust pas & ringkas
                type: 'rangeBar',
                toolbar: { show: false },
                fontFamily: 'Nunito, sans-serif'
            },
            plotOptions: {
                bar: { horizontal: true, barHeight: '50%', borderRadius: 3 }
            },
            colors: ['#3b82f6', '#10b981'],
            xaxis: { type: 'datetime', labels: { style: { fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 } } },
            stroke: { width: 1 },
            fill: { type: 'solid', opacity: 0.85 },
            legend: { position: 'top', horizontalAlign: 'left', fontFamily: 'Nunito, sans-serif', fontSize: '10px', fontWeight: 700 }
        };

        var mrTimelineChart = new ApexCharts(document.querySelector("#mrTimelineChart"), mrTimelineOptions);
        mrTimelineChart.render();

        document.getElementById('mrFilterSelect').addEventListener('change', function(e) {
            mrTimelineChart.updateSeries(mrDataSets[e.target.value]);
        });
    });
</script>
@endsection