<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = ['kategori_id', 'nama', 'slug', 'harga_beli', 'harga_jual', 'stok', 'tanpa_stok'];

    protected $casts = [
        'tanpa_stok' => 'boolean',
        'stok' => 'integer',
        'harga_beli' => 'integer',
        'harga_jual' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }
}
