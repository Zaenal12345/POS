<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::latest()->paginate(10);
        return view('kategori.index', compact('kategoris'));
    }

    public function data(Request $request)
    {
        $kategoris = Kategori::latest()->paginate($request->per_page ?? 10);
        return response()->json($kategoris);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama' => 'required|string|max:255']);
        $kategori = Kategori::create([
            'nama' => $data['nama'],
            'slug' => Str::slug($data['nama']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $kategori,
            ], 201);
        }
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate(['nama' => 'required|string|max:255']);
        $kategori->update([
            'nama' => $data['nama'],
            'slug' => Str::slug($data['nama']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate',
                'data' => $kategori,
            ]);
        }
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(Request $request, Kategori $kategori)
    {
        $kategori->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
            ]);
        }
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
