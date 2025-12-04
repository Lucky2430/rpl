<?php

namespace Modules\Transaksi\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaksi extends Model
{
    protected $table = 'transaksis';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'tanggal',
        'jenis',
        'keterangan'
    ];

    protected $dates = ['tanggal'];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke detail (1 transaksi → banyak barang)
    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    // Kode otomatis TRX-2025-0001 otomatis
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($transaksi) {
            if (empty($transaksi->kode_transaksi)) {
                $year = date('Y');
                $last = self::whereYear('created_at', $year)->max('id') ?? 0;
                $transaksi->kode_transaksi = "TRX-{$year}-" . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}