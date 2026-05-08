<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    // =====================
    // Web Views
    // =====================

    public function index(Request $request)
    {
        $query = Pembelian::with('produk.kategori');

        // Filter tanggal
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->whereHas('produk', fn($q) => $q->where('nama', 'like', '%' . $request->search . '%'))
                  ->orWhere('pelanggan', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        $pembelians = $query->latest()->paginate(10);
        $produks = Produk::with('kategori')->get();
        return view('pembelian.index', compact('pembelians', 'produks'));
    }

    public function data(Request $request)
    {
        $query = Pembelian::with('produk.kategori');

        // Filter tanggal
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->whereHas('produk', fn($q) => $q->where('nama', 'like', '%' . $request->search . '%'))
                  ->orWhere('pelanggan', 'like', '%' . $request->search . '%');
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->type) $query->where('type', $request->type);
        $pembelians = $query->latest()->paginate($request->per_page ?? 10);
        return response()->json($pembelians);
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'stok');

        $rules = [
            'jumlah' => 'required|integer|min:1',
            'type' => 'nullable|in:stok,topup',
        ];

        if ($type === 'stok') {
            $rules['produk_id'] = 'required|exists:produks,id';
        } else {
            $rules['produk_id'] = 'nullable';
        }

        $request->validate($rules);

        $produk = null;
        $total = 0;

        if ($type === 'stok') {
            $produk = Produk::find($request->produk_id);
            $total = $produk->harga_beli * $request->jumlah;
        } else {
            $total = $request->jumlah;
        }

        $pembelian = Pembelian::create([
            'produk_id' => $type === 'stok' ? $request->produk_id : null,
            'jumlah' => $request->jumlah,
            'total_harga' => $total,
            'status' => 'completed',
            'type' => $type,
            'pelanggan' => $request->pelanggan,
            'keterangan' => $request->keterangan,
        ]);

        if ($type === 'stok' && $produk) {
            $produk->increment('stok', $request->jumlah);
        }

        $pembelian->load('produk.kategori');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $type === 'topup' ? 'Top up berhasil dicatat' : 'Pembelian berhasil dicatat',
                'data' => $pembelian
            ], 201);
        }
        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dicatat');
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $request->validate(['status' => 'required|in:pending,completed,cancelled']);
        $pembelian->update(['status' => $request->status]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Status pembelian berhasil diupdate', 'data' => $pembelian]);
        }
        return redirect()->route('pembelian.index')->with('success', 'Status pembelian berhasil diupdate');
    }

    public function destroy(Request $request, Pembelian $pembelian)
    {
        $produk = $pembelian->produk;
        // Hanya kembalikan stok untuk type 'stok'
        if ($produk && $pembelian->type === 'stok') {
            $produk->decrement('stok', $pembelian->jumlah);
        }
        $pembelian->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Pembelian berhasil dihapus']);
        }
        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus');
    }

    // =====================
    // API Methods
    // =====================

    public function apiIndex(Request $request): JsonResponse
    {
        $query = Pembelian::with('produk.kategori');
        if ($request->search) {
            $query->whereHas('produk', fn($q) => $q->where('nama', 'like', '%' . $request->search . '%'))
                  ->orWhere('pelanggan', 'like', '%' . $request->search . '%');
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->produk_id) $query->where('produk_id', $request->produk_id);
        $pembelians = $query->latest()->paginate($request->per_page ?? 20);
        $data = collect($pembelians->items())->map(fn($p) => [
            'id' => $p->id,
            'produk_id' => $p->produk_id,
            'produk_nama' => $p->produk?->nama,
            'produk_kategori' => $p->produk?->kategori?->nama,
            'jumlah' => $p->jumlah,
            'harga_satuan' => $p->produk?->harga_beli,
            'total_harga' => $p->total_harga,
            'status' => $p->status,
            'type' => $p->type,
            'pelanggan' => $p->pelanggan,
            'keterangan' => $p->keterangan,
            'tanggal' => $p->created_at->format('Y-m-d H:i'),
        ]);
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $pembelians->currentPage(),
                'last_page' => $pembelians->lastPage(),
                'per_page' => $pembelians->perPage(),
                'total' => $pembelians->total(),
            ],
        ]);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $type = $request->input('type', 'stok');

        $rules = [
            'jumlah' => 'required|integer|min:1',
            'type' => 'nullable|in:stok,topup',
            'pelanggan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:500',
        ];

        if ($type === 'stok') {
            $rules['produk_id'] = 'required|exists:produks,id';
        } else {
            $rules['produk_id'] = 'nullable';
        }

        $data = $request->validate($rules);

        $produk = null;
        $total = 0;

        if ($type === 'stok') {
            $produk = Produk::find($request->produk_id);
            if (!$produk) return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
            $total = $produk->harga_beli * $request->jumlah;
        } else {
            $total = $request->jumlah;
        }

        $pembelian = Pembelian::create([
            'produk_id' => $type === 'stok' ? $data['produk_id'] : null,
            'jumlah' => $data['jumlah'],
            'total_harga' => $total,
            'status' => 'completed',
            'type' => $type,
            'pelanggan' => $data['pelanggan'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        if ($type === 'stok' && $produk) {
            $produk->increment('stok', $data['jumlah']);
        }

        $pembelian->load('produk.kategori');

        $responseData = [
            'id' => $pembelian->id,
            'type' => $pembelian->type,
            'jumlah' => $pembelian->jumlah,
            'total_harga' => $pembelian->total_harga,
            'pelanggan' => $pembelian->pelanggan,
            'keterangan' => $pembelian->keterangan,
            'tanggal' => $pembelian->created_at->format('Y-m-d H:i'),
        ];

        if ($type === 'stok' && $produk) {
            $responseData['produk'] = $pembelian->produk->nama;
            $responseData['stok_baru'] = $produk->stok;
        }

        return response()->json([
            'success' => true,
            'message' => $type === 'topup' ? 'Top up berhasil dicatat' : 'Pembelian berhasil dicatat',
            'data' => $responseData,
        ], 201);
    }

    public function apiUpdate(Request $request, int $id): JsonResponse
    {
        $pembelian = Pembelian::find($id);
        if (!$pembelian) return response()->json(['success' => false, 'message' => 'Pembelian tidak ditemukan'], 404);
        $data = $request->validate(['status' => 'required|in:pending,completed,cancelled']);
        $pembelian->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate',
            'data' => $pembelian,
        ]);
    }

    public function apiDestroy(int $id): JsonResponse
    {
        $pembelian = Pembelian::find($id);
        if (!$pembelian) return response()->json(['success' => false, 'message' => 'Pembelian tidak ditemukan'], 404);
        $produk = $pembelian->produk;
        if ($produk && $pembelian->type === 'stok') {
            $produk->decrement('stok', $pembelian->jumlah);
        }
        $pembelian->delete();
        return response()->json(['success' => true, 'message' => 'Pembelian berhasil dihapus']);
    }
}