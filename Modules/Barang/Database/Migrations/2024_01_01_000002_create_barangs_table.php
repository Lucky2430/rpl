<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangsTable extends Migration
{
    public function up()
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            
            // KOLOM YANG HARUS ADA UNTUK SISTEM GUDANG
            $table->integer('stok')->unsigned()->default(0);        // STOK SAAT INI
            $table->decimal('harga_beli', 15, 2)->default(0);       // Optional: harga beli
            $table->decimal('harga_jual', 15, 2)->default(0);       // Optional: harga jual
            $table->string('satuan')->default('pcs');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa
            $table->index('kode_barang');
            $table->index('nama_barang');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('barangs');
    }
}