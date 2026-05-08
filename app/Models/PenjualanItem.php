<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanItem extends Model
{
    use HasFactory;

    protected $table = 'penjualan_items';

    protected $fillable = ['penjualan_id', 'produk_id', 'jumlah', 'harga_beli', 'harga_jual', 'subtotal'];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
