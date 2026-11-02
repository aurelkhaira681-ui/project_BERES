@extends('layouts.app')
@section('title', 'Lupa Kata Sandi - BERES')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>.login-font { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }</style>

@php
    $role = old('role', 'siswa');
    $field = 'w-full rounded-xl border border-slate-200 bg-white pl-11 py-3 text-sm shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/25';
    $ico = [
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'id'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.5-1.5 2-2 3-2s2.5.5 3 2M14 10h4M14 14h4"/>',
        'key'  => '<circle cx="8" cy="15" r="4"/><path d="M11 12l9-9M16 7l3 3"/>',
        'lock' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
    ];
@endphp

<div class="login-font min-h-screen flex items-center justify-center px-4 py-10 bg-gradient-to-br from-[#eaf2ff] to-[#f6f9ff]">
    <div class="w-full max-w-md">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 mb-5 rounded-xl bg-white border border-slate-100 shadow-sm px-4 py-2 text-sm font-bold text-[#1e3f7f] hover:bg-[#f0f5ff] transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
            Kembali
        </a>
        <div class="flex items-center gap-3 mb-6">
            @include('partials.logo-brand', ['class' => 'w-12'])
            <div>
                <p class="font-extrabold text-2xl tracking-tight text-[#142b4e] leading-none">BERES</p>
                <p class="text-xs text-slate-500 mt-1">Sistem Pengaduan Sarana Sekolah</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_24px_rgba(20,50,105,0.08)] p-6 sm:p-8">
            <h1 class="text-2xl font-extrabold tracking-tight text-[#142b4e]">Lupa Kata Sandi</h1>
            <p class="text-slate-500 text-sm mt-1.5 mb-6">Verifikasi identitasmu, lalu buat kata sandi baru.</p>

            @if($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
                    <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                {{-- jenis akun --}}
                <div class="grid grid-cols-2 gap-1 rounded-xl bg-[#eef3fb] p-1 text-sm font-bold">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="siswa" class="sr-only peer" {{ $role === 'siswa' ? 'checked' : '' }}>
                        <span class="block text-center rounded-lg py-2 text-slate-500 peer-checked:bg-[#1c407a] peer-checked:text-white">Siswa</span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="admin" class="sr-only peer" {{ $role === 'admin' ? 'checked' : '' }}>
                        <span class="block text-center rounded-lg py-2 text-slate-500 peer-checked:bg-[#1c407a] peer-checked:text-white">Admin / Petugas</span>
                    </label>
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-[#142b4e] mb-1.5">Email</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ico['mail'] !!}</svg>
                        <input id="email" type="email" name="email" required value="{{ old('email') }}" autocomplete="email" placeholder="Email akunmu" class="{{ $field }} pr-3">
                    </div>
                </div>

                <div id="box-siswa">
                    <label for="nisn" class="block text-sm font-bold text-[#142b4e] mb-1.5">NISN</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ico['id'] !!}</svg>
                        <input id="nisn" name="nisn" inputmode="numeric" value="{{ old('nisn') }}" autocomplete="off" placeholder="NISN yang kamu daftarkan" class="{{ $field }} pr-3">
                    </div>
                </div>

                <div id="box-admin" class="hidden">
                    <label for="kode_registrasi" class="block text-sm font-bold text-[#142b4e] mb-1.5">Kode Registrasi Petugas</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ico['key'] !!}</svg>
                        <input id="kode_registrasi" type="password" name="kode_registrasi" autocomplete="off" placeholder="Kode dari pihak sekolah" class="{{ $field }} pr-11">
                        @include('partials.password-toggle')
                    </div>
                    @unless($petugasAktif)
                        <p class="text-xs text-amber-600 mt-1.5">Kode registrasi petugas belum diaktifkan di server, jadi reset untuk petugas belum bisa dilakukan.</p>
                    @endunless
                </div>

                @foreach([['password', 'Kata Sandi Baru'], ['password_confirmation', 'Konfirmasi Kata Sandi Baru']] as [$nama, $label])
                    <div>
                        <label for="{{ $nama }}" class="block text-sm font-bold text-[#142b4e] mb-1.5">{{ $label }}</label>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ico['lock'] !!}</svg>
                            <input id="{{ $nama }}" type="password" name="{{ $nama }}" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" class="{{ $field }} pr-11">
                            @include('partials.password-toggle')
                        </div>
                    </div>
                @endforeach

                <button class="w-full rounded-xl bg-[#16305c] hover:bg-[#0f2547] active:scale-[.99] transition text-white font-bold py-3.5 shadow-lg shadow-blue-900/20">Ubah Kata Sandi</button>

                <p class="text-center text-sm text-slate-500">
                    Ingat kata sandimu? <a href="{{ route('login') }}" class="font-bold text-[#1e3f7f] hover:underline">Masuk</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var boxSiswa = document.getElementById('box-siswa');
    var boxAdmin = document.getElementById('box-admin');
    var nisn = document.getElementById('nisn');
    var kode = document.getElementById('kode_registrasi');

    function sesuaikan() {
        var admin = document.querySelector('input[name=role]:checked').value === 'admin';
        boxSiswa.classList.toggle('hidden', admin);
        boxAdmin.classList.toggle('hidden', !admin);
        nisn.disabled = admin; nisn.required = !admin;
        kode.disabled = !admin; kode.required = admin;
    }
    document.querySelectorAll('input[name=role]').forEach(function (r) { r.addEventListener('change', sesuaikan); });
    sesuaikan();
})();
</script>
@include('partials.password-toggle-script')
@endsection