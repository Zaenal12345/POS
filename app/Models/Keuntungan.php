<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuntungan extends Model
{
    protected $fillable = ['keterangan', 'jumlah', 'tipe', 'tanggal'];
    protected $casts = ['jumlah' => 'integer', 'tanggal' => 'date'];
}
