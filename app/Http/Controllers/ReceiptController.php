<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show($id)
    {
        $penjualan = Penjualan::with('items.produk.kategori')->findOrFail($id);
        $settings = StoreSetting::allSettings();

        return view('receipt.show', compact('penjualan', 'settings'));
    }

    public function print($id)
    {
        $penjualan = Penjualan::with('items.produk.kategori')->findOrFail($id);
        $settings = StoreSetting::allSettings();

        return view('receipt.print', compact('penjualan', 'settings'));
    }
}
