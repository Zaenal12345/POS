<?php

namespace App\Http\Controllers;

use App\Models\Keuntungan;
use Illuminate\Http\Request;

class KeuntunganController extends Controller
{
    public function index(Request $request)
    {
        $query = Keuntungan::query();

        if ($request->bulan && $request->tahun) {
            $query->whereMonth('tanggal', $request->bulan)
                  ->whereYear('tanggal', $request->tahun);
        } else {
            $query->whereMonth('tanggal', now()->month)
                  ->whereYear('tanggal', now()->year);
        }

        $keuntungans = $query->orderBy('tanggal', 'desc')->get();
        $totalMasuk = $keuntungans->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $keuntungans->where('tipe', 'keluar')->sum('jumlah');
        $total = $totalMasuk - $totalKeluar;

        return view('keuntungan.index', compact('keuntungans', 'totalMasuk', 'totalKeluar', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tipe' => 'required|in:masuk,keluar',
            'keterangan' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
        ]);

        Keuntungan::create($request->all());

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Keuntungan berhasil disimpan']);
        }

        return redirect()->back()->with('success', 'Keuntungan berhasil disimpan');
    }

    public function destroy(Request $request, Keuntungan $keuntungan)
    {
        $keuntungan->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Keuntungan berhasil dihapus']);
        }

        return redirect()->back()->with('success', 'Keuntungan berhasil dihapus');
    }
}
