<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelanggan_id', 'pelanggan_nama',
        'total_harga', 'total_bayar', 'kembalian',
        'tipe_pembayaran', 'lunas', 'status', 'keterangan',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PenjualanItem::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
