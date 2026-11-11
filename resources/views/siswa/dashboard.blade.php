@extends('layouts.dashboard')
@section('title', 'Beranda - BERES')
@section('content')

@php
    $now  = now('Asia/Jakarta')->locale('id');
    $jam  = $now->hour;
    $sapaan = $jam < 11 ? 'pagi' : ($jam < 15 ? 'siang' : ($jam < 18 ? 'sore' : 'malam'));
    $namaDepan = explode(' ', trim(auth()->user()->name))[0];
    $dibacaSampai = auth()->user()->notif_dibaca_at;

    // [label, angka, warna kartu (gradien), warna ikon, ikon]
    $statCards = [
        ['Total Aduan',   $stats['total'],    'linear-gradient(135deg,#e4efff,#f5f9ff)', '#2f7cf6', '<path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM9 12h6M9 16h6"/>'],
        ['Menunggu',      $stats['menunggu'], 'linear-gradient(135deg,#fff1d6,#fffaf0)', '#f5a524', '<circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/>'],
        ['Diproses',      $stats['diproses'], 'linear-gradient(135deg,#e0ecff,#f2f7ff)', '#1f6fe5', '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.1L3 17.7 6.3 21l6.3-6.3a4 4 0 0 0 5.1-5.4l-2.6 2.6-2.4-.6-.6-2.4z"/>'],
        ['Selesai',       $stats['selesai'],  'linear-gradient(135deg,#d9f6e6,#f1fcf6)', '#22b36b', '<path d="M5 12.5l4.5 4.5L19 7.5"/>'],
    ];

    $persen = $stats['total'] > 0 ? (int) round($stats['selesai'] / $stats['total'] * 100) : 0;
    $alur = [
        ['Ajukan aduan',  'Isi detail kerusakan & unggah foto bukti'],
        ['Diverifikasi',  'Petugas memeriksa laporan yang masuk'],
        ['Diproses',      'Teknisi mengerjakan perbaikan'],
        ['Selesai',       'Beri bintang untuk hasil perbaikan'],
    ];
    $tips = [
        'Foto kerusakan dari beberapa sudut supaya petugas cepat memahami kondisinya.',
        'Pilih prioritas “Mendesak” hanya untuk bahaya nyata, seperti korsleting atau atap bocor.',
        'Sebelum mengajukan, sistem akan memberi tahu kalau ruangan itu sudah punya aduan yang sama.',
        'Setelah aduan selesai, beri bintang supaya kualitas perbaikan terus meningkat.',
    ];

    $filterOpsi = ['semua' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Diproses', 'selesai' => 'Selesai', 'rejected' => 'Ditolak'];
@endphp

{{-- ===== HEADER ===== --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-[30px] font-extrabold tracking-tight text-[#0f2d5c]">Selamat {{ $sapaan }}, {{ $namaDepan }}</h1>
        <p class="text-[#5d7aa8] mt-1">Lihat pembaruan aduan sarana yang sudah kamu ajukan.</p>
    </div>
    <div class="contents sm:flex sm:items-center sm:gap-3">
        <div class="hidden sm:inline-flex items-center gap-2 rounded-2xl bg-white/80 border border-slate-100 px-4 h-12 text-sm font-semibold text-[#0f2d5c]">
            <svg class="w-4 h-4 text-[#1f6fe5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
            {{ $now->translatedFormat('l, d F Y') }}
        </div>

        {{-- Lonceng notifikasi status --}}
        <div class="contents md:block md:relative" id="notif-wrap">
            <button type="button" id="notif-btn" aria-label="Notifikasi status aduan" aria-haspopup="true" aria-expanded="false"
                    class="hidden md:flex relative w-12 h-12 rounded-2xl bg-white/80 hover:bg-white border border-slate-100 text-[#1f4f9c] items-center justify-center transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a6 6 0 0 0-6 6v4l-1.7 3.4A1 1 0 0 0 5.2 17h13.6a1 1 0 0 0 .9-1.6L18 12V8a6 6 0 0 0-6-6zm-2 17a2 2 0 0 0 4 0z"/></svg>
                @if($notifBaru > 0)
                    <span class="js-notif-badge absolute -top-1.5 -right-1.5 min-w-[22px] h-[22px] px-1 rounded-full bg-[#ef4b43] text-white text-[11px] font-bold flex items-center justify-center ring-2 ring-white">{{ $notifBaru }}</span>
                @endif
            </button>

            <div id="notif-panel" class="hidden fixed left-4 right-4 top-16 md:absolute md:left-auto md:right-0 md:top-[calc(100%+10px)] z-40 md:w-[23rem] rounded-3xl bg-white border border-slate-100 shadow-[0_20px_60px_rgba(20,50,105,0.28)] overflow-hidden text-slate-800">
                <div class="flex items-center justify-between gap-2 px-4 py-3.5 border-b border-slate-100">
                    <p class="font-extrabold text-sm text-[#0f2d5c]">Notifikasi Aduan</p>
                    <span class="text-[11px] font-bold {{ $notifBaru > 0 ? 'text-[#ef4b43] bg-[#fdeceb]' : 'text-slate-400 bg-slate-100' }} rounded-full px-2 py-0.5">{{ $notifBaru > 0 ? $notifBaru . ' baru' : 'Sudah dibaca' }}</span>
                </div>
                <div class="max-h-[22rem] overflow-y-auto">
                @forelse($notifikasi as $n)
                    @php
                        [$nJudul, $nIkon, $nWarna] = match ($n->status) {
                            'approved' => [$n->teknisi ? 'Disetujui · ' . $n->teknisi->nama . ' sedang mengerjakan' : 'Disetujui · sedang dikerjakan petugas', '🛠️', 'bg-[#dcebff]'],
                            'selesai'  => ['Selesai diperbaiki' . ($n->rating === null ? ' · beri bintang ya!' : ''), '✅', 'bg-[#d9f4e5]'],
                            'rejected' => ['Aduan ditolak petugas', '⛔', 'bg-[#fdeceb]'],
                            default    => ['Diperbarui', '🔔', 'bg-slate-100'],
                        };
                        $baru = !$dibacaSampai || $n->updated_at->gt($dibacaSampai);
                    @endphp
                    <a href="{{ route('siswa.aduan.show', $n) }}" class="flex items-start gap-3 px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition {{ $baru ? 'bg-[#f4f8ff]' : '' }}">
                        <span class="shrink-0 w-10 h-10 rounded-xl {{ $nWarna }} flex items-center justify-center text-lg">{{ $nIkon }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[13px] font-bold text-[#0f2d5c] leading-snug">{{ $nJudul }}</span>
                            <span class="block text-xs text-slate-500 mt-0.5 truncate">#{{ $n->kode_tiket }} · {{ Str::limit($n->deskripsi, 32) }}</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5">{{ $n->updated_at->locale('id')->diffForHumans() }}</span>
                        </span>
                        @if($baru)<span class="mt-1.5 w-2 h-2 rounded-full bg-[#2f7cf6] shrink-0"></span>@endif
                    </a>
                @empty
                    <div class="px-4 py-9 text-center">
                        <p class="text-3xl mb-1">🔔</p>
                        <p class="text-sm font-bold text-[#0f2d5c]">Belum ada pembaruan</p>
                        <p class="text-xs text-slate-400 mt-1">Kamu akan diberi tahu di sini saat aduan disetujui, dikerjakan, atau selesai.</p>
                    </div>
                @endforelse
                </div>
                <a href="{{ route('siswa.riwayat') }}" class="block text-center text-sm font-bold text-[#1d4f9c] bg-[#f4f8ff] hover:bg-[#e9f0fb] py-3 transition">Lihat semua riwayat →</a>
            </div>
        </div>
    </div>
</div>

{{-- ===== AJAKAN RATING ===== --}}
@if($belumDinilai->isNotEmpty())
    <div class="mb-5 rounded-3xl border border-[#ffe2a8] p-4 sm:p-5" style="background: linear-gradient(120deg,#fff6df,#fffbf1)">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">⭐</span>
            <div>
                <p class="font-extrabold text-[#7a4b00] leading-tight">Bagaimana hasil perbaikannya?</p>
                <p class="text-xs text-[#a56a10]">{{ $belumDinilai->count() }} aduan selesai menunggu penilaianmu</p>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
            @foreach($belumDinilai as $b)
                <a href="{{ route('siswa.aduan.show', $b) }}#nilai" class="group flex items-center gap-3 rounded-2xl bg-white/80 hover:bg-white border border-[#ffe2a8] px-3.5 py-2.5 transition hover:shadow-md">
                    <span class="w-9 h-9 shrink-0 rounded-xl bg-[#fff1cf] flex items-center justify-center">{{ $b->kategori->icon ?? '🧰' }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-[#0f2d5c] truncate">{{ Str::limit($b->deskripsi, 30) }}</span>
                        <span class="block text-[#e2a21a] text-sm tracking-widest leading-none mt-0.5 group-hover:text-[#f5a524]">☆☆☆☆☆</span>
                    </span>
                    <svg class="w-4 h-4 text-[#c98a1a] group-hover:translate-x-0.5 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                </a>
            @endforeach
        </div>
    </div>
@endif

{{-- ===== STATISTIK ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
    @foreach($statCards as [$label, $angka, $grad, $warna, $ikon])
        <div class="b-lift relative overflow-hidden rounded-3xl border border-white/70 p-4 shadow-[0_6px_22px_rgba(20,50,105,.07)]" style="background: {{ $grad }}">
            <span class="absolute -right-4 -top-4 w-20 h-20 rounded-full opacity-[.14]" style="background: {{ $warna }}"></span>
            <span class="relative w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-md" style="background: {{ $warna }}">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">{!! $ikon !!}</svg>
            </span>
            <p class="relative mt-3 text-3xl font-extrabold text-[#0f2d5c] leading-none tracking-tight" data-count="{{ $angka }}">{{ $angka }}</p>
            <p class="relative mt-1 text-xs font-semibold text-[#40608f]">{{ $label }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Riwayat aduan --}}
    <div class="{{ $card }} !rounded-3xl p-5 sm:p-6 lg:col-span-2 flex flex-col">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-4">
            <h2 class="font-extrabold text-lg text-[#0f2d5c]">Riwayat Aduan</h2>
            <div class="flex flex-wrap gap-1.5">
                @foreach($filterOpsi as $key => $label)
                    <a href="{{ route('siswa.dashboard', ['status' => $key]) }}"
                       class="rounded-full px-3 py-1 text-xs font-bold transition {{ $status === $key ? 'text-white shadow-md shadow-blue-600/30' : 'bg-[#eef3fb] text-[#40608f] hover:bg-[#dfe9f8]' }}"
                       @if($status === $key) style="background:linear-gradient(135deg,#2f7cf6,#4f46e5)" @endif>{{ $label }}</a>
                @endforeach
            </div>
        </div>

        @forelse($riwayat as $item)
            <a href="{{ route('siswa.aduan.show', $item) }}" class="group flex items-center gap-3 sm:gap-4 py-3.5 border-t border-slate-100 hover:bg-slate-50/70 -mx-2 px-2 rounded-2xl transition">
                <span class="shrink-0 w-12 h-12 rounded-2xl bg-[#eef3fb] flex items-center justify-center text-xl group-hover:scale-105 transition">{{ $item->kategori->icon ?? '🧰' }}</span>
                <span class="min-w-0 flex-1">
                    <span class="flex items-center gap-2">
                        <span class="block font-bold text-sm text-[#0f2d5c] truncate">{{ Str::limit($item->deskripsi, 44) }}</span>
                        @if($item->isMendesak() && in_array($item->status, ['pending', 'approved']))
                            @include('partials.prioritas-badge', ['item' => $item, 'kecil' => true])
                        @endif
                    </span>
                    <span class="block text-xs text-slate-400 mt-0.5 truncate">#{{ $item->kode_tiket }} · {{ $item->lokasi->nama_lengkap }} · {{ $item->tanggal_kejadian->format('d M Y') }}</span>
                </span>
                @if($item->rating)
                    <span class="hidden sm:inline text-xs font-bold text-[#e2a21a]">★ {{ $item->rating }}</span>
                @endif
                @include('partials.status-badge', ['item' => $item])
            </a>
        @empty
            <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
                <svg viewBox="0 0 120 100" class="w-32 h-28 b-float-soft" aria-hidden="true">
                    <ellipse cx="60" cy="58" rx="46" ry="34" fill="#e6f0fd"/>
                    <circle cx="20" cy="26" r="3" fill="#cfe1fb"/><circle cx="104" cy="30" r="2.5" fill="#cfe1fb"/>
                    <path d="M38 18h30l14 14v42a4 4 0 0 1-4 4H38a4 4 0 0 1-4-4V22a4 4 0 0 1 4-4z" fill="#fff" stroke="#2f6fd6" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M68 18v14h14" fill="none" stroke="#2f6fd6" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M42 44h22M42 53h30M42 62h18" stroke="#6ea6ee" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="80" cy="72" r="12" fill="#2f6fd6" stroke="#fff" stroke-width="3"/>
                    <path d="M74.5 72l4 4 7-8" stroke="#fff" stroke-width="2.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @if($status === 'semua')
                    <p class="mt-3 font-bold text-[#0f2d5c]">Belum ada aduan yang diajukan</p>
                    <p class="text-xs text-slate-400 mt-1">Laporkan kerusakan fasilitas sekolah lewat tombol di atas.</p>
                @else
                    <p class="mt-3 font-bold text-[#0f2d5c]">Tidak ada aduan dengan status ini</p>
                    <p class="text-xs text-slate-400 mt-1">Coba pilih status lain di atas.</p>
                @endif
            </div>
        @endforelse

        @if($riwayat->isNotEmpty())
            <a href="{{ route('siswa.riwayat') }}" class="mt-3 self-end text-sm font-bold text-[#1d4f9c] hover:underline">Lihat semua riwayat →</a>
        @endif
    </div>

    {{-- Kolom kanan --}}
    <div class="space-y-5">

        <div class="{{ $card }} !rounded-3xl b-lift p-5 sm:p-6">
            <h2 class="font-extrabold text-[#0f2d5c] mb-4">Tingkat Penyelesaian</h2>
            <div class="flex items-center gap-5">
                <div class="relative w-28 h-28 shrink-0">
                    <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90" aria-hidden="true">
                        <defs><linearGradient id="gRing" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#2f7cf6"/><stop offset="1" stop-color="#7c5cff"/></linearGradient></defs>
                        <circle cx="50" cy="50" r="42" fill="none" stroke="#e6effb" stroke-width="10" class="b-ring-track"/>
                        <circle cx="50" cy="50" r="42" fill="none" stroke="url(#gRing)" stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="263.9" stroke-dashoffset="263.9" class="b-ring" data-ring="{{ $persen }}"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-extrabold text-[#0f2d5c] leading-none"><span data-count="{{ $persen }}">{{ $persen }}</span>%</span>
                        <span class="text-[10px] text-slate-400 mt-1">selesai</span>
                    </div>
                </div>
                <p class="text-sm text-[#5d7aa8] leading-relaxed">
                    @if($stats['total'] > 0)
                        <b class="text-[#0f2d5c]">{{ $stats['selesai'] }}</b> dari <b class="text-[#0f2d5c]">{{ $stats['total'] }}</b> aduanmu sudah selesai ditangani.
                    @else
                        Belum ada aduan. Ajukan aduan pertamamu dan pantau progresnya di sini.
                    @endif
                </p>
            </div>
        </div>

        <div class="{{ $card }} !rounded-3xl p-5 sm:p-6">
            <h2 class="font-extrabold text-[#0f2d5c] mb-4">Alur Pengaduan</h2>
            <ol class="relative">
                <span class="b-line absolute left-[15px] top-4 bottom-4 w-0.5 bg-[#e6effb]" aria-hidden="true"></span>
                @foreach($alur as $i => [$judulAlur, $ketAlur])
                    <li class="relative flex gap-3 pb-5 last:pb-0">
                        <span class="b-step relative z-10 w-8 h-8 shrink-0 rounded-full {{ $i === 3 ? 'bg-[#22b36b]' : 'bg-[#1f6fe5]' }} text-white text-sm font-bold flex items-center justify-center" style="--i: {{ $i }}">{{ $i + 1 }}</span>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-[#0f2d5c] leading-tight">{{ $judulAlur }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $ketAlur }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="rounded-3xl border border-[#d6e6ff] p-5 sm:p-6" style="background: linear-gradient(135deg,#e8f0ff,#f6f2ff)">
            <div class="flex items-center gap-2 mb-3">
                <span class="b-bulb text-lg">💡</span>
                <h2 class="font-extrabold text-[#0f2d5c]">Tips Melapor</h2>
            </div>
            <div class="b-rot grid" data-rotator>
                @foreach($tips as $i => $tip)
                    <p class="text-sm text-[#40608f] leading-relaxed {{ $i === 0 ? 'on' : '' }}">{{ $tip }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var wrap = document.getElementById('notif-wrap');
        var btn = document.getElementById('notif-btn');
        var panel = document.getElementById('notif-panel');
        if (!btn || !panel) return;
        var sudahBaca = false;

        function tutup() { panel.classList.add('hidden'); btn.setAttribute('aria-expanded', 'false'); }
        function buka() {
            panel.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
            if (sudahBaca) return;
            sudahBaca = true;
            // tandai sudah dibaca di server, lalu hapus lencana merah
            fetch('{{ route('siswa.notifikasi.baca') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(function () {
                document.querySelectorAll('.js-notif-badge').forEach(function (el) { el.remove(); });
            }).catch(function () { sudahBaca = false; });
        }
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            panel.classList.contains('hidden') ? buka() : tutup();
        });
        document.addEventListener('click', function (e) { if (!wrap.contains(e.target)) tutup(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });
        if (location.hash === '#notif') buka();
        window.addEventListener('hashchange', function () { if (location.hash === '#notif') buka(); });
    })();
</script>
@endsection