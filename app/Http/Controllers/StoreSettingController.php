<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;

class StoreSettingController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::allSettings();
        return view('settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'no_telp' => 'nullable|string|max:50',
            'footer_nota' => 'nullable|string|max:255',
        ]);

        StoreSetting::set('nama_toko', $request->nama_toko);
        StoreSetting::set('alamat', $request->alamat);
        StoreSetting::set('no_telp', $request->no_telp);
        StoreSetting::set('footer_nota', $request->footer_nota);

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }
}
