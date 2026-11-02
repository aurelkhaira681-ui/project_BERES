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
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 w-64 max-w-[82vw] -translate-x-full transition-transform duration-200 ease-out
                  md:sticky md:top-0 md:h-screen md:max-w-none md:translate-x-0 md:z-auto
                  bg-gradient-to-b from-[#143569] to-[#0e2a58] text-white flex flex-col shrink-0 overflow-y-auto">

        <div class="flex items-center justify-between gap-2 px-5 pt-6 pb-5">
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

        <nav class="flex-1 px-3 mt-2 space-y-1.5">
            <p class="px-3 text-[11px] uppercase tracking-wider text-white/50 mb-2" data-i18n>{{ $menuTitle }}</p>
            @foreach($nav as [$route, $label, $icon, $active])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-[15px] font-medium transition {{ $active ? ($u->isAdmin() ? 'bg-[#1f6fe5] text-white shadow-[0_6px_16px_rgba(31,111,229,.35)]' : 'bg-white/[.12] text-white') : 'text-white/85 hover:bg-white/5' }}"
                   @if($active) aria-current="page" @endif>
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">{!! $navIcons[$icon] !!}</svg>
                    <span data-i18n>{{ $label }}</span>
                    @if($route === 'siswa.riwayat' && ($notifSiswa ?? 0) > 0)
                        <span class="js-notif-badge ml-auto min-w-[20px] h-5 px-1.5 rounded-full bg-[#ef4b43] text-white text-[11px] font-bold flex items-center justify-center" title="Aduan diperbarui dalam 7 hari terakhir">{{ $notifSiswa }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="px-3 pb-3">
            @csrf
            <button class="w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-[15px] font-medium text-left text-white/85 hover:bg-white/5">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                <span data-i18n>Keluar</span>
            </button>
        </form>

        @php $kartuUser = 'px-5 py-4 border-t border-white/10 flex items-center gap-3'; @endphp
        @if($u->isAdmin())
        <a href="{{ route('admin.akun') }}" class="{{ $kartuUser }} hover:bg-white/5 transition">
        @else
        <div class="{{ $kartuUser }}">
        @endif
            <div class="w-10 h-10 shrink-0 rounded-full bg-[#2f5aa0] flex items-center justify-center text-sm font-bold">{{ $initials }}</div>
            <div class="text-sm leading-tight min-w-0">
                <p class="font-semibold truncate">{{ $u->name }}</p>
                <p class="text-white/60 text-xs truncate mt-0.5">{{ $caption }}</p>
            </div>
        @if($u->isAdmin())
        </a>
        @else
        </div>
        @endif
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
</body>
</html>