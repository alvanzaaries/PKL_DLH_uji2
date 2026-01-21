<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterJenisProduksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisProduksi = [
            // Industri Primer
            ['nama' => 'Gergajian', 'kategori' => 'primer', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri penggergajian kayu'],
            ['nama' => 'Veneer', 'kategori' => 'primer', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri veneer kayu'],
            ['nama' => 'Plywood', 'kategori' => 'primer', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri kayu lapis'],
            ['nama' => 'Moulding', 'kategori' => 'primer', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri moulding kayu'],
            ['nama' => 'Barecore', 'kategori' => 'primer', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri barecore'],
            ['nama' => 'Finger Joint', 'kategori' => 'primer', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri finger joint'],
            ['nama' => 'Dowel', 'kategori' => 'primer', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri dowel'],
            ['nama' => 'Palet', 'kategori' => 'primer', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri palet kayu'],

            // Industri Sekunder
            ['nama' => 'Meubel', 'kategori' => 'sekunder', 'satuan' => 'unit/tahun', 'keterangan' => 'Industri meubel furniture'],
            ['nama' => 'Kerajinan', 'kategori' => 'sekunder', 'satuan' => 'unit/tahun', 'keterangan' => 'Industri kerajinan kayu'],
            ['nama' => 'Konstruksi', 'kategori' => 'sekunder', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri konstruksi kayu'],
            ['nama' => 'Flooring', 'kategori' => 'sekunder', 'satuan' => 'm²/tahun', 'keterangan' => 'Industri lantai kayu'],
            ['nama' => 'Decking', 'kategori' => 'sekunder', 'satuan' => 'm²/tahun', 'keterangan' => 'Industri decking kayu'],
            ['nama' => 'Particle Board', 'kategori' => 'both', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri particle board'],
            ['nama' => 'MDF (Medium Density Fiberboard)', 'kategori' => 'both', 'satuan' => 'm³/tahun', 'keterangan' => 'Industri MDF'],
            ['nama' => 'Pulp & Paper', 'kategori' => 'both', 'satuan' => 'ton/tahun', 'keterangan' => 'Industri pulp dan kertas'],
            ['nama' => 'Lainnya', 'kategori' => 'both', 'satuan' => 'unit', 'keterangan' => 'Jenis produksi lainnya (isi manual)'],
        ];

        foreach ($jenisProduksi as $item) {
            \App\Models\MasterJenisProduksi::updateOrCreate(
                ['nama' => $item['nama']], // Cari berdasarkan nama
                [
                    'kategori' => $item['kategori'],
                    'satuan' => $item['satuan'],
                    'keterangan' => $item['keterangan'],
                    'aktif' => true,
                ]
            );
        }
    }
}
