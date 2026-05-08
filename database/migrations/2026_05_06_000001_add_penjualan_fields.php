<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->integer('harga_beli_custom')->nullable()->after('harga_jual_custom');
            $table->enum('tipe_pembayaran', ['tunai', 'transfer', 'qris', 'cicilan', 'piutang'])->default('tunai')->after('lunas');
            $table->integer('total_bayar')->default(0)->after('tipe_pembayaran');
            $table->string('keterangan')->nullable()->after('total_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['harga_beli_custom', 'tipe_pembayaran', 'total_bayar', 'keterangan']);
        });
    }
};