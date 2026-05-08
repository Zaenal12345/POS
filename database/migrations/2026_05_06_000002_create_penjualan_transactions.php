<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('penjualans', 'penjualans_header');

        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('pelanggan_nama')->nullable();
            $table->integer('total_harga')->default(0);
            $table->integer('total_bayar')->default(0);
            $table->integer('kembalian')->default(0);
            $table->enum('tipe_pembayaran', ['tunai', 'transfer', 'qris', 'cicilan', 'piutang'])->default('tunai');
            $table->boolean('lunas')->default(true);
            $table->string('status')->default('completed');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('penjualan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->integer('harga_beli')->default(0);
            $table->integer('harga_jual')->default(0);
            $table->integer('subtotal')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_items');
        Schema::dropIfExists('penjualans');
        Schema::rename('penjualans_header', 'penjualans');
    }
};
