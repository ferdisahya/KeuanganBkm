<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $connection = 'mysql_logistik';
    protected $table = 'pembelian';
    protected $guarded = [];

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function permintaan()
    {
        return $this->belongsTo(PermintaanBarang::class, 'permintaan_id');
    }

    /**
     * Accessor untuk nama Divisi
     */
    public function getNamaDivisiAttribute(): string
    {
        if (!empty($this->divisi_id) && $this->divisi) {
            return $this->divisi->nama ?? $this->divisi->kode ?? '-';
        }
        if ($this->permintaan && $this->permintaan->divisi) {
            return $this->permintaan->divisi->nama ?? $this->permintaan->divisi->kode ?? '-';
        }
        if (isset($this->attributes['divisi']) && is_string($this->attributes['divisi']) && !empty($this->attributes['divisi'])) {
            return $this->attributes['divisi'];
        }
        if (isset($this->attributes['nama_divisi']) && is_string($this->attributes['nama_divisi']) && !empty($this->attributes['nama_divisi'])) {
            return $this->attributes['nama_divisi'];
        }
        return '-';
    }

    /**
     * Accessor untuk nama Lokasi
     */
    public function getNamaLokasiAttribute(): string
    {
        if (!empty($this->lokasi_id) && $this->lokasi) {
            return $this->lokasi->nama ?? $this->lokasi->kode ?? '-';
        }
        if ($this->permintaan && $this->permintaan->lokasi) {
            return $this->permintaan->lokasi->nama ?? $this->permintaan->lokasi->kode ?? '-';
        }
        if (isset($this->attributes['lokasi']) && is_string($this->attributes['lokasi']) && !empty($this->attributes['lokasi'])) {
            return $this->attributes['lokasi'];
        }
        if (isset($this->attributes['nama_lokasi']) && is_string($this->attributes['nama_lokasi']) && !empty($this->attributes['nama_lokasi'])) {
            return $this->attributes['nama_lokasi'];
        }
        return '-';
    }
}
