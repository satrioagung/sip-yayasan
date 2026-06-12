@extends('layouts.app')

@section('title', 'Import Siswa dari Excel')
@section('page-title', 'Import Siswa dari Excel')
@section('breadcrumb', 'Beranda / Data Siswa / Import')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

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
                    <p class="text-sm font-medium text-gray-700">Drag & drop atau klik untuk memilih file</p>
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

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('students.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary" :disabled="!file" :class="!file ? 'opacity-50 cursor-not-allowed' : ''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Proses Import
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
        },

        clearFile() {
            this.file = null;
            this.$refs.fileInput.value = '';
        }
    }
}
</script>
@endpush
@endsection
