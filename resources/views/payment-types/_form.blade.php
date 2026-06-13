{{-- resources/views/payment-types/_form.blade.php --}}
@props(['paymentType' => null, 'schoolYears' => collect()])

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-semibold text-gray-700 pb-3 border-b border-gray-100">Informasi Jenis Pembayaran</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Nama --}}
        <div class="md:col-span-2">
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Jenis Pembayaran <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nama" name="nama"
                   value="{{ old('nama', $paymentType?->nama) }}"
                   placeholder="contoh: SPP Bulanan, Ujian Semester"
                   class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('nama') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('nama') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Kode --}}
        <div>
            <label for="kode" class="block text-sm font-medium text-gray-700 mb-1.5">
                Kode <span class="text-red-500">*</span>
            </label>
            <input type="text" id="kode" name="kode"
                   value="{{ old('kode', $paymentType?->kode) }}"
                   placeholder="contoh: SPP, UJIAN, SERAGAM"
                   maxlength="20"
                   class="w-full px-4 py-2.5 border rounded-xl text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('kode') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                   oninput="this.value = this.value.toUpperCase()">
            <p class="mt-1 text-xs text-gray-400">Huruf kapital dan angka, tanpa spasi. Unik per lembaga.</p>
            @error('kode') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Tipe --}}
        <div>
            <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1.5">
                Tipe Pembayaran <span class="text-red-500">*</span>
            </label>
            <select id="tipe" name="tipe"
                    class="w-full px-4 py-2.5 border rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500
                           {{ $errors->has('tipe') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Pilih Tipe —</option>
                @foreach(\App\Models\PaymentType::$tipeLabels as $val => $label)
                    <option value="{{ $val }}" {{ old('tipe', $paymentType?->tipe) === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('tipe') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Nominal Default --}}
        <div>
            <label for="nominal_default" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nominal Default <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                <input type="text" id="nominal_input"
                       value="{{ number_format(old('nominal_default', $paymentType?->nominal_default ?? 0), 0, ',', '.') }}"
                       placeholder="0"
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       oninput="formatNominal(this)">
                <input type="hidden" id="nominal_default" name="nominal_default"
                       value="{{ old('nominal_default', $paymentType?->nominal_default ?? 0) }}">
            </div>
            @error('nominal_default') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Tahun Ajaran (opsional) --}}
        <div>
            <label for="school_year_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                Tahun Ajaran (Opsional)
            </label>
            <select id="school_year_id" name="school_year_id"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">— Semua Tahun Ajaran —</option>
                @foreach($schoolYears as $sy)
                    <option value="{{ $sy->id }}"
                        {{ old('school_year_id', $paymentType?->school_year_id) == $sy->id ? 'selected' : '' }}>
                        {{ $sy->nama }}{{ $sy->aktif ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Keterangan --}}
        <div class="md:col-span-2">
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
            <textarea id="keterangan" name="keterangan" rows="2"
                      placeholder="Keterangan tambahan (opsional)"
                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('keterangan', $paymentType?->keterangan) }}</textarea>
        </div>

        {{-- Checkbox: Bisa Cicil --}}
        <div class="flex items-start gap-3">
            <input type="hidden" name="bisa_cicil" value="0">
            <input type="checkbox" id="bisa_cicil" name="bisa_cicil" value="1"
                   {{ old('bisa_cicil', $paymentType?->bisa_cicil) ? 'checked' : '' }}
                   class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
            <div>
                <label for="bisa_cicil" class="text-sm font-medium text-gray-700 cursor-pointer">Dapat Dicicil</label>
                <p class="text-xs text-gray-400 mt-0.5">Pembayaran dapat dilakukan lebih dari satu kali.</p>
            </div>
        </div>

        {{-- Checkbox: Wajib --}}
        <div class="flex items-start gap-3">
            <input type="hidden" name="wajib" value="0">
            <input type="checkbox" id="wajib" name="wajib" value="1"
                   {{ old('wajib', $paymentType?->wajib ?? true) ? 'checked' : '' }}
                   class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
            <div>
                <label for="wajib" class="text-sm font-medium text-gray-700 cursor-pointer">Pembayaran Wajib</label>
                <p class="text-xs text-gray-400 mt-0.5">Semua siswa wajib membayar jenis ini.</p>
            </div>
        </div>

        {{-- Status Aktif --}}
        @php $aktifDefault = old('aktif', $paymentType?->aktif ?? true) ? '1' : '0'; @endphp
        <div class="md:col-span-2 flex items-center gap-3"
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
            <label class="text-sm font-medium text-gray-700 cursor-pointer" @click="on = !on">
                <span x-text="on ? 'Aktif' : 'Nonaktif'"></span>
            </label>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatNominal(input) {
        let raw = input.value.replace(/\D/g, '');
        document.getElementById('nominal_default').value = raw;
        input.value = raw ? new Intl.NumberFormat('id-ID').format(parseInt(raw)) : '';
    }
</script>
@endpush
