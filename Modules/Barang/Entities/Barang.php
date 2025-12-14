<?php

namespace Modules\Barang\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Barang extends Model
{
    use SoftDeletes;

    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'gudang_id',
        'stok',
        'harga',
        'keterangan',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stok'      => 'integer',
        'harga'     => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barang) {
            if (empty($barang->kode_barang)) {
                $last = self::withTrashed()->orderBy('id', 'desc')->first();
                $number = $last ? $last->id + 1 : 1;
                $barang->kode_barang = 'BRG-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // RELASI KE GUDANG
    public function gudang()
    {
        return $this->belongsTo(\Modules\Gudang\Entities\Gudang::class);
    }
}