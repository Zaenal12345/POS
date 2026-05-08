<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Buat tabel baru (kosong, baru)
        Schema::create('penjualans_new', function (Blueprint $table) {
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

        // Step 2: Insert data lama ke penjualans_new
        DB::statement("INSERT INTO penjualans_new (id, pelanggan_nama, total_harga, total_bayar, kembalian, tipe_pembayaran, lunas, status, keterangan, created_at, updated_at)
SELECT id, COALESCE(pelanggan, '-'), total_harga, COALESCE(total_bayar, total_harga), GREATEST(0, COALESCE(total_bayar, total_harga) - total_harga), COALESCE(tipe_pembayaran, 'tunai'), COALESCE(lunas, 1), COALESCE(status, 'completed'), keterangan, created_at, updated_at FROM penjualans");

        // Step 3: Buat tabel items dan insert data lama
        Schema::create('penjualan_items_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penjualan_id');
            $table->unsignedBigInteger('produk_id');
            $table->integer('jumlah')->default(1);
            $table->integer('harga_beli')->default(0);
            $table->integer('harga_jual')->default(0);
            $table->integer('subtotal')->default(0);
            $table->timestamps();
            $table->foreign('penjualan_id')->references('id')->on('penjualans_new')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');
        });

        DB::statement("INSERT INTO penjualan_items_new (penjualan_id, produk_id, jumlah, harga_beli, harga_jual, subtotal, created_at, updated_at)
SELECT p.id, p.produk_id, p.jumlah, COALESCE(p.harga_beli_custom, pr.harga_beli), COALESCE(p.harga_jual_custom, pr.harga_jual), p.total_harga, p.created_at, p.updated_at
FROM penjualans p
JOIN produks pr ON pr.id = p.produk_id");

        // Step 4: Drop old tables/structures
        Schema::dropIfExists('penjualan_items');
        Schema::dropIfExists('penjualans');

        // Step 5: Rename new tables to final names
        Schema::rename('penjualans_new', 'penjualans');
        Schema::rename('penjualan_items_new', 'penjualan_items');
    }

    public function down(): void
    {
        // Restore: create old structure, insert data
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('pelanggan')->nullable();
            $table->integer('total_harga');
            $table->integer('harga_jual_custom')->nullable();
            $table->integer('harga_beli_custom')->nullable();
            $table->string('status')->default('completed');
            $table->boolean('lunas')->default(true);
            $table->enum('tipe_pembayaran', ['tunai', 'transfer', 'qris', 'cicilan', 'piutang'])->default('tunai');
            $table->integer('total_bayar')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        DB::statement("INSERT INTO penjualans (id, produk_id, jumlah, pelanggan, total_harga, harga_jual_custom, harga_beli_custom, status, lunas, tipe_pembayaran, total_bayar, keterangan, created_at, updated_at)
SELECT p.id, i.produk_id, i.jumlah, p.pelanggan_nama, p.total_harga, i.harga_jual, i.harga_beli, p.status, p.lunas, p.tipe_pembayaran, p.total_bayar, p.keterangan, p.created_at, p.updated_at
FROM penjualans p
JOIN penjualan_items i ON i.penjualan_id = p.id");

        Schema::dropIfExists('penjualan_items');
    }
};
