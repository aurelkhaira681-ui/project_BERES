<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\Teknisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'kategori');

        $kategori = KategoriKerusakan::withCount('aduan')->get();
        $lokasi = Lokasi::withCount('aduan')->get();
        $pengguna = User::where('role', 'siswa')->latest()->paginate(15);
        $teknisi = Teknisi::beban()->orderBy('jurusan')->orderBy('nama')->get();

        return view('admin.master-data', compact('tab', 'kategori', 'lokasi', 'pengguna', 'teknisi'));
    }

    public function storeKategori(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:10'],
        ]);

        KategoriKerusakan::create($data);

        return back()->with('success', __('Kategori kerusakan ditambahkan.'));
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

        return back()->with('success', __('Kategori kerusakan diperbarui.'));
    }

    public function destroyKategori(KategoriKerusakan $kategori)
    {
        if ($kategori->aduan()->exists()) {
            return back()->with('error', __('Kategori tidak dapat dihapus karena masih dipakai aduan. Nonaktifkan saja lewat tombol edit.'));
        }

        $kategori->delete();

        return back()->with('success', __('Kategori kerusakan dihapus.'));
    }

    public function storeLokasi(Request $request)
    {
        $data = $request->validate([
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'lantai' => ['nullable', 'string', 'max:50'],
            'jurusan' => ['nullable', Rule::in(Teknisi::JURUSAN)],
        ]);

        Lokasi::create($data);

        return back()->with('success', __('Lokasi/ruangan ditambahkan.'));
    }

    public function updateLokasi(Request $request, Lokasi $lokasi)
    {
        $data = $request->validate([
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'lantai' => ['nullable', 'string', 'max:50'],
            'jurusan' => ['nullable', Rule::in(Teknisi::JURUSAN)],
        ]);

        $lokasi->update($data);

        return back()->with('success', __('Lokasi/ruangan diperbarui.'));
    }

    public function destroyLokasi(Lokasi $lokasi)
    {
        if ($lokasi->aduan()->exists()) {
            return back()->with('error', __('Lokasi tidak dapat dihapus karena masih dipakai aduan.'));
        }

        $lokasi->delete();

        return back()->with('success', __('Lokasi/ruangan dihapus.'));
    }

    public function storeTeknisi(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', Rule::in(Teknisi::JURUSAN)],
        ]);

        Teknisi::create([...$data, 'aktif' => true]);

        return back()->with('success', __('Teknisi ditambahkan.'));
    }

    public function updateTeknisi(Request $request, Teknisi $teknisi)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', Rule::in(Teknisi::JURUSAN)],
        ]);

        $teknisi->update([...$data, 'aktif' => $request->boolean('aktif')]);

        return back()->with('success', __('Teknisi diperbarui.'));
    }

    public function destroyTeknisi(Teknisi $teknisi)
    {
        if ($teknisi->aduan()->exists()) {
            return back()->with('error', __('Teknisi tidak dapat dihapus karena masih punya riwayat aduan. Nonaktifkan saja (cuti) lewat tombol edit.'));
        }

        $teknisi->delete();

        return back()->with('success', __('Teknisi dihapus.'));
    }
}