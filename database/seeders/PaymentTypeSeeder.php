<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\PaymentType;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = Institution::all();

        foreach ($institutions as $institution) {
            $schoolYear = SchoolYear::where('institution_id', $institution->id)
                ->aktif()
                ->first();

            $types = [
                [
                    'kode'            => 'SPP',
                    'nama'            => 'SPP Bulanan',
                    'nominal_default' => 250000,
                    'tipe'            => 'bulanan',
                    'bisa_cicil'      => true,
                    'wajib'           => true,
                    'keterangan'      => 'Sumbangan Pembinaan Pendidikan bulanan',
                ],
                [
                    'kode'            => 'UJIAN',
                    'nama'            => 'Ujian Semester',
                    'nominal_default' => 150000,
                    'tipe'            => 'tahunan',
                    'bisa_cicil'      => false,
                    'wajib'           => true,
                    'keterangan'      => 'Biaya ujian akhir semester',
                ],
                [
                    'kode'            => 'SERAGAM',
                    'nama'            => 'Seragam',
                    'nominal_default' => 350000,
                    'tipe'            => 'sekali',
                    'bisa_cicil'      => true,
                    'wajib'           => false,
                    'keterangan'      => 'Pembelian seragam sekolah',
                ],
            ];

            foreach ($types as $type) {
                PaymentType::firstOrCreate(
                    [
                        'institution_id' => $institution->id,
                        'kode'           => $type['kode'],
                    ],
                    array_merge($type, [
                        'institution_id' => $institution->id,
                        'school_year_id' => $schoolYear?->id,
                        'aktif'          => true,
                    ])
                );
            }
        }
    }
}
