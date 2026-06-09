<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisLaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisLaporan = [
            ['nama' => 'Laporan Penerimaan Kayu Bulat', 'slug' => 'penerimaan_kayu_bulat'],
            ['nama' => 'Laporan Mutasi Kayu Bulat (LMKB)', 'slug' => 'mutasi_kayu_bulat'],
            ['nama' => 'Laporan Penerimaan Kayu Olahan', 'slug' => 'penerimaan_kayu_olahan'],
            ['nama' => 'Laporan Mutasi Kayu Olahan (LMKO)', 'slug' => 'mutasi_kayu_olahan'],
            ['nama' => 'Laporan Penjualan Kayu Olahan', 'slug' => 'penjualan_kayu_olahan'],
        ];

        foreach ($jenisLaporan as $item) {
            DB::table('master_jenis_laporan')->updateOrInsert(
                ['slug' => $item['slug']],
                ['nama' => $item['nama'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
