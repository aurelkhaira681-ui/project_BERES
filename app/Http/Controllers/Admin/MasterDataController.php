<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'kategori');

        $kategori = KategoriKerusakan::withCount('aduan')->get();
        $lokasi = Lokasi::withCount('aduan')->get();
        $pengguna = User::where('role', 'siswa')->latest()->paginate(15);

        return view('admin.master-data', compact('tab', 'kategori', 'lokasi', 'pengguna'));
    }

    public function storeKategori(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:10'],
        ]);

        KategoriKerusakan::create($data);

        return back()->with('success', 'Kategori kerusakan ditambahkan.');
    }

    public function updateKategori(Request $request, KategoriKerusakan $kategori)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:10'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $kategori->update([
            ...$data,
            'aktif' => $request->boolean('aktif'),
        ]);

        return back()->with('success', 'Kategori kerusakan diperbarui.');
    }

    public function destroyKategori(KategoriKerusakan $kategori)
    {
        if ($kategori->aduan()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih dipakai aduan. Nonaktifkan saja lewat tombol edit.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori kerusakan dihapus.');
    }

    public function storeLokasi(Request $request)
    {
        $data = $request->validate([
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'lantai' => ['nullable', 'string', 'max:50'],
        ]);

        Lokasi::create($data);

        return back()->with('success', 'Lokasi/ruangan ditambahkan.');
    }

    public function updateLokasi(Request $request, Lokasi $lokasi)
    {
        $data = $request->validate([
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'lantai' => ['nullable', 'string', 'max:50'],
        ]);

        $lokasi->update($data);

        return back()->with('success', 'Lokasi/ruangan diperbarui.');
    }

    public function destroyLokasi(Lokasi $lokasi)
    {
        if ($lokasi->aduan()->exists()) {
            return back()->with('error', 'Lokasi tidak dapat dihapus karena masih dipakai aduan.');
        }

        $lokasi->delete();

        return back()->with('success', 'Lokasi/ruangan dihapus.');
    }
}