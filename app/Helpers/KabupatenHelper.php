<?php

namespace App\Helpers;

class KabupatenHelper
{
    /**
     * Mapping nama kabupaten informal ke nama resmi API
     */
    private static $mapping = [
        // Kota
        'solo' => 'Kota Surakarta',
        'surakarta' => 'Kota Surakarta',
        'semarang' => 'Kota Semarang',
        'salatiga' => 'Kota Salatiga',
        'magelang' => 'Kota Magelang',
        'pekalongan' => 'Kota Pekalongan',
        'tegal' => 'Kota Tegal',
        
        // Kabupaten (untuk yang sering salah tulis)
        'kab. semarang' => 'Kabupaten Semarang',
        'kab semarang' => 'Kabupaten Semarang',
        'kab. magelang' => 'Kabupaten Magelang',
        'kab magelang' => 'Kabupaten Magelang',
        'kab. pekalongan' => 'Kabupaten Pekalongan',
        'kab pekalongan' => 'Kabupaten Pekalongan',
        'kab. tegal' => 'Kabupaten Tegal',
        'kab tegal' => 'Kabupaten Tegal',
    ];

    /**
     * Normalize nama kabupaten ke format API
     */
    public static function normalize($kabupaten)
    {
        if (empty($kabupaten)) {
            return $kabupaten;
        }

        $lower = strtolower(trim($kabupaten));
        
        // Cek di mapping
        if (isset(self::$mapping[$lower])) {
            return self::$mapping[$lower];
        }

        // Jika sudah format "Kota X" atau "Kabupaten X", return as is
        if (preg_match('/^(Kota|Kabupaten)\s+/i', $kabupaten)) {
            return ucwords(strtolower($kabupaten));
        }

        // Jika belum ada prefix, coba deteksi apakah kota atau kabupaten
        // Default: tambahkan "Kabupaten" jika tidak ada di mapping kota
        $kotaList = ['surakarta', 'semarang', 'salatiga', 'magelang', 'pekalongan', 'tegal'];
        
        if (in_array($lower, $kotaList)) {
            return 'Kota ' . ucfirst($lower);
        }

        // Default: Kabupaten
        return 'Kabupaten ' . ucwords($lower);
    }

    /**
     * Get all valid kabupaten names from API
     */
    public static function getValidNames()
    {
        return [
            'Kabupaten Banjarnegara',
            'Kabupaten Banyumas',
            'Kabupaten Batang',
            'Kabupaten Blora',
            'Kabupaten Boyolali',
            'Kabupaten Brebes',
            'Kabupaten Cilacap',
            'Kabupaten Demak',
            'Kabupaten Grobogan',
            'Kabupaten Jepara',
            'Kabupaten Karanganyar',
            'Kabupaten Kebumen',
            'Kabupaten Kendal',
            'Kabupaten Klaten',
            'Kabupaten Kudus',
            'Kabupaten Magelang',
            'Kabupaten Pati',
            'Kabupaten Pekalongan',
            'Kabupaten Pemalang',
            'Kabupaten Purbalingga',
            'Kabupaten Purworejo',
            'Kabupaten Rembang',
            'Kabupaten Semarang',
            'Kabupaten Sragen',
            'Kabupaten Sukoharjo',
            'Kabupaten Tegal',
            'Kabupaten Temanggung',
            'Kabupaten Wonogiri',
            'Kabupaten Wonosobo',
            'Kota Magelang',
            'Kota Pekalongan',
            'Kota Salatiga',
            'Kota Semarang',
            'Kota Surakarta',
            'Kota Tegal',
        ];
    }
}
