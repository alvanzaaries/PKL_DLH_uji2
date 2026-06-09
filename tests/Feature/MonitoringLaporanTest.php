




<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Industri;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringLaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_page_requires_authentication()
    {
        $response = $this->get('/laporan/monitoring');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_monitoring_page()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/laporan/monitoring');
        $response->assertStatus(200);
    }

    public function test_admin_can_filter_by_jenis_industri()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create a primer industry
        $primer = Industri::create([
            'nama' => 'PT Primer Wood',
            'type' => 'primer',
            'status' => 'Aktif',
            'alamat' => 'Alamat Primer',
            'penanggungjawab' => 'Direktur Primer',
            'kontak' => '08123456789',
            'kabupaten' => 'Semarang',
            'nomor_izin' => '123-PRIMER',
        ]);

        // Create a sekunder industry
        $sekunder = Industri::create([
            'nama' => 'PT Sekunder Wood',
            'type' => 'sekunder',
            'status' => 'Aktif',
            'alamat' => 'Alamat Sekunder',
            'penanggungjawab' => 'Direktur Sekunder',
            'kontak' => '08123456789',
            'kabupaten' => 'Semarang',
            'nomor_izin' => '456-SEKUNDER',
        ]);

        // Access monitoring with filter: jenis_industri = primer
        $response = $this->actingAs($admin)->get('/laporan/monitoring?jenis_industri=primer');
        $response->assertStatus(200);
        
        $companies = $response->viewData('companies');
        $this->assertCount(1, $companies);
        $this->assertEquals('PT Primer Wood', $companies->first()->nama);
    }


    public function test_admin_can_filter_by_status_keaktifan()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create active industry
        $activeInd = Industri::create([
            'nama' => 'PT Wood Active',
            'type' => 'primer',
            'status' => 'Aktif',
            'alamat' => 'Alamat A',
            'penanggungjawab' => 'Direktur A',
            'kontak' => '08123456789',
            'kabupaten' => 'Semarang',
            'nomor_izin' => 'IZIN-AAA-999',
        ]);

        // Create inactive industry
        $inactiveInd = Industri::create([
            'nama' => 'PT Wood Inactive',
            'type' => 'primer',
            'status' => 'Tidak Aktif',
            'alamat' => 'Alamat B',
            'penanggungjawab' => 'Direktur B',
            'kontak' => '08123456789',
            'kabupaten' => 'Semarang',
            'nomor_izin' => 'IZIN-BBB-888',
        ]);

        // Test filter: status_industri = aktif
        $response = $this->actingAs($admin)->get('/laporan/monitoring?status_industri=aktif');
        $response->assertStatus(200);
        $companies = $response->viewData('companies');
        $this->assertCount(1, $companies);
        $this->assertEquals('PT Wood Active', $companies->first()->nama);

        // Test filter: status_industri = tidak_aktif
        $response = $this->actingAs($admin)->get('/laporan/monitoring?status_industri=tidak_aktif');
        $response->assertStatus(200);
        $companies = $response->viewData('companies');
        $this->assertCount(1, $companies);
        $this->assertEquals('PT Wood Inactive', $companies->first()->nama);

        // Test filter: status_industri = semua
        $response = $this->actingAs($admin)->get('/laporan/monitoring?status_industri=semua');
        $response->assertStatus(200);
        $companies = $response->viewData('companies');
        $this->assertCount(2, $companies);
    }
}
