<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    // public function up()
    // {
    //     Schema::create('transaksis', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('kode_transaksi')->unique();
    //         $table->enum('jenis', ['masuk', 'keluar']);
    //         $table->integer('jumlah')->unsigned();
    //         $table->date('tanggal');
    //         $table->text('keterangan')->nullable();

    //         // KOLOM FK — PAKAI TIPE YANG PASTI COCOK
    //         $table->unsignedBigInteger('barang_id');
    //         $table->unsignedBigInteger('gudang_id');
    //         $table->unsignedBigInteger('user_id');

    //         $table->timestamps();

    //         // FK DITULIS MANUAL → INI YANG SELALU JALAN!
    //         $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
    //         $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('cascade');
    //         $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

    //         $table->softdeletes();
    //     });
    // }

    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('user_id');

            $table->timestamps();
            $table->softDeletes();

            // FOREIGN KEY
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // INDEX UNTUK PERFORMANSI
            $table->index('kode_transaksi');
            $table->index('jenis');
            $table->index('tanggal');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
};