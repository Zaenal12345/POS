<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    // =====================
    // Web Views
    // =====================

    public function index(Request $request)
    {
        $query = Penjualan::with('items.produk.kategori');

        // Filter tanggal
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->where('pelanggan_nama', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->lunas !== null && $request->lunas !== '') {
            $query->where('lunas', $request->boolean('lunas'));
        }
        $penjualans = $query->latest()->paginate(10);
        $produks = Produk::with('kategori')->get();
        return view('penjualan.index', compact('penjualans', 'produks'));
    }

    public function data(Request $request)
    {
        $query = Penjualan::with('items.produk.kategori');

        // Filter tanggal
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->where('pelanggan_nama', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->lunas !== null && $request->lunas !== '') {
            $query->where('lunas', $request->boolean('lunas'));
        }
        $penjualans = $query->latest()->paginate($request->per_page ?? 10);
        return response()->json($penjualans);
    }

    public function store(Request $request)
    {
        $items = $request->input('items', []);
        if (empty($items)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Pilihminimal satu produk'], 422);
            }
            return back()->with('error', 'Pilih minimal satu produk');
        }

        try {
            $penjualan = DB::transaction(function () use ($request, $items) {
                $totalHarga = 0;
                $itemRecords = [];

                // First pass: validate stock and calculate totals
                foreach ($items as $item) {
                    $produk = Produk::find($item['produk_id']);
                    if (!$produk) continue;
                    $jumlah = (int) ($item['jumlah'] ?? 1);
                    $hargaJual = (int) ($item['harga_jual'] ?? $produk->harga_jual);
                    $hargaBeli = (int) ($item['harga_beli'] ?? $produk->harga_beli);
                    $subtotal = $hargaJual * $jumlah;
                    $totalHarga += $subtotal;
                    $itemRecords[] = [
                        'produk_id' => $produk->id,
                        'jumlah' => $jumlah,
                        'harga_beli' => $hargaBeli,
                        'harga_jual' => $hargaJual,
                        'subtotal' => $subtotal,
                    ];
                    if (!$produk->tanpa_stok) {
                        $produk->decrement('stok', $jumlah);
                    }
                }

                $totalBayar = (int) ($request->has('total_bayar') ? $request->total_bayar : 0);
                $kembalian = max(0, $totalBayar - $totalHarga);

                $penjualan = Penjualan::create([
                    'pelanggan_nama' => $request->pelanggan_nama,
                    'total_harga' => $totalHarga,
                    'total_bayar' => $totalBayar,
                    'kembalian' => $kembalian,
                    'tipe_pembayaran' => $request->tipe_pembayaran ?? 'tunai',
                    'lunas' => $request->boolean('lunas'),
                    'status' => 'completed',
                    'keterangan' => $request->keterangan,
                ]);

                foreach ($itemRecords as $rec) {
                    $penjualan->items()->create($rec);
                }

                return $penjualan;
            });

            $penjualan->load('items.produk.kategori');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Penjualan berhasil disimpan',
                    'data' => $penjualan,
                ], 201);
            }
            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menyimpan penjualan');
        }
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $validated = $request->validate([
            'lunas' => 'nullable|boolean',
            'tipe_pembayaran' => 'nullable|in:tunai,transfer,qris,cicilan,piutang',
            'total_bayar' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string|max:500',
            'status' => 'nullable|in:pending,completed,cancelled',
        ]);

        $update = array_filter([
            'lunas' => isset($validated['lunas']) ? $request->boolean('lunas') : null,
            'tipe_pembayaran' => $validated['tipe_pembayaran'] ?? null,
            'total_bayar' => $validated['total_bayar'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => $validated['status'] ?? null,
        ], fn($v) => $v !== null);

        if (isset($update['total_bayar'])) {
            $update['kembalian'] = max(0, $update['total_bayar'] - $penjualan->total_harga);
            if (!isset($validated['lunas'])) {
                $update['lunas'] = $update['total_bayar'] >= $penjualan->total_harga;
            }
        }

        $penjualan->update($update);
        $penjualan->load('items.produk.kategori');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Penjualan berhasil diupdate', 'data' => $penjualan]);
        }
        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diupdate');
    }

    public function destroy(Request $request, Penjualan $penjualan)
    {
        DB::transaction(function () use ($penjualan) {
            foreach ($penjualan->items as $item) {
                $produk = $item->produk;
                if ($produk && !$produk->tanpa_stok) {
                    $produk->increment('stok', $item->jumlah);
                }
            }
            $penjualan->items()->delete();
            $penjualan->delete();
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Penjualan berhasil dihapus']);
        }
        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus');
    }

    // =====================
    // API Methods
    // =====================

    public function apiIndex(Request $request): JsonResponse
    {
        $query = Penjualan::with('items.produk.kategori');
        if ($request->search) {
            $query->where('pelanggan_nama', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->lunas !== null && $request->lunas !== '') {
            $query->where('lunas', $request->boolean('lunas'));
        }
        $perPage = (int) ($request->per_page ?? 20);
        $penjualans = $query->latest()->paginate($perPage);

        $data = $penjualans->map(fn($p) => [
            'id' => $p->id,
            'pelanggan_nama' => $p->pelanggan_nama,
            'items' => $p->items->map(fn($i) => [
                'produk_nama' => $i->produk?->nama,
                'kategori' => $i->produk?->kategori?->nama,
                'jumlah' => $i->jumlah,
                'harga_beli' => $i->harga_beli,
                'harga_jual' => $i->harga_jual,
                'subtotal' => $i->subtotal,
            ]),
            'total_harga' => $p->total_harga,
            'total_bayar' => $p->total_bayar,
            'kembalian' => $p->kembalian,
            'tipe_pembayaran' => $p->tipe_pembayaran,
            'lunas' => (bool) $p->lunas,
            'status' => $p->status,
            'keterangan' => $p->keterangan,
            'tanggal' => $p->created_at->format('Y-m-d H:i'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $penjualans->currentPage(),
                'last_page' => $penjualans->lastPage(),
                'per_page' => $penjualans->perPage(),
                'total' => $penjualans->total(),
            ],
        ]);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'Pilih minimal satu produk'], 422);
        }

        try {
            $penjualan = DB::transaction(function () use ($request, $items) {
                $totalHarga = 0;
                $itemRecords = [];

                foreach ($items as $item) {
                    $produk = Produk::find($item['produk_id']);
                    if (!$produk) continue;
                    $jumlah = (int) ($item['jumlah'] ?? 1);
                    $hargaJual = (int) ($item['harga_jual'] ?? $produk->harga_jual);
                    $hargaBeli = (int) ($item['harga_beli'] ?? $produk->harga_beli);
                    $subtotal = $hargaJual * $jumlah;
                    $totalHarga += $subtotal;
                    $itemRecords[] = [
                        'produk_id' => $produk->id,
                        'jumlah' => $jumlah,
                        'harga_beli' => $hargaBeli,
                        'harga_jual' => $hargaJual,
                        'subtotal' => $subtotal,
                    ];
                    if (!$produk->tanpa_stok) {
                        $produk->decrement('stok', $jumlah);
                    }
                }

                $totalBayar = (int) ($request->has('total_bayar') ? $request->total_bayar : 0);
                $kembalian = max(0, $totalBayar - $totalHarga);

                $penjualan = Penjualan::create([
                    'pelanggan_nama' => $request->pelanggan_nama,
                    'total_harga' => $totalHarga,
                    'total_bayar' => $totalBayar,
                    'kembalian' => $kembalian,
                    'tipe_pembayaran' => $request->tipe_pembayaran ?? 'tunai',
                    'lunas' => $request->boolean('lunas'),
                    'status' => 'completed',
                    'keterangan' => $request->keterangan,
                ]);

                foreach ($itemRecords as $rec) {
                    $penjualan->items()->create($rec);
                }

                return $penjualan;
            });

            $penjualan->load('items.produk.kategori');

            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil disimpan',
                'data' => [
                    'id' => $penjualan->id,
                    'total_harga' => $penjualan->total_harga,
                    'total_bayar' => $penjualan->total_bayar,
                    'kembalian' => $penjualan->kembalian,
                    'items_count' => $penjualan->items->count(),
                    'tanggal' => $penjualan->created_at->format('Y-m-d H:i'),
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function apiUpdate(Request $request, int $id): JsonResponse
    {
        $penjualan = Penjualan::find($id);
        if (!$penjualan) {
            return response()->json(['success' => false, 'message' => 'Penjualan tidak ditemukan'], 404);
        }
        $data = $request->validate([
            'lunas' => 'nullable|boolean',
            'tipe_pembayaran' => 'nullable|in:tunai,transfer,qris,cicilan,piutang',
            'total_bayar' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string|max:500',
            'status' => 'nullable|in:pending,completed,cancelled',
        ]);
        $update = array_filter($data, fn($v) => $v !== null);
        if (isset($update['total_bayar'])) {
            $update['kembalian'] = max(0, $update['total_bayar'] - $penjualan->total_harga);
            $update['lunas'] = $update['total_bayar'] >= $penjualan->total_harga;
        }
        $penjualan->update($update);
        return response()->json(['success' => true, 'message' => 'Data berhasil diupdate', 'data' => $penjualan]);
    }

    public function apiDestroy(int $id): JsonResponse
    {
        $penjualan = Penjualan::find($id);
        if (!$penjualan) {
            return response()->json(['success' => false, 'message' => 'Penjualan tidak ditemukan'], 404);
        }
        DB::transaction(function () use ($penjualan) {
            foreach ($penjualan->items as $item) {
                $produk = $item->produk;
                if ($produk && !$produk->tanpa_stok) {
                    $produk->increment('stok', $item->jumlah);
                }
            }
            $penjualan->items()->delete();
            $penjualan->delete();
        });
        return response()->json(['success' => true, 'message' => 'Penjualan berhasil dihapus']);
    }
}
