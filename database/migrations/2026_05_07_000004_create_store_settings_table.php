<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        \Illuminate\Support\Facades\DB::table('store_settings')->insert([
            ['key' => 'nama_toko', 'value' => 'Toko Kasir', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'alamat', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'no_telp', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_nota', 'value' => 'Terima kasih atas kunjungan Anda!', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
