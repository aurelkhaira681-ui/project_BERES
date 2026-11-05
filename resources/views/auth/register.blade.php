@extends('layouts.app')
@section('title', 'Daftar - BERES')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .login-font { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
    .login-illus {
        -webkit-mask-image: linear-gradient(to right, transparent, #000 8%, #000 92%, transparent), linear-gradient(to bottom, transparent, #000 12%, #000 80%, transparent);
        -webkit-mask-composite: source-in;
        mask-image: linear-gradient(to right, transparent, #000 8%, #000 92%, transparent), linear-gradient(to bottom, transparent, #000 12%, #000 80%, transparent);
        mask-composite: intersect;
    }
    .login-card-float { animation: login-card-float 3.6s ease-in-out infinite; }
    @keyframes login-card-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    @media (prefers-reduced-motion: reduce) { .login-card-float { animation: none; } }
</style>

@php
    $icons = [
        'user'  => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-6 8-6s8 2 8 6z"/>',
        'card'  => '<path d="M3 6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zm4 3v2h4V9zm0 4v2h6v-2zm8-4v6h2V9z"/>',
        'cap'   => '<path d="M12 3 1 9l11 6 9-4.9V17h2V9zM5 13.2V17c0 1.7 3.1 3 7 3s7-1.3 7-3v-3.8l-7 3.8z"/>',
        'work'  => '<path d="M9 4a2 2 0 0 0-2 2v1H4a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-3V6a2 2 0 0 0-2-2zm0 2h6v1H9z"/>',
        'phone' => '<path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11 11 0 0 0 3.6.6 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.6 3.6a1 1 0 0 1-.25 1z"/>',
        'mail'  => '<path d="M3 6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v.4l-9 5.6-9-5.6zM3 9l9 5.6L21 9v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
        'lock'  => '<path d="M7 10V7a5 5 0 0 1 10 0v3h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2zm2 0h6V7a3 3 0 0 0-6 0z"/>',
    ];
    $inputCls = 'w-full rounded-xl border border-slate-200 bg-white pl-12 pr-4 py-3.5 shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/25';
@endphp

<div class="login-font min-h-screen grid grid-cols-1 lg:grid-cols-2">

    {{-- PANEL KIRI --}}
    @include('partials.panel-siswa')

    {{-- PANEL KANAN: FORM --}}
    <div class="relative flex items-start lg:items-center justify-center overflow-hidden bg-[#f6f9fd] px-5 py-10 sm:px-10">
        <div class="absolute -top-40 -right-32 w-[420px] h-[420px] rounded-full bg-[#e3ecfb] pointer-events-none"></div>
        <div class="absolute -bottom-52 -right-40 w-[460px] h-[460px] rounded-full bg-[#e9f0fc] pointer-events-none"></div>

        <div class="relative w-full max-w-xl">

            <div class="lg:hidden flex items-center gap-3 mb-8">
                @include('partials.logo-brand', ['class' => 'w-16', 'tone' => 'dark'])
                <div>
                    <p class="text-3xl font-extrabold tracking-tight text-[#142b4e] leading-none">BERES</p>
                    <p class="text-sm text-slate-500 mt-1">Sistem Pengaduan Sarana Sekolah</p>
                </div>
            </div>

            {{-- Tombol kembali ke halaman login --}}
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 mb-6 rounded-xl bg-white border border-slate-100 shadow-sm px-4 py-2 text-sm font-bold text-[#1e3f7f] hover:bg-[#f0f5ff] transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                Kembali
            </a>

            <h2 class="text-4xl sm:text-[44px] font-extrabold tracking-tight text-[#142b4e] leading-tight">Lengkapi Data Diri</h2>
            <p class="text-slate-500 text-base sm:text-lg mt-2 mb-8">Biodata Siswa</p>

            @if($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                @foreach ([
                    [['name', 'Nama Lengkap', 'user', 'text', 'Nama sesuai data sekolah', 'name']],
                    [['nisn', 'NISN (10 digit)', 'card', 'text', 'Contoh: 0012345678', 'username'], ['kelas', 'Kelas', 'cap', 'text', 'Contoh: XII RPL A', 'off']],
                    [['no_presensi', 'No. Presensi', 'card', 'text', 'Contoh: 12', 'off'], ['konsentrasi_keahlian', 'Konsentrasi Keahlian', 'work', 'text', 'Contoh: Rekayasa Perangkat Lunak', 'off']],
                    [['no_hp', 'No. HP / WhatsApp', 'phone', 'tel', 'Contoh: 081234567890', 'tel']],
                    [['email', 'Email', 'mail', 'email', 'nama@siswa.sch.id', 'email']],
                ] as $row)
                    <div class="grid gap-5 {{ count($row) > 1 ? 'sm:grid-cols-2' : '' }}">
                        @foreach ($row as [$name, $label, $icon, $type, $ph, $ac])
                            <div>
                                <label for="{{ $name }}" class="block font-bold text-[#142b4e] mb-2">{{ $label }}</label>
                                <div class="relative">
                                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" viewBox="0 0 24 24" fill="currentColor">{!! $icons[$icon] !!}</svg>
                                    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name) }}" required
                                           autocomplete="{{ $ac }}" placeholder="{{ $ph }}" class="{{ $inputCls }}"
                                           @if($name === 'nisn') inputmode="numeric" maxlength="10" @endif>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                {{-- kata sandi --}}
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach ([['password', 'Kata Sandi'], ['password_confirmation', 'Konfirmasi Kata Sandi']] as [$name, $label])
                        <div>
                            <label for="{{ $name }}" class="block font-bold text-[#142b4e] mb-2">{{ $label }}</label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" viewBox="0 0 24 24" fill="currentColor">{!! $icons['lock'] !!}</svg>
                                <input id="{{ $name }}" type="password" name="{{ $name }}" required autocomplete="new-password"
                                       placeholder="{{ $name === 'password' ? 'Minimal 8 karakter' : 'Ulangi kata sandi' }}"
                                       class="w-full rounded-xl border border-slate-200 bg-white pl-12 pr-12 py-3.5 shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/25">
                                <button type="button" data-toggle="{{ $name }}" aria-label="Tampilkan kata sandi"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-500 hover:text-[#1e3f7f]">
                                    <svg data-eye-off class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10 10 0 0 1 12 6c5 0 8.5 4 10 6-.6.8-1.6 2-3 3.1M6.5 7.6C4.4 9 2.9 10.9 2 12c1.5 2 5 6 10 6 1.5 0 2.8-.4 4-.9"/><path d="M9.9 10a3 3 0 0 0 4.1 4.1"/></svg>
                                    <svg data-eye-on class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12c1.5-2 5-6 10-6s8.5 4 10 6c-1.5 2-5 6-10 6S3.5 14 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="w-full flex items-center justify-center gap-2 rounded-2xl bg-[#23529d] hover:bg-[#1a4285] active:scale-[.99] transition text-white text-lg font-bold py-4 shadow-lg shadow-blue-900/25">
                    Lanjutkan
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>

                <p class="text-center text-[#5d7aa8] relative z-10">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-bold text-[#0f2d5c] relative z-10">Masuk</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[data-toggle]').forEach(function (btn) {
        var input = document.getElementById(btn.dataset.toggle);
        btn.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('[data-eye-on]').classList.toggle('hidden', !show);
            btn.querySelector('[data-eye-off]').classList.toggle('hidden', show);
        });
    });
</script>
@endsection