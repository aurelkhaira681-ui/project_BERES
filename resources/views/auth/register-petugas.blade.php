@extends('layouts.app')
@section('title', 'Daftar Petugas - BERES')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .reg-font { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
    .reg-illus {
        -webkit-mask-image: linear-gradient(to right, transparent, #000 10%, #000 88%, transparent), linear-gradient(to bottom, transparent, #000 14%, #000 78%, transparent);
        -webkit-mask-composite: source-in;
        mask-image: linear-gradient(to right, transparent, #000 10%, #000 88%, transparent), linear-gradient(to bottom, transparent, #000 14%, #000 78%, transparent);
        mask-composite: intersect;
    }
</style>

@php
    $icons = [
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'key'  => '<circle cx="8" cy="15" r="4"/><path d="M11 12l9-9M16 7l3 3M14 9l2 2"/>',
        'lock' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
    ];

    // name, label, ikon, placeholder, tipe, lebar penuh?, autocomplete
    $fields = [
        ['name' => 'name',                  'label' => 'Nama Lengkap',                'icon' => 'user', 'ph' => 'Bpk. Sutrisno',           'type' => 'text',     'full' => true,  'ac' => 'name'],
        ['name' => 'email',                 'label' => 'Email',                       'icon' => 'mail', 'ph' => 'nama@smkn5solo.sch.id',   'type' => 'email',    'full' => true,  'ac' => 'email'],
        ['name' => 'kode_registrasi',       'label' => 'Kode Registrasi Petugas',     'icon' => 'key',  'ph' => 'Kode dari pihak sekolah', 'type' => 'password', 'full' => true,  'ac' => 'off'],
        ['name' => 'password',              'label' => 'Kata Sandi',                  'icon' => 'lock', 'ph' => '••••••••',                'type' => 'password', 'full' => false, 'ac' => 'new-password'],
        ['name' => 'password_confirmation', 'label' => 'Konfirmasi Kata Sandi',       'icon' => 'lock', 'ph' => '••••••••',                'type' => 'password', 'full' => false, 'ac' => 'new-password'],
    ];
@endphp

<div class="reg-font min-h-screen grid grid-cols-1 md:grid-cols-2">

    {{-- ================= PANEL KIRI ================= --}}
    <div class="hidden md:flex relative flex-col justify-between gap-8 overflow-hidden bg-gradient-to-br from-[#173a76] via-[#1d478c] to-[#2a5aa8] text-white p-8 xl:p-12">
        <div class="absolute top-6 right-8 w-9 h-9 rounded-full bg-[#4a7bd0]/40"></div>
        <div class="absolute -bottom-28 -right-20 w-80 h-60 rounded-full bg-white/5 rotate-[-12deg]"></div>

        <div class="relative">
            <a href="{{ route('login') }}" aria-label="Kembali ke halaman masuk" title="Kembali"
               class="mb-5 inline-flex w-11 h-11 items-center justify-center rounded-lg bg-white/15 hover:bg-white/25 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
            </a>
            <h1 class="text-2xl xl:text-3xl font-extrabold tracking-tight">Buat Akun Petugas</h1>
            <p class="text-white/75 mt-2 max-w-xs text-sm xl:text-base leading-relaxed">Akun petugas dipakai untuk memverifikasi dan menindaklanjuti aduan sarana sekolah.</p>
        </div>

        <div class="relative grid grid-cols-5 items-center gap-3">
            <div class="col-span-3 space-y-6">
                <div class="flex gap-3">
                    <svg class="w-6 h-6 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z" fill="#5c9cf2"/><path d="M8.5 12l2.5 2.5 4.5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <div>
                        <p class="font-bold text-[13px] xl:text-[15px] leading-tight">Verifikasi Aduan</p>
                        <p class="text-white/70 text-xs xl:text-[13px] mt-1 leading-relaxed">Setujui, tolak, dan tandai aduan siswa yang sudah selesai.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <svg class="w-6 h-6 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none"><path d="M8 11V7.5a4 4 0 0 1 8 0V11" stroke="#d99a1f" stroke-width="2.2" stroke-linecap="round"/><rect x="4.5" y="10.5" width="15" height="10.5" rx="2.5" fill="#f5a623"/><circle cx="12" cy="15.2" r="1.5" fill="#8a5a00"/><path d="M12 16v2" stroke="#8a5a00" stroke-width="1.6" stroke-linecap="round"/></svg>
                    <div>
                        <p class="font-bold text-[13px] xl:text-[15px] leading-tight">Akses Terbatas</p>
                        <p class="text-white/70 text-xs xl:text-[13px] mt-1 leading-relaxed">Pendaftaran memerlukan kode registrasi dari pihak sekolah.</p>
                    </div>
                </div>
            </div>

            <div class="col-span-2">
                <div class="reg-illus aspect-[9/5] overflow-hidden">
                    <img src="{{ asset('images/login-illustration.jpg') }}" alt="" class="h-full w-auto max-w-none select-none" draggable="false">
                </div>
            </div>
        </div>

        <p class="relative text-white/50 text-xs xl:text-sm">© {{ date('Y') }} SMK Negeri 5 Surakarta</p>
    </div>

    {{-- ================= PANEL KANAN: FORM ================= --}}
    <div class="relative flex items-center justify-center overflow-hidden bg-[#f6f9fd] px-5 py-8 sm:px-10">
        <div class="absolute -top-40 -right-32 w-[380px] h-[380px] rounded-full bg-[#e8eefb]"></div>
        <div class="absolute -bottom-48 -right-40 w-[420px] h-[420px] rounded-full bg-[#edf2fc]"></div>

        <div class="relative w-full max-w-xl">

            <a href="{{ route('login') }}" class="md:hidden inline-flex items-center gap-2 mb-6 -ml-1 px-3 py-2 rounded-lg text-[#16305c] font-semibold hover:bg-slate-100">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                Kembali
            </a>

            <h2 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#142b4e] leading-tight">Daftar Admin / Petugas</h2>
            <p class="text-slate-500 text-sm mt-1 mb-6">Akun pengelola aduan sarana sekolah</p>

            @unless($aktif)
                <div class="mb-5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 px-4 py-3 text-sm">
                    Pendaftaran petugas belum diaktifkan. Hubungi pengelola sistem untuk mengisi <code class="font-semibold">PETUGAS_REGISTRATION_CODE</code> di file .env.
                </div>
            @endunless

            @if($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.petugas') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                @csrf

                <fieldset {{ $aktif ? '' : 'disabled' }} class="contents">
                    @foreach($fields as $f)
                        <div class="{{ $f['full'] ? 'sm:col-span-2' : '' }}">
                            <label for="{{ $f['name'] }}" class="block text-sm font-bold text-[#142b4e] mb-1.5">{{ $f['label'] }}</label>
                            <div class="relative">
                                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$f['icon']] !!}</svg>
                                <input id="{{ $f['name'] }}" name="{{ $f['name'] }}" type="{{ $f['type'] }}" required
                                       @if(in_array($f['name'], ['name', 'email'])) value="{{ old($f['name']) }}" @endif
                                       autocomplete="{{ $f['ac'] }}"
                                       placeholder="{{ $f['ph'] }}"
                                       class="w-full rounded-xl border border-slate-200 bg-white pl-11 {{ $f['type'] === 'password' ? 'pr-11' : 'pr-3' }} py-3 text-sm shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/25 disabled:bg-slate-50 disabled:cursor-not-allowed">
                                @if($f['type'] === 'password')
                                    @include('partials.password-toggle')
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <button class="sm:col-span-2 mt-2 flex items-center justify-center gap-2 rounded-xl bg-[#16305c] hover:bg-[#0f2547] active:scale-[.99] transition text-white font-bold py-3.5 shadow-lg shadow-blue-900/20 disabled:opacity-60 disabled:cursor-not-allowed">
                        Daftar sebagai Petugas
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </fieldset>

                </p>
            </form>
        </div>
    </div>
</div>
@include('partials.password-toggle-script')
@endsection