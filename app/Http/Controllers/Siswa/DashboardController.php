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

        // Filter riwayat di beranda
        $status = $request->query('status', 'semua');
        if (!in_array($status, ['pending', 'approved', 'selesai', 'rejected'], true)) {
            $status = 'semua';
        }

        $query = $user->aduan()->with(['kategori', 'lokasi'])->latest();

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        $riwayat = $query->take(6)->get();

        // Lonceng notifikasi: pembaruan status (disetujui / sedang dikerjakan / selesai / ditolak)
        $notifikasi = $user->aduan()
            ->with(['kategori', 'lokasi', 'teknisi'])
            ->where('status', '!=', 'pending')
            ->where('updated_at', '>=', now()->subDays(14))
            ->latest('updated_at')
            ->take(8)
            ->get();
        $notifBaru = $user->notifikasiBelumDibaca()->count();

        // Ajakan memberi rating: aduan selesai yang belum dinilai
        $belumDinilai = $user->aduan()
            ->with(['kategori', 'teknisi'])
            ->where('status', 'selesai')
            ->whereNull('rating')
            ->latest('selesai_at')
            ->take(3)
            ->get();

        return view('siswa.dashboard', compact('riwayat', 'stats', 'status', 'notifikasi', 'notifBaru', 'belumDinilai'));
    }

    // Tandai semua notifikasi sudah dibaca (dipanggil saat lonceng dibuka)
    public function bacaNotifikasi(Request $request)
    {
        $request->user()->forceFill(['notif_dibaca_at' => now()])->saveQuietly();

        return response()->json(['ok' => true]);
    }
}