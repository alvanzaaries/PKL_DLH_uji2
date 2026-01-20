<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kph;

class KphSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kphs = [
            'Kendal',
            'Semarang',
            'Telawa',
            'Purwodadi',
            'Kedu Selatan',
            'Kedu Utara',
            'Banyumas Timur',
            'Banyumas Barat',
            'Gundih',
            'Surakarta',
            'Blora',
            'Balapulang',
            'Cepu',
            'Kebonharjo',
            'Randublatung',
            'Pati',
            'Pemalang',
            'Pekalongan Timur',
            'Pekalongan Barat',
            'Mantingan',
        ];

        // Ensure unique values
        $kphs = array_unique($kphs);

        foreach ($kphs as $nama) {
            Kph::firstOrCreate(['nama' => $nama]);
        }
    }
}
