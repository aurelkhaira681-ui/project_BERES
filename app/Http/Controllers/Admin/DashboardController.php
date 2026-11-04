<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
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

        return view('admin.dashboard', compact('stats', 'grafik', 'skalaMaks', 'donut', 'aktivitas', 'bebanTeknisi'));
    }
}