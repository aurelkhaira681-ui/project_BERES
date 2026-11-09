@extends('layouts.app')
@section('title', 'Daftar Petugas - BERES')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .reg-font { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
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

<div class="reg-font min-h-screen grid grid-cols-1 lg:grid-cols-2">

    {{-- ================= PANEL KIRI ================= --}}
    @include('partials.auth-panel')

    {{-- ================= PANEL KANAN: FORM ================= --}}
    <div class="relative flex items-center justify-center overflow-hidden bg-[#f6f9fd] px-5 py-8 sm:px-10">
        <div class="absolute -top-40 -right-32 w-[380px] h-[380px] rounded-full bg-[#e8eefb]"></div>
        <div class="absolute -bottom-48 -right-40 w-[420px] h-[420px] rounded-full bg-[#edf2fc]"></div>

        <div class="relative w-full max-w-xl">

            {{-- Tombol kembali ke halaman login --}}
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 mb-6 rounded-xl bg-white border border-slate-100 shadow-sm px-4 py-2 text-sm font-bold text-[#1e3f7f] hover:bg-[#f0f5ff] transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                Kembali
            </a>

            <x-card class="p-6 sm:p-8">
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
                </form>
            </x-card>
        </div>
    </div>
</div>
@include('partials.password-toggle-script')
@endsection