<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Abstract Base Model untuk semua jenis industri
 * Menggunakan Table Per Type (TPT) Inheritance Pattern
 * 
 * @property int $id Primary Key dari tabel industries
 * @property string $nama Nama perusahaan/industri
 * @property string $alamat Alamat lengkap
 * @property string $penanggungjawab Nama pimpinan/direktur
 * @property string $kabupaten Kabupaten/Kota lokasi
 * @property string $kontak Nomor telepon/email
 * @property string $nomor_izin Nomor izin/NIB/SS
 * @property string $type Jenis industri (primer, sekunder, tpt_kb, end_user)
 */
class Industri extends Model
{
    /**
     * Tabel database untuk base model
     */
    protected $table = 'industries';

    /**
     * Common fillable attributes untuk semua jenis industri
     */
    protected $fillable = [
        'nama',
        'alamat',
        'penanggungjawab',
        'kabupaten',
        'kontak',
        'nomor_izin',
        'type',
        'status',
    ];

    /**
     * Get jenis industri (bisa di-override oleh child class)
     */
    public function getJenisIndustri(): string
    {
        return $this->type ?? 'unknown';
    }

    /**
     * Scope untuk filter berdasarkan kabupaten
     */
    public function scopeByKabupaten($query, $kabupaten)
    {
        return $query->where('kabupaten', $kabupaten);
    }

    /**
     * Scope untuk filter berdasarkan nama
     */
    public function scopeByNama($query, $nama)
    {
        return $query->where('nama', 'like', '%' . $nama . '%');
    }

    /**
     * Relationship ke IndustriPrimer (one-to-one)
     */
    public function industriPrimer()
    {
        return $this->hasOne(IndustriPrimer::class, 'industri_id');
    }

    /**
     * Relationship ke IndustriSekunder (one-to-one)
     */
    public function industriSekunder()
    {
        return $this->hasOne(IndustriSekunder::class, 'industri_id');
    }

    /**
     * Relationship ke TptKb (one-to-one)
     */
    public function tptKb()
    {
        return $this->hasOne(TptKb::class, 'industri_id');
    }

    /**
     * Relationship ke EndUser (one-to-one)
     */
    public function endUser()
    {
        return $this->hasOne(EndUser::class, 'industri_id');
    }

    /**
     * Relationship ke Laporan (one-to-many)
     */

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }
}
