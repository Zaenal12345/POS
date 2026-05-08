<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori');
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        $produks = $query->latest()->paginate(10);
        $kategoris = Kategori::all();
        return view('produk.index', compact('produks', 'kategoris'));
    }

    public function data(Request $request)
    {
        $query = Produk::with('kategori');
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->filter === 'low') {
            $query->where('tanpa_stok', 0)->where('stok', '<=', 3);
        }
        $produks = $query->latest()->paginate($request->per_page ?? 10);
        return response()->json($produks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:255',
            'harga_beli' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
            'stok' => 'nullable|integer|min:0',
            'tanpa_stok' => 'boolean',
        ]);

        $stok = $request->filled('stok') ? $validated['stok'] : 0;
        $produk = Produk::create([
            'kategori_id' => $request->kategori_id,
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->boolean('tanpa_stok') ? 0 : $stok,
            'tanpa_stok' => $request->boolean('tanpa_stok'),
        ]);
        $produk->load('kategori');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan', 'data' => $produk], 201);
        }
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:255',
            'harga_beli' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
            'stok' => 'nullable|integer|min:0',
            'tanpa_stok' => 'boolean',
        ]);

        $stok = $request->filled('stok') ? $validated['stok'] : 0;
        $produk->update([
            'kategori_id' => $request->kategori_id,
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->boolean('tanpa_stok') ? 0 : $stok,
            'tanpa_stok' => $request->boolean('tanpa_stok'),
        ]);
        $produk->load('kategori');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Produk berhasil diupdate', 'data' => $produk]);
        }
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Request $request, Produk $produk)
    {
        $produk->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus']);
        }
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
}
