<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrasi data existing dari industri_primer ke tabel baru
        $industriPrimer = DB::table('industri_primer')
            ->whereNotNull('jenis_produksi')
            ->where('jenis_produksi', '!=', '')
            ->get();

        foreach ($industriPrimer as $item) {
            // Cari atau buat jenis produksi di master
            $masterJenisProduksi = DB::table('master_jenis_produksi')
                ->where('nama', $item->jenis_produksi)
                ->first();

            if (!$masterJenisProduksi) {
                $jenisProduksiId = DB::table('master_jenis_produksi')->insertGetId([
                    'nama' => $item->jenis_produksi,
                    'kategori' => 'primer',
                    'satuan' => 'm³/tahun',
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $jenisProduksiId = $masterJenisProduksi->id;
            }

            // Insert ke tabel pivot
            DB::table('industri_jenis_produksi')->insert([
                'industri_type' => 'App\Models\IndustriPrimer',
                'industri_id' => $item->id,
                'jenis_produksi_id' => $jenisProduksiId,
                'kapasitas_izin' => $item->kapasitas_izin ?? '0',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Migrasi data dari industri_sekunder
        $industriSekunder = DB::table('industri_sekunder')
            ->whereNotNull('jenis_produksi')
            ->where('jenis_produksi', '!=', '')
            ->get();

        foreach ($industriSekunder as $item) {
            $masterJenisProduksi = DB::table('master_jenis_produksi')
                ->where('nama', $item->jenis_produksi)
                ->first();

            if (!$masterJenisProduksi) {
                $jenisProduksiId = DB::table('master_jenis_produksi')->insertGetId([
                    'nama' => $item->jenis_produksi,
                    'kategori' => 'sekunder',
                    'satuan' => 'm³/tahun',
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $jenisProduksiId = $masterJenisProduksi->id;
            }

            DB::table('industri_jenis_produksi')->insert([
                'industri_type' => 'App\Models\IndustriSekunder',
                'industri_id' => $item->id,
                'jenis_produksi_id' => $jenisProduksiId,
                'kapasitas_izin' => $item->kapasitas_izin ?? '0',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan data ke kolom jenis_produksi (jika rollback)
        $relations = DB::table('industri_jenis_produksi')->get();

        foreach ($relations as $relation) {
            $jenisProduksi = DB::table('master_jenis_produksi')
                ->where('id', $relation->jenis_produksi_id)
                ->first();

            if ($relation->industri_type === 'App\Models\IndustriPrimer') {
                DB::table('industri_primer')
                    ->where('id', $relation->industri_id)
                    ->update(['jenis_produksi' => $jenisProduksi->nama ?? '']);
            } elseif ($relation->industri_type === 'App\Models\IndustriSekunder') {
                DB::table('industri_sekunder')
                    ->where('id', $relation->industri_id)
                    ->update(['jenis_produksi' => $jenisProduksi->nama ?? '']);
            }
        }
    }
};
