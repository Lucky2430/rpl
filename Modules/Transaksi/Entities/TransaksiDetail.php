<?php

namespace Modules\Transaksi\Entities;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_details';
    protected $fillable = ['transaksi_id', 'barang_id', 'jumlah'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function barang()
    {
        return $this->belongsTo(\Modules\Barang\Entities\Barang::class)->with('gudang');
    }
}