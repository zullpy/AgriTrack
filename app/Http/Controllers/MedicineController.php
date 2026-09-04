<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('tanaman_sasaran', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->input('jenis')) {
            $query->where('jenis', $jenis);
        }

        $medicines = $query->latest()->paginate(10)->withQueryString();

        return view('data-obat.index', compact('medicines'));
    }

    public function create()
    {
        return view('data-obat.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|in:Pestisida,Fungisida,Pupuk,Herbisida',
            'tanaman_sasaran' => 'required|string|max:255',
            'dosis_anjuran' => 'nullable|string|max:255',
            'interval_aplikasi' => 'nullable|string|max:255',
            'catatan_keamanan' => 'nullable|string|max:1000',
            'stok' => 'required|integer|min:0',
        ]);

        Medicine::create($validated);

        return redirect('/data-obat')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function edit(Medicine $medicine)
    {
        return view('data-obat.form', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|in:Pestisida,Fungisida,Pupuk,Herbisida',
            'tanaman_sasaran' => 'required|string|max:255',
            'dosis_anjuran' => 'nullable|string|max:255',
            'interval_aplikasi' => 'nullable|string|max:255',
            'catatan_keamanan' => 'nullable|string|max:1000',
            'stok' => 'required|integer|min:0',
        ]);

        $medicine->update($validated);

        return redirect('/data-obat')->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return redirect('/data-obat')->with('success', 'Data obat berhasil dihapus.');
    }
}
