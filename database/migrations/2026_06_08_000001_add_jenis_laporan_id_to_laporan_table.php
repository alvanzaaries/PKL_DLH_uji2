<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom jenis_laporan_id sebagai nullable dahulu agar data lama bisa dimigrasi
        if (!Schema::hasColumn('laporan', 'jenis_laporan_id')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->foreignId('jenis_laporan_id')->nullable()->after('industri_id')->constrained('master_jenis_laporan')->onDelete('restrict');
            });
        }

        // 2. Jalankan seeder master jenis laporan untuk memastikan data referensi ada sebelum memetakan data lama
        $seeder = new \Database\Seeders\JenisLaporanSeeder();
        $seeder->run();

        // 3. Migrasi data lama dari kolom string 'jenis_laporan' ke foreign key 'jenis_laporan_id'
        $laporans = DB::table('laporan')->get();
        foreach ($laporans as $laporan) {
            // Cari master_jenis_laporan berdasarkan nama string jenis_laporan
            if (isset($laporan->jenis_laporan) && $laporan->jenis_laporan_id === null) {
                $master = DB::table('master_jenis_laporan')
                    ->where('nama', $laporan->jenis_laporan)
                    ->first();

                if ($master) {
                    DB::table('laporan')
                        ->where('id', $laporan->id)
                        ->update(['jenis_laporan_id' => $master->id]);
                }
            }
        }

        // 4. Atur kolom jenis_laporan_id menjadi NOT NULL setelah semua data terisi
        Schema::table('laporan', function (Blueprint $table) {
            $table->foreignId('jenis_laporan_id')->nullable(false)->change();
        });

        // 5. Sesuaikan Unique Constraint dan Drop kolom lama
        if (Schema::hasColumn('laporan', 'jenis_laporan')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->index('industri_id', 'temp_industri_id_index');
            });

            Schema::table('laporan', function (Blueprint $table) {
                try {
                    $table->dropUnique('unique_laporan_per_bulan');
                } catch (\Exception $e) {
                    // Abaikan jika tidak ada
                }
                
                // Hapus kolom string jenis_laporan lama
                $table->dropColumn('jenis_laporan');

                // Tambahkan unique constraint baru dengan jenis_laporan_id
                $table->unique(['industri_id', 'jenis_laporan_id', 'tanggal'], 'unique_laporan_per_bulan');
            });

            Schema::table('laporan', function (Blueprint $table) {
                try {
                    $table->dropIndex('temp_industri_id_index');
                } catch (\Exception $e) {
                    // Abaikan jika tidak ada
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('laporan', 'jenis_laporan')) {
            Schema::table('laporan', function (Blueprint $table) {
                // Tambahkan kembali kolom string jenis_laporan
                $table->string('jenis_laporan')->nullable()->after('industri_id');
            });
        }

        // Kembalikan isi data string jenis_laporan dari relasi
        if (Schema::hasColumn('laporan', 'jenis_laporan_id')) {
            $laporans = DB::table('laporan')
                ->join('master_jenis_laporan', 'laporan.jenis_laporan_id', '=', 'master_jenis_laporan.id')
                ->select('laporan.id', 'master_jenis_laporan.nama')
                ->get();

            foreach ($laporans as $laporan) {
                DB::table('laporan')
                    ->where('id', $laporan->id)
                    ->update(['jenis_laporan' => $laporan->nama]);
            }
        }

        Schema::table('laporan', function (Blueprint $table) {
            if (Schema::hasColumn('laporan', 'jenis_laporan')) {
                $table->string('jenis_laporan')->nullable(false)->change();
            }
            
            // Buat temporary index pada industri_id agar kita bisa drop index unique
            $table->index('industri_id', 'temp_industri_id_index');
        });

        Schema::table('laporan', function (Blueprint $table) {
            // Drop unique constraint baru
            try {
                $table->dropUnique('unique_laporan_per_bulan');
            } catch (\Exception $e) {
                // Abaikan jika tidak ada
            }

            // Drop foreign key & kolom jenis_laporan_id
            if (Schema::hasColumn('laporan', 'jenis_laporan_id')) {
                try {
                    $table->dropForeign(['jenis_laporan_id']);
                } catch (\Exception $e) {
                    // Abaikan jika tidak ada
                }
                $table->dropColumn('jenis_laporan_id');
            }

            // Buat kembali unique constraint lama
            if (Schema::hasColumn('laporan', 'jenis_laporan')) {
                $table->unique(['industri_id', 'jenis_laporan', 'tanggal'], 'unique_laporan_per_bulan');
            }
        });

        Schema::table('laporan', function (Blueprint $table) {
            // Drop temporary index
            try {
                $table->dropIndex('temp_industri_id_index');
            } catch (\Exception $e) {
                // Abaikan jika tidak ada
            }
        });
    }
};
