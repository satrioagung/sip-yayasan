{{-- Partial form untuk siswa --}}
@props(['student' => null, 'kelas' => []])

<div class="space-y-5">

    {{-- Data Utama --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Data Utama</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label for="nis" class="block text-sm font-medium text-gray-700 mb-1.5">
                    NIS (Nomor Induk Siswa) <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nis" name="nis" value="{{ old('nis', $student?->nis) }}"
                       placeholder="contoh: 2024001"
                       class="form-input font-mono {{ $errors->has('nis') ? 'form-input-error' : '' }}">
                @error('nis') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1.5">NISN</label>
                <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $student?->nisn) }}"
                       placeholder="10 digit NISN (opsional)"
                       class="form-input font-mono">
            </div>

            <div class="md:col-span-2">
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_lengkap" name="nama_lengkap"
                       value="{{ old('nama_lengkap', $student?->nama_lengkap) }}"
                       placeholder="Nama lengkap sesuai ijazah"
                       class="form-input {{ $errors->has('nama_lengkap') ? 'form-input-error' : '' }}">
                @error('nama_lengkap') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Jenis Kelamin <span class="text-red-500">*</span>
                </label>
                <select id="jenis_kelamin" name="jenis_kelamin"
                        class="form-input {{ $errors->has('jenis_kelamin') ? 'form-input-error' : '' }}">
                    <option value="">— Pilih —</option>
                    <option value="L" {{ old('jenis_kelamin', $student?->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $student?->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1.5">Kelas</label>
                <select id="class_id" name="class_id" class="form-input">
                    <option value="">— Belum Masuk Kelas —</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}"
                            {{ old('class_id', $student?->class_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Data Pribadi --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Data Pribadi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1.5">Tempat Lahir</label>
                <input type="text" id="tempat_lahir" name="tempat_lahir"
                       value="{{ old('tempat_lahir', $student?->tempat_lahir) }}"
                       placeholder="Kota tempat lahir"
                       class="form-input">
            </div>

            <div>
                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tanggal Lahir
                    <span class="text-gray-400 font-normal">(dd/mm/yyyy)</span>
                </label>
                <input type="text" id="tanggal_lahir" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', $student?->tanggal_lahir?->format('d/m/Y')) }}"
                       placeholder="17/08/2009"
                       maxlength="10"
                       x-data x-mask="99/99/9999"
                       class="form-input font-mono {{ $errors->has('tanggal_lahir') ? 'form-input-error' : '' }}">
                @error('tanggal_lahir') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2"
                          placeholder="Alamat lengkap siswa"
                          class="form-input resize-none">{{ old('alamat', $student?->alamat) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Data Orang Tua --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Data Orang Tua / Wali</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="nama_ortu" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Orang Tua / Wali</label>
                <input type="text" id="nama_ortu" name="nama_ortu"
                       value="{{ old('nama_ortu', $student?->nama_ortu) }}"
                       placeholder="Nama orang tua atau wali"
                       class="form-input">
            </div>
            <div>
                <label for="no_hp_ortu" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor HP Orang Tua</label>
                <input type="text" id="no_hp_ortu" name="no_hp_ortu"
                       value="{{ old('no_hp_ortu', $student?->no_hp_ortu) }}"
                       placeholder="08xxxxxxxxxx"
                       class="form-input font-mono">
            </div>
        </div>
    </div>

    {{-- Status --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        @php $aktifDefault = old('aktif', $student?->aktif ?? true) ? '1' : '0'; @endphp
        <label class="flex items-center gap-3 cursor-pointer"
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
                <p class="text-sm font-medium text-gray-700">Siswa Aktif</p>
                <p class="text-xs text-gray-400">Nonaktifkan jika siswa sudah keluar atau lulus.</p>
            </div>
        </label>
    </div>

</div>
