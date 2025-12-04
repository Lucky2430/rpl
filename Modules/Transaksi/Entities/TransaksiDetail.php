<?php

namespace Modules\Transaksi\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Barang\Entities\Barang;
use Modules\Gudang\Entities\Gudang;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_details';

    protected $fillable = [
        'transaksi_id',
        'barang_id',
        'gudang_id',
        'jumlah',
        'harga_satuan'
    ];

    public function transaksi()
    {
        return $this->belongsTo(\Modules\Transaksi\Entities\Transaksi::class);
    }

    public function barang()
    {
        return $this->belongsTo(\Modules\Barang\Entities\Barang::class);
    }

    public function gudang()
    {
        return $this->belongsTo(\Modules\Gudang\Entities\Gudang::class);
    }
}