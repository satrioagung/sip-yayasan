<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Institution::class);
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:150'],
            'jenjang'             => ['nullable', 'string', Rule::in(\App\Models\Institution::daftarJenjang())],
            'code'                => ['required', 'string', 'max:20', 'unique:institutions,code', 'alpha_dash'],
            'email'               => ['nullable', 'email', 'max:100'],
            'phone'               => ['nullable', 'string', 'max:20'],
            'address'             => ['nullable', 'string', 'max:500'],
            'principal_name'      => ['nullable', 'string', 'max:100'],
            'nip_kepala'          => ['nullable', 'string', 'max:30'],
            'logo'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'warna_tema'          => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'footer_struk'        => ['nullable', 'string', 'max:300'],
            'prefix_nomor_struk'  => ['nullable', 'string', 'max:10', 'alpha_num'],
            'is_active'           => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                => 'Nama lembaga wajib diisi.',
            'name.max'                     => 'Nama lembaga maksimal 150 karakter.',
            'jenjang.in'                   => 'Jenjang tidak valid.',
            'code.required'                => 'Kode lembaga wajib diisi.',
            'code.unique'                  => 'Kode lembaga sudah digunakan.',
            'code.alpha_dash'              => 'Kode hanya boleh huruf, angka, dan tanda hubung.',
            'email.email'                  => 'Format email tidak valid.',
            'phone.max'                    => 'Nomor telepon maksimal 20 karakter.',
            'logo.image'                   => 'File logo harus berupa gambar.',
            'logo.mimes'                   => 'Logo harus berformat JPG, PNG, atau WebP.',
            'logo.max'                     => 'Ukuran logo maksimal 1 MB.',
            'warna_tema.regex'             => 'Format warna tema harus berupa kode HEX (contoh: #2563eb).',
            'footer_struk.max'             => 'Teks footer struk maksimal 300 karakter.',
            'prefix_nomor_struk.alpha_num' => 'Prefix struk hanya boleh berisi huruf dan angka.',
            'prefix_nomor_struk.max'       => 'Prefix struk maksimal 10 karakter.',
        ];
    }
}
