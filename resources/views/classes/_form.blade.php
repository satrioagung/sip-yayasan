{{-- Partial form untuk kelas --}}
@props(['class' => null, 'tahunAjaran' => []])

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Data Kelas</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Tahun Ajaran --}}
        <div class="md:col-span-2">
            <label for="school_year_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                Tahun Ajaran <span class="text-red-500">*</span>
            </label>
            <select id="school_year_id" name="school_year_id"
                    class="form-input {{ $errors->has('school_year_id') ? 'form-input-error' : '' }}">
                <option value="">— Pilih Tahun Ajaran —</option>
                @foreach($tahunAjaran as $ta)
                    <option value="{{ $ta->id }}"
                        {{ old('school_year_id', $class?->school_year_id) == $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama }}{{ $ta->aktif ? ' ✓ (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('school_year_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Nama Kelas --}}
        <div>
            <label for="nama_kelas" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Kelas <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nama_kelas" name="nama_kelas"
                   value="{{ old('nama_kelas', $class?->nama_kelas) }}"
                   placeholder="contoh: X RPL 1"
                   class="form-input {{ $errors->has('nama_kelas') ? 'form-input-error' : '' }}">
            @error('nama_kelas') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Tingkat --}}
        <div>
            <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-1.5">
                Tingkat <span class="text-red-500">*</span>
            </label>
            <input type="text" id="tingkat" name="tingkat"
                   value="{{ old('tingkat', $class?->tingkat) }}"
                   placeholder="contoh: X, XI, XII, 7, 8"
                   class="form-input {{ $errors->has('tingkat') ? 'form-input-error' : '' }}">
            @error('tingkat') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Jurusan --}}
        <div>
            <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1.5">Jurusan / Program Keahlian</label>
            <input type="text" id="jurusan" name="jurusan"
                   value="{{ old('jurusan', $class?->jurusan) }}"
                   placeholder="contoh: RPL, TKJ, Akuntansi (opsional)"
                   class="form-input">
        </div>

        {{-- Wali Kelas --}}
        <div>
            <label for="wali_kelas" class="block text-sm font-medium text-gray-700 mb-1.5">Wali Kelas</label>
            <input type="text" id="wali_kelas" name="wali_kelas"
                   value="{{ old('wali_kelas', $class?->wali_kelas) }}"
                   placeholder="Nama wali kelas (opsional)"
                   class="form-input">
        </div>
    </div>

    {{-- Status Aktif --}}
    @php $aktifDefault = old('aktif', $class?->aktif ?? true) ? '1' : '0'; @endphp
    <label class="flex items-center gap-3 cursor-pointer pt-1"
           x-data="{ on: {{ $aktifDefault === '1' ? 'true' : 'false' }} }"
           x-init="$watch('on', val => $refs.aktifInput.value = val ? '1' : '0')">
        <div class="relative">
            <input type="hidden" name="aktif" x-ref="aktifInput" value="{{ $aktifDefault }}">
            <button type="button" @click="on = !on"
                :class="on ? 'bg-blue-600' : 'bg-gray-200'"
                class="relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                <span
                    :class="on ? 'translate-x-[23px]' : 'translate-x-[3px]'"
                    class="absolute top-1 left-0 w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200">
                </span>
            </button>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">Kelas Aktif</p>
            <p class="text-xs text-gray-400">Kelas nonaktif tidak bisa diisi siswa baru.</p>
        </div>
    </label>
</div>
