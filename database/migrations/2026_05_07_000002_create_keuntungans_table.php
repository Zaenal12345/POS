<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuntungans', function (Blueprint $table) {
            $table->id();
            $table->string('keterangan')->nullable();
            $table->bigInteger('jumlah');
            $table->enum('tipe', ['masuk', 'keluar'])->default('masuk');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuntungans');
    }
};
