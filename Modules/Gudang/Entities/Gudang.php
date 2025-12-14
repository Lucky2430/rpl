<?php

namespace Modules\Gudang\Entities;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gudang extends Model
{
    // Kalau kamu memang mau pakai SoftDeletes, aktifkan ini + tambah di migration
    use SoftDeletes;

    protected $table = 'gudangs';

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'lokasi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // ================================================================================
    // OTOMATIS GENERATE KODE GUDANG: GDG-001, GDG-002, dst
    // ================================================================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($gudang) {
            if (empty($gudang->kode_gudang)) {
                $gudang->kode_gudang = self::generateKodeGudang();
            }
        });
    }

    public static function generateKodeGudang()
    {
        $prefix = 'GDG';
        $last = self::withTrashed() // tetap ambil yang di-softdelete juga
                    ->where('kode_gudang', 'like', $prefix . '%')
                    ->orderByRaw('CAST(SUBSTRING(kode_gudang, 5) AS UNSIGNED) DESC')
                    ->first();

        if (!$last) {
            return $prefix . '-001';
        }

        // Ambil angka dari kode terakhir
        $number = (int) substr($last->kode_gudang, 4); // setelah "GDG-"
        $number++;

        return $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // ================================================================================
    // Scope biar lebih gampang dipakai di query
    // ================================================================================
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
    public function transaksiDetails()
    {
        return $this->hasMany(\Modules\Transaksi\Entities\TransaksiDetail::class);
    }

    public function barangs()
    {
        return $this->hasMany(\Modules\Barang\Entities\Barang::class);
    }

    public function transaksi()
    {
        return $this->hasMany(\Modules\Transaksi\Entities\Transaksi::class);
    }
}