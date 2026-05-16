<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Jasa;

class JasaController extends Controller
{
    public function index()
    {
        $jasas = Jasa::latest()->get();
        return view('jasa.index', compact('jasas'));
    }

    public function create()
    {
        return view('jasa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jasa' => 'required|string|max:100',
            'harga_jasa' => 'required|numeric|min:0',
        ]);

        Jasa::create($request->all());

        return redirect()->route('jasa.index')->with('success', 'Layanan Jasa berhasil ditambahkan!');
    }

    public function edit(Jasa $jasa)
    {
        return view('jasa.edit', compact('jasa'));
    }

    public function update(Request $request, Jasa $jasa)
    {
        $request->validate([
            'nama_jasa' => 'required|string|max:100',
            'harga_jasa' => 'required|numeric|min:0',
        ]);

        $jasa->update($request->all());

        return redirect()->route('jasa.index')->with('success', 'Layanan Jasa berhasil diperbarui!');
    }

    public function destroy(Jasa $jasa)
    {
        $jasa->delete();

        return redirect()->route('jasa.index')->with('success', 'Layanan Jasa berhasil dihapus!');
    }
}
