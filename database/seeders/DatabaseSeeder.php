<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // CMS Users
        User::factory()->create([
            'name' => 'Admin CMS',
            'email' => 'admin@cms.test',
        ]);
        User::factory()->create([
            'name' => 'Manager Gudang',
            'email' => 'manager@cms.test',
        ]);
        User::factory()->create([
            'name' => 'Kasir CMS',
            'email' => 'kasir@cms.test',
        ]);

        // Kategori
        $kategoris = [
            ['nama' => 'Sepatu', 'slug' => 'sepatu'],
            ['nama' => 'Tas', 'slug' => 'tas'],
            ['nama' => 'Pakaian', 'slug' => 'pakaian'],
            ['nama' => 'Aksesoris', 'slug' => 'aksesoris'],
            ['nama' => 'Elektronik', 'slug' => 'elektronik'],
        ];
        foreach ($kategoris as $k) {
            Kategori::create($k);
        }

        // Produk
        $produks = [
            ['kategori_id' => 1, 'nama' => 'Sepatu Sneakers Premium', 'slug' => 'sepatu-sneakers-premium', 'harga_beli' => 250000, 'harga_jual' => 380000, 'stok' => 45, 'tanpa_stok' => false],
            ['kategori_id' => 1, 'nama' => 'Sepatu Running Pro', 'slug' => 'sepatu-running-pro', 'harga_beli' => 320000, 'harga_jual' => 480000, 'stok' => 28, 'tanpa_stok' => false],
            ['kategori_id' => 2, 'nama' => 'Tas Ransel Urban', 'slug' => 'tas-ransel-urban', 'harga_beli' => 120000, 'harga_jual' => 195000, 'stok' => 62, 'tanpa_stok' => false],
            ['kategori_id' => 2, 'nama' => 'Tote Bag Canvas', 'slug' => 'tote-bag-canvas', 'harga_beli' => 55000, 'harga_jual' => 95000, 'stok' => 88, 'tanpa_stok' => false],
            ['kategori_id' => 3, 'nama' => 'Kaos Polos Cotton', 'slug' => 'kaos-polos-cotton', 'harga_beli' => 45000, 'harga_jual' => 85000, 'stok' => 200, 'tanpa_stok' => false],
            ['kategori_id' => 3, 'nama' => 'Hoodie Polos', 'slug' => 'hoodie-polos', 'harga_beli' => 110000, 'harga_jual' => 185000, 'stok' => 55, 'tanpa_stok' => false],
            ['kategori_id' => 3, 'nama' => 'Celana Jeans Slim', 'slug' => 'celana-jeans-slim', 'harga_beli' => 180000, 'harga_jual' => 275000, 'stok' => 42, 'tanpa_stok' => false],
            ['kategori_id' => 3, 'nama' => 'Kemeja Flanel', 'slug' => 'kemeja-flanel', 'harga_beli' => 140000, 'harga_jual' => 225000, 'stok' => 35, 'tanpa_stok' => false],
            ['kategori_id' => 4, 'nama' => 'Topi Baseball', 'slug' => 'topi-baseball', 'harga_beli' => 35000, 'harga_jual' => 75000, 'stok' => 120, 'tanpa_stok' => false],
            ['kategori_id' => 4, 'nama' => 'Jam Tangan Minimalis', 'slug' => 'jam-tangan-minimalis', 'harga_beli' => 280000, 'harga_jual' => 450000, 'stok' => 18, 'tanpa_stok' => false],
            ['kategori_id' => 5, 'nama' => 'Earphone Wireless', 'slug' => 'earphone-wireless', 'harga_beli' => 150000, 'harga_jual' => 280000, 'stok' => 8, 'tanpa_stok' => true],
            ['kategori_id' => 3, 'nama' => 'Jaket Kulit', 'slug' => 'jaket-kulit', 'harga_beli' => 450000, 'harga_jual' => 680000, 'stok' => 12, 'tanpa_stok' => false],
        ];
        foreach ($produks as $p) {
            Produk::create($p);
        }

        // Pembelian (stok masuk) - 7 hari terakhir
        $pembelianData = [
            [1, 20, now()->subDays(6)],
            [2, 15, now()->subDays(5)],
            [3, 30, now()->subDays(5)],
            [5, 50, now()->subDays(4)],
            [6, 25, now()->subDays(4)],
            [1, 10, now()->subDays(3)],
            [4, 40, now()->subDays(3)],
            [7, 18, now()->subDays(2)],
            [8, 22, now()->subDays(2)],
            [9, 60, now()->subDays(1)],
            [3, 15, now()->subDays(1)],
            [10, 5, now()],
        ];
        foreach ($pembelianData as [$pid, $jml, $tgl]) {
            $produk = Produk::find($pid);
            Pembelian::create([
                'produk_id' => $pid,
                'jumlah' => $jml,
                'total_harga' => $produk->harga_beli * $jml,
                'status' => 'completed',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);
            $produk->increment('stok', $jml);
        }

        // Penjualan - 7 hari terakhir
        $penjualanData = [
            [1, 5, now()->subDays(6)],
            [5, 12, now()->subDays(6)],
            [2, 3, now()->subDays(5)],
            [6, 7, now()->subDays(5)],
            [3, 8, now()->subDays(4)],
            [9, 15, now()->subDays(4)],
            [1, 6, now()->subDays(3)],
            [7, 4, now()->subDays(3)],
            [5, 18, now()->subDays(2)],
            [4, 10, now()->subDays(2)],
            [6, 5, now()->subDays(1)],
            [8, 3, now()->subDays(1)],
            [1, 4, now()],
            [3, 7, now()],
            [9, 20, now()],
            [5, 25, now()],
            [10, 2, now()],
        ];
        foreach ($penjualanData as [$pid, $jml, $tgl]) {
            $produk = Produk::find($pid);
            if (!$produk->tanpa_stok && $produk->stok < $jml) continue;
            Penjualan::create([
                'produk_id' => $pid,
                'jumlah' => $jml,
                'total_harga' => $produk->harga_jual * $jml,
                'status' => 'completed',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);
            if (!$produk->tanpa_stok) $produk->decrement('stok', $jml);
        }
    }
}