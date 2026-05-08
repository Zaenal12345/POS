<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->boolean('lunas')->default(true)->after('status');
            $table->string('pelanggan')->nullable()->after('jumlah');
            $table->integer('harga_jual_custom')->nullable()->after('total_harga');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['lunas', 'pelanggan', 'harga_jual_custom']);
        });
    }
};