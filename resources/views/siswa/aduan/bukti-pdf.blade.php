<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Laporan {{ $aduan->kode_tiket }}</title>
    <style>
        @page { margin: 36px 40px; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        .header { border-bottom: 3px solid #16305c; padding-bottom: 10px; margin-bottom: 18px; }
        .header table { width: 100%; }
        .brand { font-size: 22px; font-weight: bold; color: #16305c; letter-spacing: 1px; }
        .brand-sub { font-size: 10px; color: #64748b; }
        .judul { text-align: right; font-size: 14px; font-weight: bold; color: #0f2d5c; }
        .tiket-box { background: #f0f5ff; border: 1px solid #b9d6fb; border-radius: 6px; padding: 12px 14px; margin-bottom: 16px; }
        .tiket-box table { width: 100%; }
        .tiket-label { font-size: 9px; text-transform: uppercase; color: #64748b; }
        .tiket-kode { font-size: 20px; font-weight: bold; color: #16305c; }
        .qr { width: 78px; height: 78px; }
        .qr-ket { font-size: 8px; color: #64748b; text-align: center; margin-top: 2px; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: bold; background: #fff6e0; color: #b36f00; border: 1px solid #f7d98e; }
        .status.approved { background: #e6f1ff; color: #1a63c4; border-color: #b9d6fb; }
        .status.selesai { background: #e3f7ec; color: #127a4b; border-color: #a9e2c4; }
        .status.rejected { background: #fdeceb; color: #c2352d; border-color: #f5bcb8; }
        h2 { font-size: 12px; color: #16305c; margin: 18px 0 6px; padding-bottom: 3px; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; letter-spacing: .5px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data td { padding: 5px 0; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        table.data td.k { width: 30%; color: #64748b; }
        table.data td.v { font-weight: bold; }
        .deskripsi { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 10px; }
        .foto { margin-top: 8px; }
        .foto img { width: 150px; height: 110px; border: 1px solid #e2e8f0; margin-right: 6px; margin-bottom: 6px; }
        .catatan { margin-top: 20px; font-size: 10px; color: #475569; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 8px 10px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="brand">BERES</div>
                    <div class="brand-sub">Sistem Pelaporan Kerusakan Sarana &amp; Prasarana Sekolah</div>
                </td>
                <td class="judul">BUKTI LAPORAN<br>KERUSAKAN</td>
            </tr>
        </table>
    </div>

    <div class="tiket-box">
        <table>
            <tr>
                <td>
                    <div class="tiket-label">Nomor Tiket</div>
                    <div class="tiket-kode">{{ $aduan->kode_tiket }}</div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div class="tiket-label">Status Saat Dicetak</div>
                    <span class="status {{ $aduan->status }}">{{ match($aduan->status) {
                        'pending' => 'Menunggu',
                        'approved' => 'Diproses',
                        'selesai' => 'Selesai',
                        'rejected' => 'Ditolak',
                        default => ucfirst($aduan->status),
                    } }}</span>
                </td>
                @if(!empty($qr))
                    <td style="width: 90px; text-align: center; vertical-align: middle;">
                        <img class="qr" src="{{ $qr }}" alt="QR tiket">
                        <div class="qr-ket">Pindai untuk<br>cek status</div>
                    </td>
                @endif
            </tr>
        </table>
    </div>

    <h2>Data Pelapor</h2>
    <table class="data">
        <tr><td class="k">Nama</td><td class="v">{{ $aduan->user->name }}</td></tr>
        <tr><td class="k">NISN</td><td class="v">{{ $aduan->user->nisn ?: '-' }}</td></tr>
        <tr><td class="k">Kelas / No. Absen</td><td class="v">{{ $aduan->user->kelas ?: '-' }}@if($aduan->user->no_presensi) / {{ $aduan->user->no_presensi }}@endif</td></tr>
        <tr><td class="k">No. HP</td><td class="v">{{ $aduan->user->no_hp ?: '-' }}</td></tr>
    </table>

    <h2>Detail Laporan</h2>
    <table class="data">
        <tr><td class="k">Waktu Pelaporan</td><td class="v">{{ $aduan->created_at->timezone('Asia/Jakarta')->format('d M Y, H.i') }} WIB</td></tr>
        <tr><td class="k">Tanggal Kejadian</td><td class="v">{{ $aduan->tanggal_kejadian?->timezone('Asia/Jakarta')->format('d M Y') ?? '-' }}</td></tr>
        <tr><td class="k">Kategori Kerusakan</td><td class="v">{{ $aduan->kategori->nama }}</td></tr>
        <tr><td class="k">Lokasi</td><td class="v">{{ $aduan->lokasi->nama_lengkap }}</td></tr>
        <tr><td class="k">Teknisi</td><td class="v">{{ $aduan->teknisi?->nama ?? 'Ditentukan petugas' }}@if($aduan->teknisi) ({{ \App\Models\Teknisi::singkat($aduan->teknisi->jurusan) }})@endif</td></tr>
    </table>

    <h2>Deskripsi Kerusakan</h2>
    <div class="deskripsi">{{ $aduan->deskripsi }}</div>

    @if(count($foto))
        <h2>Foto Bukti</h2>
        <div class="foto">
            @foreach($foto as $src)
                <img src="{{ $src }}" alt="Foto bukti">
            @endforeach
        </div>
    @endif

    <div class="catatan">
        Dokumen ini adalah bukti bahwa laporan telah diterima oleh sistem BERES. Simpan nomor tiket
        <strong>{{ $aduan->kode_tiket }}</strong> untuk memantau progres penanganan di menu Riwayat.
    </div>

    <div class="footer">
        Dicetak pada {{ $dicetak->format('d M Y, H.i') }} WIB &middot; {{ $aduan->kode_tiket }} &middot; BERES
    </div>
</body>
</html>