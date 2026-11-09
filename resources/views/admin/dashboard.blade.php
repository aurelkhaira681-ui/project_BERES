@extends('layouts.dashboard')
@section('title', 'Dashboard - BERES')
@section('content')

@php
    $namaDepan = explode(' ', trim(auth()->user()->name))[0];
    $tanggal = now()->locale('id')->translatedFormat('l, j F Y');
    $tren = $stats['tren_total'];

    $kartu = [
        ['Total Aduan', 'Bulan Ini',        $stats['total_bulan_ini'], 'from-[#e6f0ff] to-[#f4f8ff] border-[#d6e6ff]', 'bg-[#1f6fe5]', '<path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM9 12h6M9 16h6"/>'],
        ['Menunggu', 'Verifikasi',          $stats['menunggu'],        'from-[#fff3dc] to-[#fffaf0] border-[#ffe6b3]', 'bg-[#f5a524]', '<circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/>'],
        ['Disetujui', '& Selesai',          $stats['disetujui'],       'from-[#dff7e8] to-[#f2fcf6] border-[#c4eed6]', 'bg-[#22b36b]', '<path d="M5 12.5l4.5 4.5L19 7.5"/>'],
        ['Ditolak', '',                     $stats['ditolak'],         'from-[#ffe3e1] to-[#fff4f3] border-[#ffd0cc]', 'bg-[#ef4b43]', '<path d="M6 6l12 12M18 6L6 18"/>'],
    ];

    // Donut: conic-gradient
    $tot = max(1, $donut['total']);
    $pSelesai = $donut['selesai'] / $tot * 100;
    $pPending = $donut['pending'] / $tot * 100;
    $gradien = $donut['total'] === 0
        ? '#e5ebf5 0 100%'
        : sprintf('#2f7cf6 0 %1$.2f%%, #f5a524 %1$.2f%% %2$.2f%%, #ef4b43 %2$.2f%% 100%%', $pSelesai, $pSelesai + $pPending);

    $legend = [
        ['Selesai', $donut['selesai'], 'bg-[#2f7cf6]'],
        ['Pending', $donut['pending'], 'bg-[#f5a524]'],
        ['Ditolak', $donut['ditolak'], 'bg-[#ef4b43]'],
    ];
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div class="flex items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#0f2d5c]">Selamat datang, {{ $namaDepan }}!</h1>
            <p class="text-[#5d7aa8] mt-0.5 text-sm sm:text-base">Kelola pengaduan sarana sekolah dengan mudah dan cepat.</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.aduan.index', ['status' => 'pending']) }}" aria-label="Aduan menunggu verifikasi" class="relative w-11 h-11 rounded-xl bg-white/80 border border-slate-100 flex items-center justify-center text-[#1f4f9c] hover:bg-white">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a6 6 0 0 0-6 6v4l-1.7 3.4A1 1 0 0 0 5.2 17h13.6a1 1 0 0 0 .9-1.6L18 12V8a6 6 0 0 0-6-6zm-2 17a2 2 0 0 0 4 0z"/></svg>
            @if($stats['menunggu'] > 0)
                <span class="js-notif-badge absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 rounded-full bg-[#ef4b43] text-white text-[11px] font-bold flex items-center justify-center">{{ $stats['menunggu'] }}</span>
            @endif
        </a>
        <div class="flex items-center gap-2 rounded-xl bg-white/80 border border-slate-100 px-4 h-11 text-sm font-semibold text-[#0f2d5c]">
            <svg class="w-4 h-4 text-[#1f6fe5]" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2v2H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2zm-2 8h14v9H5z"/></svg>
            {{ $tanggal }}
                    </div>
    </div>
</div>

{{-- Kartu statistik --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-5">
    @foreach($kartu as $i => [$l1, $l2, $angka, $bg, $ikonBg, $ikon])
        <div class="b-lift relative overflow-hidden rounded-2xl border bg-gradient-to-br {{ $bg }} px-4 py-3.5">
            @if($i === 0)
                <span class="absolute top-2 right-2 text-[10px] font-bold rounded-full px-1.5 py-0.5 {{ $tren >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">{{ $tren >= 0 ? '↑' : '↓' }} {{ abs($tren) }}%</span>
            @endif
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 shrink-0 rounded-xl {{ $ikonBg }} text-white flex items-center justify-center">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">{!! $ikon !!}</svg>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold text-[#0f2d5c] leading-none" data-count="{{ $angka }}">{{ $angka }}</p>
                    <p class="text-xs text-[#40608f] mt-1 leading-tight">{{ $l1 }}@if($l2) {{ $l2 }}@endif</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Aksi cepat --}}
@php
    $aksi = [
        [route('admin.aduan.index', ['status' => 'pending']), 'Verifikasi Aduan', $stats['menunggu'] > 0 ? $stats['menunggu'] . ' aduan menunggu giliran' : 'Semua aduan sudah ditinjau', 'bg-[#f5a524]', '<path d="M5 12.5l4.5 4.5L19 7.5"/>', $stats['menunggu']],
        [route('admin.master-data'), 'Kelola Master Data', 'Kategori kerusakan & lokasi', 'bg-[#1f6fe5]', '<path d="M3 5a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>', 0],
        [route('admin.aduan.export'), 'Ekspor Data', 'Unduh rekap aduan (CSV)', 'bg-[#22b36b]', '<path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>', 0],
    ];
@endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-5">
    @foreach($aksi as [$urlAksi, $judulAksi, $ketAksi, $warnaAksi, $ikonAksi, $badgeAksi])
        <a href="{{ $urlAksi }}" class="{{ $card }} b-lift group flex items-center gap-3 px-4 py-3.5">
            <span class="w-10 h-10 shrink-0 rounded-xl {{ $warnaAksi }} text-white flex items-center justify-center transition duration-300 group-hover:rotate-6 group-hover:scale-110">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">{!! $ikonAksi !!}</svg>
            </span>
            <span class="min-w-0 flex-1">
                <span class="block font-bold text-sm text-[#0f2d5c]">{{ $judulAksi }}</span>
                <span class="block text-xs text-[#5d7aa8] truncate">{{ $ketAksi }}</span>
            </span>
            @if($badgeAksi > 0)
                <span class="relative flex h-5 min-w-[20px]">
                    <span class="absolute inset-0 rounded-full bg-[#ef4b43] opacity-60 animate-ping"></span>
                    <span class="relative min-w-[20px] h-5 px-1.5 rounded-full bg-[#ef4b43] text-white text-[11px] font-bold flex items-center justify-center">{{ $badgeAksi }}</span>
                </span>
            @endif
            <svg class="w-4 h-4 shrink-0 text-slate-400 transition duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
        </a>
    @endforeach
</div>

{{-- Grafik + donut + aktivitas --}}
<div class="grid grid-cols-1 xl:grid-cols-[1.5fr_1fr_1fr] gap-4 sm:gap-5 mb-5">

    {{-- Grafik pengaduan --}}
    <div class="{{ $card }} p-5">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h2 class="font-bold text-[#0f2d5c] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1f6fe5]" viewBox="0 0 24 24" fill="currentColor"><path d="M4 12h4v8H4zm6-8h4v16h-4zm6 5h4v11h-4z"/></svg>
                Grafik Pengaduan
            </h2>
            <span class="text-xs font-semibold text-[#40608f] border border-slate-200 rounded-lg px-3 py-1.5">6 Bulan Terakhir</span>
        </div>
        <div class="flex items-center justify-center gap-4 text-xs text-[#40608f] mb-3">
            <span class="flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-full bg-[#2f7cf6]"></i>Selesai</span>
            <span class="flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-full bg-[#f5a524]"></i>Pending</span>
            <span class="flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-full bg-[#ef4b43]"></i>Ditolak</span>
        </div>
        <div class="flex gap-2">
            <div class="flex flex-col-reverse justify-between h-48 text-[11px] text-slate-400 pb-0 -mt-1.5">
                @for($t = 0; $t <= 4; $t++)<span>{{ $skalaMaks / 4 * $t }}</span>@endfor
            </div>
            <div class="flex-1 relative h-48">
                <div class="absolute inset-0 flex flex-col-reverse justify-between pointer-events-none">
                    @for($t = 0; $t <= 4; $t++)<div class="border-t border-slate-100"></div>@endfor
                </div>
                <div class="absolute inset-0 flex items-end justify-around px-1 sm:px-3">
                    @foreach($grafik as $g)
                        @php $jml = $g['selesai'] + $g['pending'] + $g['ditolak']; @endphp
                        <div class="b-bar relative w-7 sm:w-10 h-full flex flex-col-reverse justify-start" title="{{ $g['label'] }}: {{ $jml }} aduan">
                            @if($g['selesai'])<div class="bg-[#2f7cf6]" style="height: {{ $g['selesai'] / $skalaMaks * 100 }}%"></div>@endif
                            @if($g['pending'])<div class="bg-[#f5a524]" style="height: {{ $g['pending'] / $skalaMaks * 100 }}%"></div>@endif
                            @if($g['ditolak'])<div class="bg-[#ef4b43]" style="height: {{ $g['ditolak'] / $skalaMaks * 100 }}%"></div>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-around pl-6 pr-1 sm:px-3 ml-2 mt-2 text-xs font-semibold text-[#40608f]">
            @foreach($grafik as $g)<span class="w-7 sm:w-10 text-center">{{ $g['label'] }}</span>@endforeach
        </div>
    </div>

    {{-- Status pengaduan --}}
    <div class="{{ $card }} p-5">
        <h2 class="font-bold text-[#0f2d5c] flex items-center gap-2 mb-5">
            <svg class="w-5 h-5 text-[#1f6fe5]" viewBox="0 0 24 24" fill="currentColor"><path d="M4 12h4v8H4zm6-8h4v16h-4zm6 5h4v11h-4z"/></svg>
            Status Pengaduan
        </h2>
        <div class="flex flex-wrap sm:flex-nowrap items-center justify-center gap-5">
            <div class="b-pop relative w-36 h-36 shrink-0 rounded-full" style="background: conic-gradient({{ $gradien }})">
                <div class="absolute inset-[22px] rounded-full bg-white flex flex-col items-center justify-center">
                    <span class="text-3xl font-extrabold text-[#0f2d5c] leading-none" data-count="{{ $donut['total'] }}">{{ $donut['total'] }}</span>
                    <span class="text-xs text-[#5d7aa8] mt-1">Total</span>
                </div>
            </div>
            <ul class="space-y-3 text-sm">
                @foreach($legend as [$nama, $jml, $warna])
                    <li class="flex items-start gap-2">
                        <i class="w-3 h-3 mt-1 rounded-full {{ $warna }}"></i>
                        <div>
                            <p class="font-semibold text-[#0f2d5c] leading-tight">{{ $nama }}</p>
                            <p class="text-xs text-[#5d7aa8]">{{ round($jml / $tot * 100) }}% ({{ $jml }})</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Aktivitas terbaru --}}
    <div class="{{ $card }} p-5">
        <div class="flex items-center justify-between gap-2 mb-5">
            <h2 class="font-bold text-[#0f2d5c] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1f6fe5]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 6.6L21 9l-5.2 4.3L17.5 20 12 16.3 6.5 20l1.7-6.7L3 9l6.6-.4z"/></svg>
                Aktivitas Terbaru
            </h2>
            <a href="{{ route('admin.aduan.index') }}" class="text-xs font-semibold text-[#1f6fe5] whitespace-nowrap">Lihat Semua →</a>
        </div>
        <ul class="space-y-4">
            @forelse($aktivitas as $a)
                @php
                    [$ket, $warna] = match ($a->status) {
                        'selesai'  => ['Telah diselesaikan', 'bg-[#2f7cf6]'],
                        'approved' => ['Disetujui, sedang diproses', 'bg-[#22b36b]'],
                        'rejected' => ['Telah ditolak', 'bg-[#ef4b43]'],
                        default    => ['Pengaduan baru dari ' . $a->user->name, 'bg-[#f5a524]'],
                    };
                @endphp
                <li class="flex items-start gap-3">
                    <span class="w-8 h-8 shrink-0 rounded-full {{ $warna }} text-white flex items-center justify-center text-xs font-bold">{{ mb_substr($a->user->name, 0, 1) }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <a href="{{ route('admin.aduan.show', $a) }}" class="font-semibold text-sm text-[#0f2d5c] hover:underline">Pengaduan #{{ $a->kode_tiket }}</a>
                            <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $a->updated_at->locale('id')->diffForHumans(short: true) }}</span>
                        </div>
                        <p class="text-xs text-[#5d7aa8] truncate">{{ $ket }}</p>
                    </div>
                </li>
            @empty
                <li class="text-sm text-slate-400">Belum ada aktivitas.</li>
            @endforelse
        </ul>
    </div>
</div>

{{-- Analitik: kategori & ruangan terbanyak + peta panas denah --}}
@php
    $maksKategori = max(1, (int) $kategoriTeratas->max('total'));
    $maksRuangan = max(1, (int) $ruanganTeratas->max('total'));
@endphp
<div class="grid grid-cols-1 xl:grid-cols-[1fr_1.1fr] gap-4 sm:gap-5 mb-5">
    <div class="space-y-4 sm:space-y-5">
        {{-- Rata-rata waktu penyelesaian --}}
        <div class="{{ $card }} p-5 flex items-center gap-4">
            <span class="w-12 h-12 shrink-0 rounded-xl bg-[#22b36b] text-white flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div>
                <p class="text-xs text-[#5d7aa8]">Rata-rata Waktu Penyelesaian</p>
                <p class="text-2xl font-extrabold text-[#0f2d5c] leading-tight">{{ $rataSelesai ?? '-' }}</p>
                <p class="text-xs text-slate-400">{{ $rataSelesai ? 'Dihitung dari diajukan sampai selesai' : 'Belum ada aduan yang selesai' }}</p>
            </div>
        </div>

        {{-- Kategori kerusakan terbanyak --}}
        <div class="{{ $card }} p-5">
            <h2 class="font-bold text-[#0f2d5c] mb-4">Kategori Kerusakan Terbanyak</h2>
            <div class="space-y-3.5">
                @forelse($kategoriTeratas as $k)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-semibold text-[#0f2d5c] truncate">{{ $k->kategori?->nama ?? 'Tanpa kategori' }}</span>
                            <span class="font-bold text-[#0f2d5c]">{{ $k->total }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-[#e6effb] overflow-hidden">
                            <div class="h-2 rounded-full bg-[#2f7cf6]" style="width: {{ round($k->total / $maksKategori * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        {{-- Ruangan paling sering rusak --}}
        <div class="{{ $card }} p-5">
            <h2 class="font-bold text-[#0f2d5c] mb-4">Ruangan Paling Sering Dilaporkan</h2>
            <div class="space-y-3.5">
                @forelse($ruanganTeratas as $i => $r)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-semibold text-[#0f2d5c] truncate"><span class="text-slate-400 font-normal mr-1">{{ $i + 1 }}.</span>{{ $r->lokasi?->nama_ruangan ?? 'Lokasi dihapus' }}</span>
                            <span class="font-bold text-[#0f2d5c]">{{ $r->total }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-[#fdeceb] overflow-hidden">
                            <div class="h-2 rounded-full {{ $i === 0 ? 'bg-[#ef4b43]' : 'bg-[#f5a524]' }}" style="width: {{ round($r->total / $maksRuangan * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    @include('partials.denah-panas')
</div>

{{-- Banner --}}
<div class="rounded-2xl bg-gradient-to-r from-[#e3eeff] to-[#f1f6ff] border border-[#d6e6ff] px-5 py-4 flex flex-wrap items-center justify-between gap-3">
    <p class="flex items-center gap-3 text-sm font-semibold text-[#0f2d5c]">
        <svg class="w-6 h-6 shrink-0 text-[#1f6fe5]" viewBox="0 0 24 24" fill="currentColor"><path d="M3 10v4a1 1 0 0 0 1 1h2l5 4V5L6 9H4a1 1 0 0 0-1 1zm13.5 2a4.5 4.5 0 0 0-2.5-4v8a4.5 4.5 0 0 0 2.5-4z"/></svg>
        Pastikan sarana dan prasarana sekolah selalu dalam kondisi baik untuk kenyamanan bersama.
    </p>
    <a href="{{ route('admin.aduan.index') }}" class="rounded-xl bg-[#0f3a9a] hover:bg-[#0b2f7e] text-white text-sm font-bold px-5 py-2.5 transition">Lihat Semua Aduan →</a>
</div>

{{-- Beban teknisi --}}
@php
    $bebanMaks = max(1, (int) $bebanTeknisi->where('aktif', true)->max('aktif_count'));
@endphp
<div class="{{ $card }} p-5 mb-5">
    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="font-bold text-[#0f2d5c]">Tanggung Jawab Teknisi</h2>
            <p class="text-xs text-[#5d7aa8] mt-0.5">Aduan yang masih menunggu atau diproses per teknisi</p>
        </div>
        <a href="{{ route('admin.master-data', ['tab' => 'teknisi']) }}" class="text-sm font-semibold text-[#1f6fe5] hover:underline whitespace-nowrap">Kelola teknisi</a>
    </div>
    <div class="grid sm:grid-cols-2 gap-x-8 gap-y-3.5">
        @forelse($bebanTeknisi as $t)
            @php $lebar = $t->aktif ? round($t->aktif_count / $bebanMaks * 100) : 0; $berat = $t->aktif && $t->aktif_count >= 4; @endphp
            <div class="{{ $t->aktif ? '' : 'opacity-60' }}">
                <div class="flex items-center justify-between text-sm mb-1.5">
                    <span class="font-semibold text-[#0f2d5c]">{{ $t->nama }} <span class="text-xs font-normal text-slate-400">· {{ \App\Models\Teknisi::singkat($t->jurusan) }}@unless($t->aktif) · cuti @endunless</span></span>
                    <span class="font-bold {{ $berat ? 'text-[#c97f00]' : 'text-[#0f2d5c]' }}">{{ $t->aktif_count }}</span>
                </div>
                <div class="h-2 rounded-full bg-[#e6effb] overflow-hidden">
                    <div class="h-2 rounded-full {{ $berat ? 'bg-[#f5a524]' : 'bg-[#2f7cf6]' }}" style="width: {{ $lebar }}%; transform-origin: left"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">Belum ada teknisi.</p>
        @endforelse
    </div>
</div>

@endsection