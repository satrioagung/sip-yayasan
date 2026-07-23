{{-- resources/views/receipt-templates/_form.blade.php --}}
@props(['t' => null])

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-semibold text-gray-700 pb-3 border-b border-gray-100">Informasi Template</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Nama --}}
        <div class="md:col-span-2">
            <label for="nama_template" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Template <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nama_template" name="nama_template"
                   value="{{ old('nama_template', $t?->nama_template) }}"
                   placeholder="contoh: Struk A4 Standar, Thermal 80mm Premium"
                   class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('nama_template') ? 'border-red-400' : 'border-gray-200' }}">
            @error('nama_template') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Ukuran --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Ukuran Struk <span class="text-red-500">*</span></label>
            <select name="ukuran" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach(\App\Models\ReceiptTemplate::$ukuranLabels as $val => $label)
                    <option value="{{ $val }}" {{ old('ukuran', $t?->ukuran ?? 'a4') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('ukuran') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Toggle: Default + Logo + QR --}}
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_default" value="0">
                <input type="checkbox" id="is_default" name="is_default" value="1"
                       {{ old('is_default', $t?->is_default) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_default" class="text-sm font-medium text-gray-700">Jadikan Template Default</label>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="show_logo" value="0">
                <input type="checkbox" id="show_logo" name="show_logo" value="1"
                       {{ old('show_logo', $t?->show_logo ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="show_logo" class="text-sm font-medium text-gray-700">Tampilkan Logo Lembaga</label>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="show_qr" value="0">
                <input type="checkbox" id="show_qr" name="show_qr" value="1"
                       {{ old('show_qr', $t?->show_qr) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="show_qr" class="text-sm font-medium text-gray-700">Tampilkan QR Code</label>
            </div>
        </div>

        {{-- Header HTML --}}
        <div class="md:col-span-2">
            <label for="header" class="block text-sm font-medium text-gray-700 mb-1.5">
                Header Struk (HTML / Teks)
            </label>
            <textarea id="header" name="header" rows="5"
                      placeholder="Isi header struk. Contoh:&#10;&lt;p style=&quot;text-align:center;font-weight:bold;&quot;&gt;@{{ school_name }}&lt;/p&gt;&#10;&lt;p style=&quot;text-align:center;&quot;&gt;@{{ print_date }}&lt;/p&gt;"
                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('header', $t?->header) }}</textarea>
            <p class="mt-1 text-xs text-gray-400">Bisa menggunakan HTML sederhana dan placeholder. Kosongkan untuk menggunakan header default (logo + nama lembaga).</p>
        </div>

        {{-- Footer HTML --}}
        <div class="md:col-span-2">
            <label for="footer" class="block text-sm font-medium text-gray-700 mb-1.5">
                Footer Struk (HTML / Teks)
            </label>
            <textarea id="footer" name="footer" rows="4"
                      placeholder="Isi footer struk. Contoh:&#10;&lt;p style=&quot;text-align:center;font-size:10px;&quot;&gt;Terima kasih atas kepercayaan Anda.&lt;/p&gt;&#10;&lt;p style=&quot;text-align:center;font-size:9px;&quot;&gt;Dicetak: @{{ print_date }}&lt;/p&gt;"
                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y">{{ old('footer', $t?->footer) }}</textarea>
            <p class="mt-1 text-xs text-gray-400">Kosongkan untuk menggunakan footer default dari pengaturan lembaga.</p>
        </div>

    </div>
</div>
