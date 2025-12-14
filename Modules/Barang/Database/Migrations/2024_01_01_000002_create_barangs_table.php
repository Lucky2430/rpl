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
            
            // LANGSUNG TERKONEKSI KE GUDANG!
            $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('cascade');
            
            $table->integer('stok')->unsigned()->default(0);
            $table->decimal('harga', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('kode_barang');
            $table->index('gudang_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('barangs');
    }
}