@extends('layouts.app')

@section('title', 'Import Siswa dari Excel')
@section('page-title', 'Import Siswa dari Excel')
@section('breadcrumb', 'Beranda / Data Siswa / Import')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Panduan --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-blue-800 mb-2">Panduan Import</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Unduh template Excel terlebih dahulu sebagai panduan format kolom.</li>
                    <li>• Kolom wajib: <strong>NIS</strong>, <strong>Nama Lengkap</strong>, <strong>Jenis Kelamin (L/P)</strong>.</li>
                    <li>• Format tanggal lahir: <strong>dd/mm/yyyy</strong> (contoh: 17/08/2009).</li>
                    <li>• Jika NIS sudah ada, data siswa yang ada akan <strong>diperbarui</strong>.</li>
                    <li>• Ukuran file maksimal 5 MB (format: XLSX, XLS, atau CSV).</li>
                </ul>
                <a href="{{ route('students.template') }}"
                   class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Template Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Form Upload --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
         x-data="importApp()" @dragover.prevent @drop.prevent="handleDrop($event)">

        <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3 mb-5">Unggah File Excel</h3>

        <form method="POST" action="{{ route('students.import.process') }}" enctype="multipart/form-data"
              id="importForm">
            @csrf

            {{-- Pilih Kelas --}}
            <div class="mb-5">
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Masukkan ke Kelas (opsional)
                </label>
                <select name="class_id" id="class_id" class="form-input">
                    <option value="">— Tidak masuk ke kelas tertentu —</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Drop zone --}}
            <div class="relative border-2 border-dashed rounded-2xl p-10 text-center transition-colors cursor-pointer"
                 :class="dragging ? 'border-blue-400 bg-blue-50' : (file ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50')"
                 @dragenter.prevent="dragging = true"
                 @dragleave.prevent="dragging = false"
                 @click="$refs.fileInput.click()">

                <input type="file" name="file" id="file"
                       x-ref="fileInput"
                       @change="handleFile($event)"
                       accept=".xlsx,.xls,.csv"
                       class="hidden">

                <div x-show="!file">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-700">Drag &amp; drop atau klik untuk memilih file</p>
                    <p class="text-xs text-gray-400 mt-1">XLSX, XLS, CSV · Maks. 5 MB</p>
                </div>

                <div x-show="file" x-cloak>
                    <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-semibold text-green-700" x-text="file?.name"></p>
                    <p class="text-xs text-gray-400 mt-1" x-text="fileSize"></p>
                    <button type="button" @click.stop="clearFile()"
                            class="mt-2 text-xs text-red-500 hover:text-red-700 underline">
                        Ganti file
                    </button>
                </div>
            </div>

            @error('file') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror

            {{-- Loading preview --}}
            <div x-show="loadingPreview" class="mt-4 flex items-center gap-2 text-sm text-blue-600">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Membaca file dan menganalisis data...
            </div>

            {{-- Preview Table --}}
            <div x-show="previewData" x-cloak class="mt-5">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">Preview Data</h4>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="flex items-center gap-1.5 text-green-700 font-medium">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span x-text="previewData?.valid + ' valid'"></span>
                        </span>
                        <span x-show="(previewData?.invalid ?? 0) > 0"
                              class="flex items-center gap-1.5 text-red-600 font-medium">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span x-text="previewData?.invalid + ' tidak valid'"></span>
                        </span>
                        <span class="text-gray-500" x-text="'Total: ' + previewData?.total + ' baris'"></span>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto max-h-72">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                                <tr class="text-gray-500 font-semibold uppercase tracking-wider">
                                    <th class="px-3 py-2.5 text-left w-10">#</th>
                                    <th class="px-3 py-2.5 text-left">NIS</th>
                                    <th class="px-3 py-2.5 text-left">Nama Lengkap</th>
                                    <th class="px-3 py-2.5 text-center">JK</th>
                                    <th class="px-3 py-2.5 text-left">Kelas</th>
                                    <th class="px-3 py-2.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="row in previewData?.rows ?? []" :key="row.baris">
                                    <tr :class="row.valid ? 'hover:bg-gray-50' : 'bg-red-50'">
                                        <td class="px-3 py-2 text-gray-400" x-text="row.baris"></td>
                                        <td class="px-3 py-2 font-mono text-gray-700" x-text="row.nis"></td>
                                        <td class="px-3 py-2 font-medium text-gray-800" x-text="row.nama"></td>
                                        <td class="px-3 py-2 text-center text-gray-600" x-text="row.jk"></td>
                                        <td class="px-3 py-2 text-gray-500" x-text="row.kelas"></td>
                                        <td class="px-3 py-2 text-center">
                                            <template x-if="!row.valid">
                                                <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    <span x-text="row.error"></span>
                                                </span>
                                            </template>
                                            <template x-if="row.valid && row.status === 'update'">
                                                <span class="inline-flex items-center gap-1 text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded-full">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    Update
                                                </span>
                                            </template>
                                            <template x-if="row.valid && row.status === 'baru'">
                                                <span class="inline-flex items-center gap-1 text-green-700 font-medium bg-green-50 px-2 py-0.5 rounded-full">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    Baru
                                                </span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Warning jika ada baris invalid --}}
                <p x-show="(previewData?.invalid ?? 0) > 0"
                   class="mt-2.5 text-xs text-red-600 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Baris yang tidak valid akan dilewati saat import.
                </p>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('students.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary"
                        :disabled="!previewData || previewData.valid === 0"
                        :class="(!previewData || previewData.valid === 0) ? 'opacity-50 cursor-not-allowed' : ''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span x-text="previewData ? `Proses Import (${previewData.valid} siswa)` : 'Proses Import'"></span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
function importApp() {
    return {
        file: null,
        dragging: false,
        fileSize: '',
        loadingPreview: false,
        previewData: null,

        handleFile(event) {
            const f = event.target.files[0];
            if (f) this.setFile(f);
        },

        handleDrop(event) {
            this.dragging = false;
            const f = event.dataTransfer.files[0];
            if (f) {
                this.$refs.fileInput.files = event.dataTransfer.files;
                this.setFile(f);
            }
        },

        setFile(f) {
            this.file = f;
            const kb = (f.size / 1024).toFixed(1);
            this.fileSize = kb < 1024 ? kb + ' KB' : (f.size / 1024 / 1024).toFixed(2) + ' MB';
            this.previewData = null;
            this.fetchPreview();
        },

        clearFile() {
            this.file = null;
            this.$refs.fileInput.value = '';
            this.previewData = null;
        },

        async fetchPreview() {
            if (!this.file) return;

            this.loadingPreview = true;
            this.previewData = null;

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('file', this.file);

            const classId = document.getElementById('class_id')?.value;
            if (classId) formData.append('class_id', classId);

            try {
                const res = await fetch('{{ route("students.import.preview") }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData,
                });

                if (res.ok) {
                    this.previewData = await res.json();
                } else {
                    const err = await res.json();
                    alert(err.message ?? 'Gagal membaca file. Pastikan format file benar.');
                    this.clearFile();
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat membaca file.');
                this.clearFile();
            } finally {
                this.loadingPreview = false;
            }
        },
    }
}
</script>
@endpush
@endsection
