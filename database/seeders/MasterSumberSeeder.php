<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterSumber;

class MasterSumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sumberBahanBaku = [
            [
                'nama' => 'Hutan Rakyat',
                'keterangan' => 'Kayu berasal dari hutan milik rakyat/masyarakat'
            ],
            [
                'nama' => 'Perhutani',
                'keterangan' => 'Kayu berasal dari Perusahaan Umum Perhutani'
            ],
            [
                'nama' => 'Hutan Negara',
                'keterangan' => 'Kayu berasal dari hutan yang dikelola negara'
            ],
            [
                'nama' => 'Hutan HTI (Hutan Tanaman Industri)',
                'keterangan' => 'Kayu berasal dari hutan tanaman industri'
            ],
            [
                'nama' => 'Kayu Import',
                'keterangan' => 'Kayu yang diimpor dari luar negeri'
            ],
            [
                'nama' => 'Kayu Limbah Industri',
                'keterangan' => 'Kayu berasal dari limbah industri pengolahan kayu'
            ],
            [
                'nama' => 'Lainnya',
                'keterangan' => 'Sumber bahan baku lainnya yang tidak termasuk kategori di atas'
            ],
        ];

        foreach ($sumberBahanBaku as $sumber) {
            MasterSumber::updateOrCreate(
                ['nama' => $sumber['nama']],
                $sumber
            );
        }
    }
}
