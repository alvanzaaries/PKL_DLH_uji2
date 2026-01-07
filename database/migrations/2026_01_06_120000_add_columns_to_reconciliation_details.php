<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reconciliation_details', function (Blueprint $table) {
            $table->string('no_urut')->nullable()->after('wilayah');
            // C: lhp_no
            $table->string('lhp_no')->nullable()->after('jenis_sdh');
            // D: lhp_tanggal
            $table->string('lhp_tanggal')->nullable()->after('lhp_no');
            // F: volume is actually needed here for record keeping, even if normalized in Fact
            $table->decimal('volume', 20, 2)->nullable()->after('jenis_sdh'); 
            // G: lhp_nilai
            $table->decimal('lhp_nilai', 20, 2)->nullable()->after('volume');
            
            // H: billing_no
            $table->string('billing_no')->nullable()->after('lhp_nilai');
            // I: billing_tanggal
            $table->string('billing_tanggal')->nullable()->after('billing_no');
            // J: billing_nilai
            $table->decimal('billing_nilai', 20, 2)->nullable()->after('billing_tanggal');
            
            // K: setor_tanggal
            $table->string('setor_tanggal')->nullable()->after('billing_nilai');
            // L: setor_bank
            $table->string('setor_bank')->nullable()->after('setor_tanggal');
            // M: setor_ntpn
            $table->string('setor_ntpn')->nullable()->after('setor_bank');
            // N: setor_ntb
            $table->string('setor_ntb')->nullable()->after('setor_ntpn');
            // O: setor_nilai
            $table->decimal('setor_nilai', 20, 2)->nullable()->after('setor_ntb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reconciliation_details', function (Blueprint $table) {
            $table->dropColumn([
                'no_urut',
                'lhp_no',
                'lhp_tanggal',
                'volume',
                'lhp_nilai',
                'billing_no',
                'billing_tanggal',
                'billing_nilai',
                'setor_tanggal',
                'setor_bank',
                'setor_ntpn',
                'setor_ntb',
                'setor_nilai'
            ]);
        });
    }
};
