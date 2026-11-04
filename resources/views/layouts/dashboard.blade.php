<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BERES')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pref-head')
    <style>body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }</style>
</head>
<body class="{{ auth()->user()->isAdmin() ? 'bg-gradient-to-br from-[#eaf2ff] via-[#f3f7fd] to-[#eaf1fc]' : 'bg-[#f4f7fc]' }} text-slate-800 min-h-screen md:flex">

@php
    $u = auth()->user();
    $initials = collect(explode(' ', $u->name))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');

    // ikon sidebar (isi solid, warna mengikuti teks)
    $navIcons = [
        'home'   => '<path d="M12 3 2.5 11.5H5V20h5v-5.5h4V20h5v-8.5h2.5z"/>',
        'doc'    => '<path d="M6 2h8l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm7 1.5V8h4.5z"/>',
        'ticket' => '<path d="M3 6h18a1 1 0 0 1 1 1v3a2 2 0 0 0 0 4v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-3a2 2 0 0 0 0-4V7a1 1 0 0 1 1-1z"/>',
        'user'   => '<circle cx="12" cy="8" r="4.5"/><path d="M3.5 21c0-4.5 4-7 8.5-7s8.5 2.5 8.5 7z"/>',
        'grid'   => '<path d="M3 3h8v8H3zm10 0h8v5h-8zM3 13h8v8H3zm10-3h8v11h-8z"/>',
        'folder' => '<path d="M3 5a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
    ];

    if ($u->isSiswa()) {
        $menuTitle = 'Menu Saya';
        $nav = [
            ['siswa.dashboard',     'Beranda',        'home',   request()->routeIs('siswa.dashboard')],
            ['siswa.aduan.create',  'Ajukan Aduan',   'doc',    request()->routeIs('siswa.aduan.create')],
            ['siswa.riwayat',       'Riwayat Aduan',  'ticket', request()->routeIs('siswa.riwayat') || request()->routeIs('siswa.aduan.show')],
            ['siswa.akun',          'Akun Saya',      'user',   request()->routeIs('siswa.akun')],
        ];
        $caption = 'Siswa · ' . $u->kelas;
        $notifSiswa = $u->aduan()->where('status', '!=', 'pending')->where('updated_at', '>=', now()->subDays(7))->count();
    } else {
        $menuTitle = 'Menu Utama';
        $nav = [
            ['admin.dashboard',     'Dashboard',      'grid',   request()->routeIs('admin.dashboard')],
            ['admin.aduan.index',   'Data Aduan',     'ticket', request()->routeIs('admin.aduan.*')],
            ['admin.master-data',   'Master Data',    'folder', request()->routeIs('admin.master-data')],
            ['admin.akun',          'Akun Saya',      'user',   request()->routeIs('admin.akun')],
        ];
        $caption = 'Admin / Petugas';
    }
@endphp

    {{-- TOP BAR KHUSUS HP --}}
    <div class="md:hidden fixed top-0 inset-x-0 z-30 h-14 bg-[#12305f] text-white flex items-center gap-3 px-4 shadow">
        <button id="menu-open" type="button" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false" class="p-2 -ml-2 rounded-lg hover:bg-white/10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        @include('partials.logo-brand', ['class' => 'w-9'])
        <span class="font-extrabold text-lg tracking-tight">BERES</span>
    </div>

    <div id="sidebar-overlay" class="hidden md:hidden fixed inset-0 z-40 bg-black/50"></div>

    {{-- SIDEBAR --}}
    @php
        $fotoUrl = $u->fotoUrl();
        if ($u->isSiswa()) {
            $sbMenunggu = $u->aduan()->where('status', 'pending')->count();
            $sbDiproses = $u->aduan()->where('status', 'approved')->count();
            $sbAktif = $sbMenunggu + $sbDiproses;
        } else {
            $sbAktif = \App\Models\Aduan::where('status', 'pending')->count();
        }
        $sbSparkle = '<path d="M12 2l2.2 7.8L22 12l-7.8 2.2L12 22l-2.2-7.8L2 12l7.8-2.2z"/>';
    @endphp
    <style>
        @keyframes sbTwinkle { 0%, 100% { opacity: .15; transform: scale(.7) rotate(0); } 50% { opacity: .85; transform: scale(1.15) rotate(20deg); } }
        .sb-spark { position: absolute; fill: #fff; animation: sbTwinkle 4s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) { .sb-spark { animation: none; opacity: .3; } }
    </style>
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 w-64 max-w-[82vw] -translate-x-full transition-transform duration-200 ease-out
                  md:sticky md:top-0 md:h-screen md:max-w-none md:translate-x-0 md:z-auto
                  bg-gradient-to-b from-[#16408a] via-[#12346b] to-[#0b2250] text-white flex flex-col shrink-0 overflow-y-auto">

        {{-- dekorasi latar: cahaya, pola titik, bintang berkelip --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
            <div class="absolute -top-24 -left-20 w-72 h-72 rounded-full" style="background: radial-gradient(circle, rgba(86,160,255,.50), transparent 65%)"></div>
            <div class="absolute bottom-16 -right-28 w-72 h-72 rounded-full" style="background: radial-gradient(circle, rgba(130,110,255,.30), transparent 65%)"></div>
            <div class="absolute inset-0 opacity-[.10]" style="background-image: radial-gradient(#fff 1px, transparent 1.3px); background-size: 18px 18px; -webkit-mask-image: linear-gradient(to bottom, #000, transparent 60%); mask-image: linear-gradient(to bottom, #000, transparent 60%)"></div>
            <svg class="sb-spark w-3 h-3" style="left: 78%; top: 6%" viewBox="0 0 24 24">{!! $sbSparkle !!}</svg>
            <svg class="sb-spark w-2.5 h-2.5" style="left: 88%; top: 15%; animation-delay: -1.3s" viewBox="0 0 24 24">{!! $sbSparkle !!}</svg>
            <svg class="sb-spark w-2 h-2" style="left: 62%; top: 22%; animation-delay: -2.4s" viewBox="0 0 24 24">{!! $sbSparkle !!}</svg>
            <svg class="sb-spark w-3 h-3" style="left: 10%; top: 58%; animation-delay: -3s" viewBox="0 0 24 24">{!! $sbSparkle !!}</svg>
            <svg class="sb-spark w-2 h-2" style="left: 90%; top: 70%; animation-delay: -.8s" viewBox="0 0 24 24">{!! $sbSparkle !!}</svg>
        </div>

        <div class="relative flex items-center justify-between gap-2 px-5 pt-6 pb-5">
            <div class="flex items-center gap-3 min-w-0">
                @include('partials.logo-brand', ['class' => 'w-12'])
                <div class="min-w-0">
                    <p class="font-extrabold text-xl tracking-tight leading-none">BERES</p>
                    <p class="text-[10px] leading-tight text-white/70 mt-1">Sistem Pengaduan Sarana Sekolah</p>
                </div>
            </div>
            <button id="menu-close" type="button" aria-label="Tutup menu" class="md:hidden p-1.5 -mr-1 rounded-lg hover:bg-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        <div class="relative flex-1 px-3 mt-1">
            <nav class="space-y-1.5">
                <p class="px-3 text-[11px] uppercase tracking-wider text-white/50 mb-2" data-i18n>{{ $menuTitle }}</p>
                @foreach($nav as [$route, $label, $icon, $active])
                    <a href="{{ route($route) }}"
                       class="group relative flex items-center gap-3 pl-2.5 pr-3 py-2 rounded-xl text-[15px] font-medium transition duration-200 {{ $active ? 'text-white shadow-[0_8px_20px_rgba(10,50,150,.45)]' : 'text-white/80 hover:text-white hover:bg-white/[.07] hover:translate-x-0.5' }}"
                       @if($active) style="background: linear-gradient(90deg, rgba(47,124,246,.95), rgba(31,111,229,.40))" aria-current="page" @endif>
                        @if($active)<span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-[#8cc4ff]"></span>@endif
                        <span class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center transition {{ $active ? 'bg-white/20' : 'bg-white/[.07] group-hover:bg-white/15' }}">
                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="currentColor">{!! $navIcons[$icon] !!}</svg>
                        </span>
                        <span data-i18n>{{ $label }}</span>
                        @if($route === 'siswa.riwayat' && ($notifSiswa ?? 0) > 0)
                            <span class="js-notif-badge ml-auto min-w-[20px] h-5 px-1.5 rounded-full bg-[#ef4b43] text-white text-[11px] font-bold flex items-center justify-center" title="Aduan diperbarui dalam 7 hari terakhir">{{ $notifSiswa }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- kartu ringkasan cepat --}}
            <div class="relative mt-5 rounded-2xl border border-white/15 bg-white/[.08] p-4 overflow-hidden">
                <div class="absolute -top-10 -right-10 w-28 h-28 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(120,190,255,.45), transparent 70%)"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 text-[11px] uppercase tracking-wider text-white/65">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-[#ffb340] opacity-70 animate-ping"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-[#ffb340]"></span>
                        </span>
                        <span data-i18n>{{ $u->isSiswa() ? 'Aduan Aktif' : 'Menunggu Verifikasi' }}</span>
                    </div>
                    <p class="mt-2 text-3xl font-extrabold leading-none" data-count="{{ $sbAktif }}">{{ $sbAktif }}</p>
                    @if($u->isSiswa())
                        <p class="text-xs text-white/65 mt-1.5">{{ $sbMenunggu }} <span data-i18n>menunggu</span> · {{ $sbDiproses }} <span data-i18n>diproses</span></p>
                    @else
                        <p class="text-xs text-white/65 mt-1.5" data-i18n>aduan perlu ditinjau</p>
                    @endif
                    <a href="{{ $u->isSiswa() ? route('siswa.aduan.create') : route('admin.aduan.index', ['status' => 'pending']) }}"
                       class="mt-3 flex items-center justify-center gap-1.5 rounded-lg bg-[#f2f7ff] hover:bg-[#dcebff] text-[#12336a] text-[13px] font-bold py-2 transition">
                        <span data-i18n>{{ $u->isSiswa() ? 'Ajukan Sekarang' : 'Tinjau Sekarang' }}</span>
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="relative px-3 pt-4 pb-3">
            @csrf
            <button class="w-full group flex items-center gap-3 pl-2.5 pr-3 py-2 rounded-xl text-[15px] font-medium text-left text-white/80 hover:text-white hover:bg-[#ef4b43]/20 transition">
                <span class="w-9 h-9 shrink-0 rounded-lg bg-white/[.07] group-hover:bg-[#ef4b43]/30 flex items-center justify-center transition">
                    <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                </span>
                <span data-i18n>Keluar</span>
            </button>
        </form>

        {{-- kartu pengguna (klik = buka Akun Saya) --}}
        <a href="{{ route($u->isAdmin() ? 'admin.akun' : 'siswa.akun') }}" class="group relative px-5 py-4 border-t border-white/10 flex items-center gap-3 hover:bg-white/5 transition">
            <span class="relative shrink-0">
                <span class="block w-11 h-11 rounded-full p-[2px]" style="background: linear-gradient(135deg, #5aa7ff, #a5e3ff)">
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="" class="w-full h-full rounded-full object-cover bg-[#12336a]">
                    @else
                        <span class="w-full h-full rounded-full bg-[#2f5aa0] flex items-center justify-center text-sm font-bold">{{ $initials }}</span>
                    @endif
                </span>
                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-[#22c55e] border-2 border-[#0e2a58]"></span>
            </span>
            <div class="text-sm leading-tight min-w-0 flex-1">
                <p class="font-semibold truncate">{{ $u->name }}</p>
                <p class="text-white/60 text-xs truncate mt-0.5">{{ $caption }}</p>
            </div>
            <svg class="w-4 h-4 shrink-0 text-white/40 group-hover:text-white/80 group-hover:translate-x-0.5 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
        </a>
    </aside>

    {{-- KONTEN --}}
    <div class="flex-1 min-w-0 flex flex-col pt-14 md:pt-0">
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-5 rounded-xl bg-green-50 text-green-700 border border-green-200 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 rounded-xl bg-red-50 text-red-700 border border-red-200 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
        (function () {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            var btnOpen = document.getElementById('menu-open');
            var btnClose = document.getElementById('menu-close');

            function openMenu() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                btnOpen.setAttribute('aria-expanded', 'true');
            }
            function closeMenu() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                btnOpen.setAttribute('aria-expanded', 'false');
            }
            btnOpen.addEventListener('click', openMenu);
            btnClose.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMenu(); });
            window.matchMedia('(min-width: 768px)').addEventListener('change', function (e) { if (e.matches) closeMenu(); });
        })();
    </script>
    @include('partials.animasi')
</body>
</html>