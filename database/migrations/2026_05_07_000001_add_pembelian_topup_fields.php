<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->enum('type', ['stok', 'topup'])->default('stok')->after('total_harga');
            $table->string('pelanggan')->nullable()->after('type');
            $table->string('keterangan')->nullable()->after('pelanggan');
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn(['type', 'pelanggan', 'keterangan']);
        });
    }
};