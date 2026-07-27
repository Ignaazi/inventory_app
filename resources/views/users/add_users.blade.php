@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 md:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
        
        <!-- HEADER FORM -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-base md:text-lg font-bold text-slate-950 dark:text-white">Add New User Account</h2>
                <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">Create a new user profile, access credentials, and attach digital signatures.</p>
            </div>
            
            <a href="{{ route('users.index') }}" 
               class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90">
                <i class="fas fa-arrow-left text-[10px]"></i> Kembali
            </a>
        </div>

        <!-- NOTIFIKASI ERROR VALIDASI -->
        @if ($errors->any())
            <div class="mb-5 p-4 text-xs md:text-sm text-red-800 rounded-lg bg-red-50 font-bold border border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM TAMBAH -->
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-5">
                
                <!-- INPUT NAMA -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Enter full name" 
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- INPUT NIK -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">NIK (Nomor Induk Karyawan)</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" required placeholder="Enter NIK" 
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- DROPDOWN ROLE -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Select Role / Department</label>
                    <div class="relative">
                        <select name="role" required class="w-full bg-white dark:bg-slate-950 border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Choose Role --</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Full Access)</option>
                            <option value="engineering" {{ old('role') == 'engineering' ? 'selected' : '' }}>Engineering</option>
                            <option value="production" {{ old('role') == 'production' ? 'selected' : '' }}>Production</option>
                            <option value="costing" {{ old('role') == 'costing' ? 'selected' : '' }}>Costing</option>
                        </select>
                    </div>
                </div>

                <!-- INPUT PASSWORD -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Password</label>
                    <input type="password" name="password" required placeholder="Enter password (min. 6 chars)" 
                           class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-3.5 py-2 text-xs md:text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- COMPONENT PROFILE PHOTO (RESPONSIVE) -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300 flex items-center gap-1.5">
                        <i class="fas fa-user-circle text-blue-500"></i> Profile Photo
                    </label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full">
                        <!-- Preview Box Image -->
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-gray-200 dark:border-gray-700 flex-shrink-0 flex items-center justify-center shadow-sm">
                            <img id="profile-preview" 
                                 src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
                                 alt="Profile Preview" class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Input File Custom Outline -->
                        <div class="w-full flex-1 flex flex-col gap-1.5">
                            <input type="file" name="image" id="profile-input" accept="image/*" required
                                   class="block w-full text-[11px] text-slate-500 bg-white dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer focus:outline-none
                                          file:mr-3 file:py-2 file:px-3 file:rounded-l-md file:border-0 file:text-[11px] file:font-bold 
                                          file:bg-slate-100 file:text-slate-800 dark:file:bg-slate-800 dark:file:text-white 
                                          file:shadow-[inset_0_1px_0_rgba(255,255,255,0.2)]
                                          file:border-r file:border-gray-300 dark:file:border-gray-700
                                          hover:file:bg-slate-200 dark:hover:file:bg-slate-700 transition-all" />
                            
                            <!-- Tombol Remove Photo -->
                            <button type="button" id="btn-remove-profile" class="hidden text-left text-[11px] font-bold text-red-600 hover:text-red-700 dark:text-red-400 w-max flex items-center gap-1 transition-all">
                                <i class="fas fa-trash-alt text-[10px]"></i> Cancel Selection
                            </button>
                        </div>
                    </div>
                </div>

                <!-- COMPONENT DIGITAL SIGNATURE (RESPONSIVE) -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300 flex items-center gap-1.5">
                        <i class="fas fa-signature text-amber-500"></i> Digital Signature
                    </label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full">
                        <!-- Preview Box Signature -->
                        <div class="w-24 h-16 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-gray-200 dark:border-gray-700 flex-shrink-0 flex items-center justify-center p-1.5 shadow-sm">
                            <img id="signature-preview" src="" alt="Signature Preview" class="max-w-full max-h-full object-contain hidden">
                            
                            <div id="signature-placeholder" class="text-[10px] text-gray-400 text-center font-medium">
                                <i class="fas fa-pen-nib text-xs block mb-0.5 text-slate-400"></i> No Signature
                            </div>
                        </div>
                        
                        <!-- Input File Custom Outline -->
                        <div class="w-full flex-1 flex flex-col gap-1.5">
                            <input type="file" name="signature" id="signature-input" accept="image/*" required
                                   class="block w-full text-[11px] text-slate-500 bg-white dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer focus:outline-none
                                          file:mr-3 file:py-2 file:px-3 file:rounded-l-md file:border-0 file:text-[11px] file:font-bold 
                                          file:bg-slate-100 file:text-slate-800 dark:file:bg-slate-800 dark:file:text-white 
                                          file:shadow-[inset_0_1px_0_rgba(255,255,255,0.2)]
                                          file:border-r file:border-gray-300 dark:file:border-gray-700
                                          hover:file:bg-slate-200 dark:hover:file:bg-slate-700 transition-all" />
                            
                            <!-- Tombol Remove Signature -->
                            <button type="button" id="btn-remove-signature" class="hidden text-left text-[11px] font-bold text-red-600 hover:text-red-700 dark:text-red-400 w-max flex items-center gap-1 transition-all">
                                <i class="fas fa-trash-alt text-[10px]"></i> Cancel Selection
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BUTTON ACTIONS -->
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="submit" class="w-full md:w-auto md:px-8 py-2.5 bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 text-white rounded-lg text-xs font-bold shadow-md uppercase tracking-wider transition-all hover:opacity-95 active:scale-95">
                    Save Account
                </button>
            </div>
        </form>
    </div>
</div>

<!-- LIVE PREVIEW JAVASCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultProfileImg = 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';

        // --- PREVIEW FOTO PROFIL ---
        const profileInput = document.getElementById('profile-input');
        const profilePreview = document.getElementById('profile-preview');
        const btnRemoveProfile = document.getElementById('btn-remove-profile');

        profileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    profilePreview.src = e.target.result;
                    btnRemoveProfile.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        btnRemoveProfile.addEventListener('click', function() {
            profileInput.value = ""; 
            profilePreview.src = defaultProfileImg; 
            btnRemoveProfile.classList.add('hidden'); 
        });


        // --- PREVIEW TANDA TANGAN ---
        const signatureInput = document.getElementById('signature-input');
        const signaturePreview = document.getElementById('signature-preview');
        const signaturePlaceholder = document.getElementById('signature-placeholder');
        const btnRemoveSignature = document.getElementById('btn-remove-signature');

        signatureInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    signaturePlaceholder.classList.add('hidden');
                    signaturePreview.src = e.target.result;
                    signaturePreview.classList.remove('hidden');
                    btnRemoveSignature.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        btnRemoveSignature.addEventListener('click', function() {
            signatureInput.value = ""; 
            signaturePreview.src = ""; 
            signaturePreview.classList.add('hidden'); 
            signaturePlaceholder.classList.remove('hidden'); 
            btnRemoveSignature.classList.add('hidden'); 
        });
    });
</script>
@endsection