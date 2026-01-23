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
            "KPH Kebonharjo",
            "KPH Ngawi",
            "KPH Cepu",
            "KPH Randublatung",
            "KPH Blora",
            "KPH Mantingan", 
            "KPH Pati",
            "KPH Purwodadi",
            "KPH Gundih",
            "KPH Telawa",
            "KPH Semarang",
            "KPH Surakarta",
            "KPH Kedu Selatan",
            "KPH Kedu Utara",
            "KPH Kendal",
            "KPH Pekalongan Timur",
            "KPH Pemalang",
            "KPH Pekalongan",
            "KPH Balapulang",
            "KPH Banyumas Barat",
            "KPH Banyumas Timur"
        ];

        // Ensure unique values
        $kphs = array_unique($kphs);

        foreach ($kphs as $nama) {
            Kph::firstOrCreate(['nama' => $nama]);
        }
    }
}
