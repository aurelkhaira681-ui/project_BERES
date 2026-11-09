@extends('layouts.app')
@section('title', 'Masuk - BERES')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .login-font { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
</style>

<div class="login-font min-h-screen grid grid-cols-1 lg:grid-cols-2">

    {{-- ================= PANEL KIRI ================= --}}
    @include('partials.auth-panel')

    {{-- ================= PANEL KANAN: FORM ================= --}}
    <div class="relative flex items-center justify-center overflow-hidden bg-[#f6f9fd] px-5 py-10 sm:px-10">
        <div class="absolute -top-40 -right-32 w-[420px] h-[420px] rounded-full bg-[#e3ecfb]"></div>
        <div class="absolute -bottom-52 -right-40 w-[460px] h-[460px] rounded-full bg-[#e9f0fc]"></div>

        <div class="relative w-full max-w-xl" data-stagger>

            {{-- brand (HP & tablet, saat panel kiri disembunyikan) --}}
            <div class="lg:hidden flex items-center gap-3 mb-8">
                @include('partials.logo-brand', ['class' => 'w-16', 'tone' => 'dark'])
                <div>
                    <p class="text-3xl font-extrabold tracking-tight text-[#142b4e] leading-none">BERES</p>
                    <p class="text-sm text-slate-500 mt-1">Sistem Pengaduan Sarana Sekolah</p>
                </div>
            </div>

            <x-card class="p-6 sm:p-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#142b4e] leading-tight">Masuk ke Akun</h2>
                <p class="text-slate-500 text-base mt-1.5 mb-6" data-typewriter data-start="700">Pantau & ajukan aduan sarana sekolahmu di sini.</p>

                @if(session('status'))
                    <div class="mb-5 rounded-xl bg-green-50 text-green-700 border border-green-200 px-4 py-3 text-sm">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="mb-5 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- pilih peran --}}
                    <div class="grid grid-cols-2 mb-7 rounded-full bg-[#eef2f9] border border-slate-200/70 text-sm sm:text-base font-semibold">
                        <label class="flex items-center justify-center gap-2 rounded-full py-3.5 cursor-pointer text-slate-500 transition
                                      has-[:checked]:bg-[#1e3f7f] has-[:checked]:text-white has-[:checked]:shadow-md has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-[#4a86e8]">
                            <input type="radio" name="login_as" value="siswa" class="sr-only" {{ old('login_as', 'siswa') === 'siswa' ? 'checked' : '' }}>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-6 8-6s8 2 8 6z"/></svg>
                            Siswa
                        </label>
                        <label class="flex items-center justify-center gap-2 rounded-full py-3.5 cursor-pointer text-slate-500 transition
                                      has-[:checked]:bg-[#1e3f7f] has-[:checked]:text-white has-[:checked]:shadow-md has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-[#4a86e8]">
                            <input type="radio" name="login_as" value="admin" class="sr-only" {{ old('login_as') === 'admin' ? 'checked' : '' }}>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/><path d="M12 7v5M12 15v.5" stroke="#eef2f9" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
                            Admin / Petugas
                        </label>
                    </div>

                    {{-- NISN (siswa) / email (admin) --}}
                    <label for="identitas" id="identitas-label" class="block font-bold text-[#142b4e] mb-2">NISN</label>
                    <div class="relative mb-6">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v.4l-9 5.6-9-5.6zM3 9l9 5.6L21 9v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        <input id="identitas" type="text" name="identitas" required value="{{ old('identitas') }}" autocomplete="username"
                               inputmode="numeric" placeholder="Masukkan NISN (10 digit)"
                               class="w-full rounded-xl border border-slate-200 bg-white pl-12 pr-4 py-3.5 shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/25">
                    </div>

                    {{-- kata sandi --}}
                    <label for="password" class="block font-bold text-[#142b4e] mb-2">Kata Sandi</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2zm2 0h6V7a3 3 0 0 0-6 0z"/></svg>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="Masukkan kata sandi"
                               class="w-full rounded-xl border border-slate-200 bg-white pl-12 pr-12 py-3.5 shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/25">
                        <button type="button" id="toggle-password" aria-label="Tampilkan kata sandi"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-500 hover:text-[#1e3f7f]">
                            <svg id="eye-off" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10 10 0 0 1 12 6c5 0 8.5 4 10 6-.6.8-1.6 2-3 3.1M6.5 7.6C4.4 9 2.9 10.9 2 12c1.5 2 5 6 10 6 1.5 0 2.8-.4 4-.9"/><path d="M9.9 10a3 3 0 0 0 4.1 4.1"/></svg>
                            <svg id="eye-on" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12c1.5-2 5-6 10-6s8.5 4 10 6c-1.5 2-5 6-10 6S3.5 14 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>

                    <div class="text-right mt-3 mb-7">
                        <a href="{{ route('password.request') }}" class="text-sm font-bold text-[#1e3f7f] hover:underline">Lupa kata sandi?</a>
                    </div>

                    <button class="w-full flex items-center justify-center gap-2 rounded-2xl bg-[#23529d] hover:bg-[#1a4285] active:scale-[.99] transition text-white text-lg font-bold py-4 shadow-lg shadow-blue-900/25">
                        Masuk
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>

                    @php
                    $urlPetugas = Route::has('register.petugas') ? route('register.petugas') : route('register');
                    @endphp
                    <p class="text-center text-[#5d7aa8] relative z-10">
                        Belum punya akun?
                        <a id="link-daftar" href="{{ route('register') }}"
                        data-siswa="{{ route('register') }}" data-admin="{{ $urlPetugas }}"
                        class="font-bold text-[#0f2d5c] relative z-10">Daftar</a>
                    </p>
                </form>
            </x-card>

        </div>
    </div>
</div>

<script>
    // "Daftar" mengikuti pilihan peran: Siswa -> biodata siswa, Admin/Petugas -> daftar petugas
    (function () {
        var link = document.getElementById('link-daftar');
        var idLabel = document.getElementById('identitas-label');
        var idInput = document.getElementById('identitas');
        function ubahPeran(peran) {
            var admin = peran === 'admin';
            link.href = link.dataset[admin ? 'admin' : 'siswa'];
            idLabel.textContent = admin ? 'Email' : 'NISN';
            idInput.placeholder = admin ? 'Masukkan email' : 'Masukkan NISN (10 digit)';
            idInput.inputMode = admin ? 'email' : 'numeric';
        }
        document.querySelectorAll('input[name="login_as"]').forEach(function (radio) {
            radio.addEventListener('change', function () { if (radio.checked) ubahPeran(radio.value); });
            if (radio.checked) ubahPeran(radio.value);
        });
    })();

    (function () {
        var input = document.getElementById('password');
        var btn = document.getElementById('toggle-password');
        var on = document.getElementById('eye-on');
        var off = document.getElementById('eye-off');
        btn.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            on.classList.toggle('hidden', !show);
            off.classList.toggle('hidden', show);
            btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    })();
</script>
@endsection