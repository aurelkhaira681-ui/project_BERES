<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Hitung aduan per status untuk kartu statistik di beranda
        $counts = $user->aduan()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total'    => (int) $counts->sum(),
            'menunggu' => (int) ($counts['pending'] ?? 0),
            'diproses' => (int) ($counts['approved'] ?? 0),
            'selesai'  => (int) ($counts['selesai'] ?? 0),
        ];

        // Filter riwayat di beranda (dropdown "Semua")
        $status = $request->query('status', 'semua');
        if (!in_array($status, ['pending', 'approved', 'selesai', 'rejected'], true)) {
            $status = 'semua';
        }

        $query = $user->aduan()->with(['kategori', 'lokasi'])->latest();

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        $riwayat = $query->take(6)->get();

        // Notifikasi: aduan yang sedang diproses (sudah disetujui, belum selesai)
        $sedangDiproses = $user->aduan()
            ->with(['kategori', 'lokasi', 'teknisi'])
            ->where('status', 'approved')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('siswa.dashboard', compact('riwayat', 'stats', 'status', 'sedangDiproses'));
    }
}