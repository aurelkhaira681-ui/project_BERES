<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\Lokasi;
use App\Models\Teknisi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $awalBulan = now()->startOfMonth();
        $awalBulanLalu = now()->subMonthNoOverflow()->startOfMonth();

        $totalBulanIni = Aduan::where('created_at', '>=', $awalBulan)->count();
        $totalBulanLalu = Aduan::whereBetween('created_at', [$awalBulanLalu, $awalBulan])->count();

        if ($totalBulanLalu > 0) {
            $tren = (int) round(($totalBulanIni - $totalBulanLalu) / $totalBulanLalu * 100);
        } else {
            $tren = $totalBulanIni > 0 ? 100 : 0;
        }

        $stats = [
            'total_bulan_ini' => $totalBulanIni,
            'tren_total' => $tren,
            'menunggu' => Aduan::where('status', 'pending')->count(),
            'disetujui' => Aduan::whereIn('status', ['approved', 'selesai'])->count(),
            'ditolak' => Aduan::where('status', 'rejected')->count(),
        ];

        // Grafik 6 bulan terakhir (Selesai = disetujui + selesai)
        $mulai = now()->subMonthsNoOverflow(5)->startOfMonth();
        $semua = Aduan::where('created_at', '>=', $mulai)->get(['status', 'created_at']);

        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonthsNoOverflow($i)->startOfMonth();
            $data = $semua->filter(fn ($a) => $a->created_at->isSameMonth($bulan));
            $grafik[] = [
                'label' => $bulan->locale('id')->translatedFormat('M'),
                'selesai' => $data->whereIn('status', ['approved', 'selesai'])->count(),
                'pending' => $data->where('status', 'pending')->count(),
                'ditolak' => $data->where('status', 'rejected')->count(),
            ];
        }
        $maks = max(1, collect($grafik)->max(fn ($g) => $g['selesai'] + $g['pending'] + $g['ditolak']));
        $skalaMaks = max(4, (int) ceil($maks / 4) * 4);

        // Donut status (seluruh aduan)
        $donut = [
            'selesai' => $stats['disetujui'],
            'pending' => $stats['menunggu'],
            'ditolak' => $stats['ditolak'],
        ];
        $donut['total'] = array_sum($donut);

        $aktivitas = Aduan::with('user')->latest('updated_at')->take(5)->get();

        $bebanTeknisi = Teknisi::beban()->orderByDesc('aktif')->orderByDesc('aktif_count')->orderBy('nama')->get();

        // ===== Analitik tambahan (aduan yang ditolak tidak dihitung) =====
        $kategoriTeratas = Aduan::with('kategori')
            ->where('status', '!=', 'rejected')
            ->selectRaw('kategori_kerusakan_id, count(*) as total')
            ->groupBy('kategori_kerusakan_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $ruanganTeratas = Aduan::with('lokasi')
            ->where('status', '!=', 'rejected')
            ->selectRaw('lokasi_id, count(*) as total')
            ->groupBy('lokasi_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $rataSelesai = $this->rataWaktuSelesai();

        // Peta panas denah: jumlah aduan per ruangan yang ada di config/denah.php
        $perLokasi = Aduan::where('status', '!=', 'rejected')
            ->selectRaw('lokasi_id, count(*) as total')
            ->groupBy('lokasi_id')
            ->pluck('total', 'lokasi_id');
        $idLokasi = Lokasi::pluck('id', 'nama_ruangan');

        $panas = collect(config('denah.ruangan', []))
            ->map(fn ($r) => $r + ['jumlah' => (int) ($perLokasi[$idLokasi[$r['nama']] ?? 0] ?? 0)])
            ->values();
        $panasMaks = max(1, (int) $panas->max('jumlah'));

        return view('admin.dashboard', compact(
            'stats', 'grafik', 'skalaMaks', 'donut', 'aktivitas', 'bebanTeknisi',
            'kategoriTeratas', 'ruanganTeratas', 'rataSelesai', 'panas', 'panasMaks'
        ));
    }

    /** Rata-rata lama penyelesaian (dari diajukan sampai selesai) dalam teks ramah baca; null jika belum ada data. */
    private function rataWaktuSelesai(): ?string
    {
        $menit = Aduan::where('status', 'selesai')
            ->whereNotNull('selesai_at')
            ->get(['created_at', 'selesai_at'])
            ->avg(fn ($a) => max(0, $a->created_at->diffInMinutes($a->selesai_at)));

        if ($menit === null) {
            return null;
        }

        return match (true) {
            $menit < 60 => round($menit) . ' menit',
            $menit < 48 * 60 => round($menit / 60, 1) . ' jam',
            default => round($menit / 1440, 1) . ' hari',
        };
    }
}