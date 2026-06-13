{{-- Komponen form field reusable untuk institutions --}}
@props(['institution' => null, 'jenjang' => []])

{{-- ===== BAGIAN 1: IDENTITAS LEMBAGA ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-semibold text-gray-700 pb-3 border-b border-gray-100">Identitas Lembaga</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Nama Lembaga --}}
        <div class="md:col-span-2">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Lembaga <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" name="name"
                   value="{{ old('name', $institution?->name) }}"
                   placeholder="contoh: SMK Kartika Nusantara"
                   class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all
                          {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('name')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Jenjang --}}
        <div>
            <label for="jenjang" class="block text-sm font-medium text-gray-700 mb-1.5">Jenjang Pendidikan</label>
            <select id="jenjang" name="jenjang"
                    class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white
                           {{ $errors->has('jenjang') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Pilih Jenjang —</option>
                @foreach($jenjang as $j)
                    <option value="{{ $j }}" {{ old('jenjang', $institution?->jenjang) === $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
            @error('jenjang') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Kode --}}
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1.5">
                Kode Lembaga <span class="text-red-500">*</span>
            </label>
            <input type="text" id="code" name="code"
                   value="{{ old('code', $institution?->code) }}"
                   placeholder="contoh: SMK-KN-01"
                   class="w-full px-4 py-2.5 border rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all
                          {{ $errors->has('code') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            <p class="mt-1 text-xs text-gray-400">Huruf, angka, dan tanda hubung. Tidak bisa diubah setelah digunakan.</p>
            @error('code') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Lembaga</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $institution?->email) }}"
                   placeholder="admin@sekolah.sch.id"
                   class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                          {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Telepon --}}
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
            <input type="text" id="phone" name="phone"
                   value="{{ old('phone', $institution?->phone) }}"
                   placeholder="021-12345678"
                   class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                          {{ $errors->has('phone') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('phone') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Alamat --}}
        <div class="md:col-span-2">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
            <textarea id="address" name="address" rows="2"
                      placeholder="Jl. Pendidikan No. 1, Kelurahan, Kecamatan, Kota/Kabupaten"
                      class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none
                             {{ $errors->has('address') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">{{ old('address', $institution?->address) }}</textarea>
            @error('address') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- ===== BAGIAN 2: KEPALA SEKOLAH ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-semibold text-gray-700 pb-3 border-b border-gray-100">Kepala Sekolah / Pimpinan</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="principal_name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kepala Sekolah</label>
            <input type="text" id="principal_name" name="principal_name"
                   value="{{ old('principal_name', $institution?->principal_name) }}"
                   placeholder="Drs. Nama Kepala Sekolah, M.Pd."
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label for="nip_kepala" class="block text-sm font-medium text-gray-700 mb-1.5">NIP Kepala Sekolah</label>
            <input type="text" id="nip_kepala" name="nip_kepala"
                   value="{{ old('nip_kepala', $institution?->nip_kepala) }}"
                   placeholder="196501011990011001"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>
</div>

{{-- ===== BAGIAN 3: TAMPILAN & STRUK ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-semibold text-gray-700 pb-3 border-b border-gray-100">Tampilan & Konfigurasi Struk</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Logo --}}
        <div class="md:col-span-2" x-data="logoUpload()">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Logo Lembaga</label>
            <div class="flex items-start gap-4">
                {{-- Preview --}}
                <div class="w-20 h-20 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0 bg-gray-50">
                    <img x-show="preview" :src="preview" class="w-full h-full object-cover rounded-xl">
                    @if($institution?->logo)
                        <img x-show="!preview" src="{{ $institution->logo_url }}" class="w-full h-full object-cover rounded-xl">
                    @else
                        <svg x-show="!preview" class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" id="logo" name="logo" accept="image/jpg,image/jpeg,image/png,image/webp"
                           @change="previewLogo($event)"
                           class="hidden">
                    <label for="logo"
                           class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Pilih Gambar
                    </label>
                    <p class="text-xs text-gray-400 mt-1.5">JPG, PNG, atau WebP. Maks. 1 MB.</p>
                    @if($institution?->logo)
                        <form method="POST" action="{{ route('institutions.hapusLogo', $institution) }}"
                              data-confirm="Hapus logo lembaga ini? Tidak dapat dikembalikan."
                              class="mt-2 inline-block">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-500 hover:text-red-700 underline">
                                Hapus logo saat ini
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @error('logo') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Warna Tema --}}
        <div>
            <label for="warna_tema" class="block text-sm font-medium text-gray-700 mb-1.5">Warna Tema</label>
            <div class="flex items-center gap-3">
                <input type="color" id="warna_tema_picker"
                       value="{{ old('warna_tema', $institution?->warna_tema ?? '#2563eb') }}"
                       class="w-10 h-10 rounded-lg cursor-pointer border border-gray-200"
                       oninput="document.getElementById('warna_tema').value = this.value">
                <input type="text" id="warna_tema" name="warna_tema"
                       value="{{ old('warna_tema', $institution?->warna_tema ?? '#2563eb') }}"
                       placeholder="#2563eb"
                       class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       oninput="document.getElementById('warna_tema_picker').value = this.value">
            </div>
            @error('warna_tema') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Prefix Nomor Struk --}}
        <div>
            <label for="prefix_nomor_struk" class="block text-sm font-medium text-gray-700 mb-1.5">Prefix Nomor Struk</label>
            <input type="text" id="prefix_nomor_struk" name="prefix_nomor_struk"
                   value="{{ old('prefix_nomor_struk', $institution?->prefix_nomor_struk ?? 'SPP') }}"
                   placeholder="SPP" maxlength="10"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <p class="mt-1 text-xs text-gray-400">Contoh: SPP → Nomor struk: SPP/2024/001</p>
            @error('prefix_nomor_struk') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Footer Struk --}}
        <div class="md:col-span-2">
            <label for="footer_struk" class="block text-sm font-medium text-gray-700 mb-1.5">Teks Footer Struk</label>
            <textarea id="footer_struk" name="footer_struk" rows="2"
                      placeholder="Contoh: Struk ini adalah bukti pembayaran yang sah. Terima kasih."
                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('footer_struk', $institution?->footer_struk) }}</textarea>
            @error('footer_struk') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- ===== BAGIAN 4: STATUS ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-gray-700 pb-3 border-b border-gray-100 mb-4">Status Lembaga</h3>
    @php $isActiveDefault = old('is_active', $institution?->is_active ?? true) ? '1' : '0'; @endphp
    <label class="flex items-center gap-3 cursor-pointer"
           x-data="{ on: {{ $isActiveDefault === '1' ? 'true' : 'false' }} }"
           x-init="$watch('on', val => $refs.isActiveInput.value = val ? '1' : '0')">
        <div class="relative">
            <input type="hidden" name="is_active" x-ref="isActiveInput" value="{{ $isActiveDefault }}">
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
            <p class="text-sm font-medium text-gray-700">Lembaga Aktif</p>
            <p class="text-xs text-gray-400">Lembaga yang tidak aktif tidak dapat diakses oleh penggunanya.</p>
        </div>
    </label>
</div>

@push('scripts')
<script>
    function logoUpload() {
        return {
            preview: null,
            previewLogo(event) {
                const file = event.target.files[0];
                if (file) {
                    this.preview = URL.createObjectURL(file);
                }
            }
        }
    }
</script>
@endpush
