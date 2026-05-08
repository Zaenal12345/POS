<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = ['produk_id', 'jumlah', 'total_harga', 'status', 'type', 'pelanggan', 'keterangan'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}