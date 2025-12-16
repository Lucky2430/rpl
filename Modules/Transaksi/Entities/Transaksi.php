<?php

namespace Modules\Transaksi\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = 'transaksis';
    protected $fillable = ['kode_transaksi', 'tanggal', 'keterangan', 'user_id'];
    protected $casts = [
        'tanggal' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($transaksi) {
            if (empty($transaksi->kode_transaksi)) {
                $last = self::orderBy('id', 'desc')->first();
                $number = $last ? $last->id + 1 : 1;
                $transaksi->kode_transaksi = 'TRX-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}