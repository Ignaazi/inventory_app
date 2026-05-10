@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="-m-4 md:-m-6 2xl:-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-[calc(100vh-80px)]">
    
    <div class="p-4 md:p-8 2xl:p-10">
        
        {{-- Header Section --}}
        <div class="mb-6 px-2 text-slate-900 dark:text-white">
            <h1 class="text-2xl font-black tracking-tight uppercase">Spareparts</h1>
            <nav class="flex text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mt-1">
                <span>Engineering</span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-indigo-600">Master Data Sparepart</span>
            </nav>
        </div>

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden">
            
            {{-- Action Header --}}
            <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-b border-slate-100 dark:border-strokedark">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase">Spareparts List</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mt-1 italic">Manage and monitor engineering sparepart stock.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="flex items-center justify-center gap-2 px-4 py-2 border border-slate-200 dark:border-strokedark rounded-xl text-[10px] font-black text-slate-900 dark:text-white hover:bg-slate-50 transition-all uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Export
                    </button>
                    <button onclick="openAddModal()" class="flex items-center justify-center gap-2 px-5 py-2 bg-indigo-600 rounded-xl text-[10px] font-black text-white hover:bg-indigo-700 shadow-md transition-all uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Add Sparepart
                    </button>
                </div>
            </div>

            {{-- Search Section --}}
            <div class="p-6">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <form action="{{ url()->current() }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="SEARCH SPAREPART..." class="w-full pl-11 pr-4 py-3 border border-slate-200 dark:border-strokedark rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all dark:bg-meta-4 text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">
                    </form>
                </div>
            </div>

            {{-- Table Content --}}
            <div class="max-w-full overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed min-w-[1100px]">
                    <thead>
                        <tr class="border-y border-slate-100 dark:border-strokedark bg-slate-50/50 dark:bg-meta-4/20 font-black text-slate-900 dark:text-slate-300 uppercase tracking-widest text-[10px]">
                            <th class="px-6 py-4 w-16">No.</th>
                            <th class="px-2 py-4 w-12">Sparepart Name</th>
                            <th class="px-2 py-4 text-center w-36">Image</th>
                            <th class="px-6 py-4 text-center w-36">Category</th>
                            <th class="px-6 py-4 text-center w-24">Qty</th>
                            <th class="px-6 py-4 text-center w-44">Create At</th>
                            <th class="px-6 py-4 text-center w-40">Update At</th>
                            <th class="px-6 py-4 text-center w-32">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-strokedark">
                        @forelse($spareparts as $index => $item)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all font-black uppercase text-slate-900 dark:text-white text-[11px]">
                            <td class="px-6 py-6">{{ $spareparts->firstItem() + $index }}</td>
                            <td class="px-2 py-6">
                                <div class="truncate tracking-tight" title="{{ $item->name }}">
                                    {{ $item->name }}
                                </div>
                            </td>
                            <td class="px-2 py-6">
                                <div class="flex justify-center">
                                    <div class="w-15 h-12 rounded-xl bg-slate-50 dark:bg-meta-4 flex items-center justify-center overflow-hidden border border-slate-200 shadow-sm">
                                        @if($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[8px] text-slate-300">NO PIC</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div>  {{ $item->category }}</div>
                            </td>
                            <td class="px-6 py-6 text-center text-sm font-black">{{ $item->qty }}</td>
                            <td class="px-6 py-6 text-center">
                                <div>{{ $item->created_at->format('d M Y') }}</div>
                                <div class="text-[9px] text-slate-400 mt-1">{{ $item->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div>{{ $item->updated_at->format('d M Y') }}</div>
                                <div class="text-[9px] text-slate-400 mt-1">{{ $item->updated_at->format('H:i') }}</div>
                            </td>
                            <td class="px-9 py-8">
                                <div class="flex justify-end gap-2 items-center">
                                    {{-- Edit Kuning --}}
                                    <button onclick="openEditModal({{ $item }})" class="p-2 bg-amber-400 hover:bg-amber-500 text-white rounded-xl shadow-sm transition-all transform hover:scale-110">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.5 2.5 0 113.536 3.536L12 17.207l-4 1 1-4 9.586-9.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    {{-- Delete Merah --}}
                                    <form action="{{ route('list-sparepart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete Component?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl shadow-sm transition-all transform hover:scale-110">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-20 text-slate-400 font-black tracking-widest">Data Sparepart Kosong</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer / Pagination --}}
            <div class="p-6 border-t border-slate-100 dark:border-strokedark bg-slate-50/20 flex flex-col md:flex-row justify-between items-center gap-4 font-black uppercase text-[10px]">
                <p class="text-slate-500 tracking-widest italic">Showing <span class="text-indigo-600">{{ $spareparts->firstItem() }} to {{ $spareparts->lastItem() }}</span> of {{ $spareparts->total() }} Units</p>
                <div>{{ $spareparts->links() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL (Style sama dengan table) --}}
<div id="modal-sparepart" class="fixed inset-0 z-[999] hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-boxdark w-full max-w-lg rounded-[2rem] overflow-hidden shadow-2xl border border-slate-200 dark:border-strokedark">
        <div class="px-10 py-8 bg-slate-50 dark:bg-meta-4/30 border-b border-slate-100 dark:border-strokedark flex justify-between items-center">
            <div>
                <h3 id="modal-title" class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Sparepart Form</h3>
                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em] mt-1 italic">Engineering Database</p>
            </div>
            <button onclick="closeModal()" class="text-slate-400 hover:text-rose-500 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>

        <form id="sparepart-form" action="" method="POST" enctype="multipart/form-data" class="p-10">
            @csrf
            <div id="method-field"></div>
            <div class="grid grid-cols-1 gap-6">
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Sparepart Name</label>
                    <input type="text" name="name" id="field-name" class="w-full px-6 py-4 border-2 border-slate-100 rounded-2xl dark:bg-meta-4 text-xs font-black uppercase focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all dark:text-white" required>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Category</label>
                        <select name="category" id="field-category" class="w-full px-6 py-4 border-2 border-slate-100 rounded-2xl dark:bg-meta-4 text-xs font-black uppercase outline-none focus:border-indigo-500 transition-all dark:text-white" required>
                            <option value="NOZZLE">NOZZLE</option>
                            <option value="FEEDER">FEEDER</option>
                            <option value="MOTOR">MOTOR</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Quantity</label>
                        <input type="number" name="qty" id="field-qty" class="w-full px-6 py-4 border-2 border-slate-100 rounded-2xl dark:bg-meta-4 text-xs font-black uppercase outline-none focus:border-indigo-500 transition-all dark:text-white" required>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Photo Upload</label>
                    <input type="file" name="image" class="w-full px-6 py-4 border-2 border-slate-100 border-dashed rounded-2xl text-[10px] font-black text-slate-400 uppercase">
                </div>
            </div>
            <div class="mt-12 flex gap-4">
                <button type="button" onclick="closeModal()" class="flex-1 px-8 py-4 border-2 border-slate-100 rounded-2xl text-[11px] font-black uppercase text-slate-400 hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" id="btn-submit" class="flex-[2] px-8 py-4 bg-indigo-600 text-white rounded-2xl text-[11px] font-black uppercase shadow-xl hover:bg-indigo-700 transition-all">Save Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('modal-sparepart');
    const form = document.getElementById('sparepart-form');
    const title = document.getElementById('modal-title');
    const methodField = document.getElementById('method-field');
    const btnSubmit = document.getElementById('btn-submit');

    function openAddModal() {
        title.innerText = 'Add New Sparepart';
        form.action = "{{ route('list-sparepart.store') }}";
        methodField.innerHTML = '';
        form.reset();
        btnSubmit.innerText = 'Save Sparepart';
        modal.classList.remove('hidden');
    }

    function openEditModal(item) {
        title.innerText = 'Edit Sparepart';
        form.action = `/eng/list-sparepart/${item.id}`;
        methodField.innerHTML = '@method("PUT")';
        document.getElementById('field-name').value = item.name;
        document.getElementById('field-category').value = item.category;
        document.getElementById('field-qty').value = item.qty;
        btnSubmit.innerText = 'Update Changes';
        modal.classList.remove('hidden');
    }

    function closeModal() { modal.classList.add('hidden'); }

    @if(session('success'))
        Swal.fire({
            title: 'SUCCESS!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4f46e5', // Warna indigo sesuai tema lu
            borderRadius: '2rem',
            customClass: {
                popup: 'rounded-[2rem] border-2 border-slate-100 uppercase font-black',
                title: 'text-slate-900 tracking-tighter',
                confirmButton: 'rounded-xl px-10 py-3 text-[10px] tracking-widest'
            }
        });
    @endif

    // Popup untuk Error Message
    @if(session('error'))
        Swal.fire({
            title: 'ERROR!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'TRY AGAIN',
            confirmButtonColor: '#f43f5e',
            customClass: {
                popup: 'rounded-[2rem] border-2 border-slate-100 uppercase font-black',
                confirmButton: 'rounded-xl px-10 py-3 text-[10px] tracking-widest'
            }
        });
    @endif

    // Intercept Form Delete agar pakai Popup Cantik
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = function(e) {
            e.preventDefault(); // Stop confirm bawaan browser
            
            Swal.fire({
                title: 'ARE YOU SURE?',
                text: "THIS ACTION WILL PERMANENTLY DELETE THE COMPONENT!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e', // Rose/Merah
                cancelButtonColor: '#64748b', // Slate
                confirmButtonText: 'YES, DELETE IT!',
                cancelButtonText: 'CANCEL',
                customClass: {
                    popup: 'rounded-[2rem] border-2 border-slate-100 uppercase font-black',
                    confirmButton: 'rounded-xl px-6 py-3 text-[10px] tracking-widest',
                    cancelButton: 'rounded-xl px-6 py-3 text-[10px] tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit(); // Jalankan hapus jika klik Yes
                }
            });
        };
    });

</script>


@endsection