<?php

namespace Modules\Barang\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use SoftDeletes;

    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'keterangan',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // ================================================================================
    // OTOMATIS GENERATE KODE BARANG: BRG-001, BRG-002, dst
    // ================================================================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barang) {
            if (empty($barang->kode_barang)) {
                $barang->kode_barang = self::generateKodeBarang();
            }
        });
    }

    public static function generateKodeBarang()
    {
        $prefix = 'BRG';
        $last = self::withTrashed()
                    ->where('kode_barang', 'like', $prefix . '%')
                    ->orderBy('id', 'desc')  // GANTI JADI INI — LEBIH AMAN!
                    ->first();

        if (!$last) {
            return $prefix . '-0001';
        }

        // Ambil angka dari kode terakhir
        $number = (int) substr($last->kode_barang, 4); // setelah "BRG-"
        $number++;

        return $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // ================================================================================
    // Scope aktif / nonaktif
    // ================================================================================
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // ================================================================================
    // Relasi ke Transaksi (opsional, buat laporan)
    // ================================================================================
    public function transaksiMasuk()
    {
        return $this->hasMany(\Modules\Transaksi\Entities\Transaksi::class)->where('jenis', 'masuk');
    }

    public function transaksiKeluar()
    {
        return $this->hasMany(\Modules\Transaksi\Entities\Transaksi::class)->where('jenis', 'keluar');
    }

    public function transaksiDetails()
    {
        return $this->hasMany(\Modules\Transaksi\Entities\TransaksiDetail::class);
    }
}