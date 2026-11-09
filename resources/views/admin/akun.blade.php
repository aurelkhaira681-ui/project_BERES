@extends('layouts.dashboard')
@section('title', 'Akun Saya - BERES')
@section('content')

@php
    $field = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/20';
    $editing = $errors->any();
    $gantiPw = $errors->password->any();
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');

    $ico = [
        'user'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
        'mail'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'phone' => '<rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18h2"/>',
        'work'  => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>',
    ];

    $profil = [
        ['Nama Lengkap', $user->name,  'user'],
        ['Peran',        'Admin / Petugas', 'work'],
        ['Email',        $user->email, 'mail'],
        ['No. HP',       $user->no_hp, 'phone'],
    ];

    $preferensi = [
        ['Notifikasi', 'Atur notifikasi aduan', '<path d="M6 16V11a6 6 0 0 1 12 0v5l1.5 2h-15zM10 20a2 2 0 0 0 4 0"/>', 'text-slate-400'],
        ['Tema',       'Light Mode',            '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5z"/>', 'text-[#6a8fc9]'],
        ['Bahasa',     'Indonesia',             '<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 9h6M11 9c0 3-2 5-4 6M9 12c.7 1.4 2 2.5 4 3M15 15l2-5 2 5M15.8 13.5h2.4"/>', 'text-[#1d6fd8]'],
    ];
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#0f2d5c]" data-i18n>Akun Saya</h1>
        <p class="text-[#5d7aa8] mt-1" data-i18n>Kelola biodata & preferensi akun kamu</p>
    </div>
    <button type="button" id="btn-pw" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-100 shadow-[0_2px_14px_rgba(20,50,105,0.06)] px-4 py-2.5 text-sm font-semibold text-[#0f2d5c] hover:bg-slate-50">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2zm2 0h6V7a3 3 0 0 0-6 0z"/></svg>
        <span data-i18n>Ubah Password</span>
    </button>
</div>

{{-- Ubah password --}}
<form id="form-pw" method="POST" action="{{ route('admin.akun.password') }}" class="{{ $card }} p-5 sm:p-6 mb-5 sm:mb-6 {{ $gantiPw ? '' : 'hidden' }}">
    @csrf @method('PUT')
    <h2 class="font-bold text-[#0f2d5c] mb-4" data-i18n>Ubah Password</h2>
    @if($errors->password->any())
        <div class="mb-4 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
            <ul class="list-disc pl-4">@foreach($errors->password->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="grid sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5" data-i18n>Password Saat Ini</label>
            <input type="password" name="current_password" autocomplete="current-password" class="{{ $field }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5" data-i18n>Password Baru</label>
            <input type="password" name="password" autocomplete="new-password" class="{{ $field }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5" data-i18n>Ulangi Password Baru</label>
            <input type="password" name="password_confirmation" autocomplete="new-password" class="{{ $field }}">
        </div>
    </div>
    <div class="flex flex-wrap gap-3 mt-5">
        <button class="rounded-xl bg-[#0f3a7a] hover:bg-[#0b2f66] text-white text-sm font-bold px-6 py-2.5 transition" data-i18n>Simpan Password</button>
        <button type="button" id="btn-pw-batal" class="rounded-xl bg-[#e6effb] hover:bg-[#d8e5f8] text-[#0f2d5c] text-sm font-bold px-6 py-2.5 transition" data-i18n>Batal</button>
    </div>
</form>

<div class="grid lg:grid-cols-3 gap-5 sm:gap-6 items-start">

    {{-- Informasi profil --}}
    <div class="{{ $card }} p-5 sm:p-6 lg:col-span-2">
        <div class="flex items-start justify-between gap-3">
            <h2 class="font-bold text-[#0f2d5c]" data-i18n>Informasi Profil</h2>
            <button type="button" id="btn-edit" class="{{ $editing ? 'hidden' : '' }} rounded-lg bg-[#e6effb] hover:bg-[#d8e5f8] px-5 py-2 text-sm font-bold text-[#0f2d5c] transition" data-i18n>Edit</button>
        </div>

        <div class="flex items-center gap-4 mt-4 mb-5">
            @include('partials.foto-avatar', ['user' => $user, 'initials' => $initials])
            <div class="min-w-0">
                <p class="font-bold text-lg text-[#0f2d5c] truncate">{{ $user->name }}</p>
                <p class="text-sm text-[#5d7aa8]">Admin / Petugas</p>
            </div>
        </div>

        {{-- Mode lihat --}}
        <div id="profil-view" class="{{ $editing ? 'hidden' : '' }}">
            @foreach($profil as [$label, $nilai, $ikon])
                <div class="flex items-start gap-3 py-3 text-sm">
                    <svg class="w-[18px] h-[18px] mt-0.5 shrink-0 text-[#5d7aa8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ico[$ikon] !!}</svg>
                    <span class="w-32 sm:w-44 shrink-0 text-[#40608f]" data-i18n>{{ $label }}</span>
                    <span class="min-w-0 break-words text-[#0f2d5c]">{{ $nilai ?: '-' }}</span>
                </div>
            @endforeach
        </div>

        {{-- Mode ubah --}}
        <form id="profil-edit" method="POST" action="{{ route('admin.akun.update') }}" class="{{ $editing ? '' : 'hidden' }}">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
                    <ul class="list-disc pl-4">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                </div>
            @endif

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5" data-i18n>Nama Lengkap</label>
                    <input name="name" value="{{ old('name', $user->name) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5" data-i18n>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5" data-i18n>No. HP</label>
                    <input name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="{{ $field }}">
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mt-5">
                <button class="rounded-xl bg-[#0f3a7a] hover:bg-[#0b2f66] text-white text-sm font-bold px-6 py-2.5 transition" data-i18n>Simpan Perubahan</button>
                <button type="button" id="btn-batal" class="rounded-xl bg-[#e6effb] hover:bg-[#d8e5f8] text-[#0f2d5c] text-sm font-bold px-6 py-2.5 transition" data-i18n>Batal</button>
            </div>
        </form>
    </div>

    {{-- Preferensi --}}
    @include('partials.preferensi')
</div>

<script>
    (function () {
        var view = document.getElementById('profil-view');
        var form = document.getElementById('profil-edit');
        var btnEdit = document.getElementById('btn-edit');
        var btnBatal = document.getElementById('btn-batal');
        function setEdit(on) {
            view.classList.toggle('hidden', on);
            form.classList.toggle('hidden', !on);
            btnEdit.classList.toggle('hidden', on);
        }
        btnEdit.addEventListener('click', function () { setEdit(true); });
        btnBatal.addEventListener('click', function () { setEdit(false); });

        var pw = document.getElementById('form-pw');
        document.getElementById('btn-pw').addEventListener('click', function () { pw.classList.toggle('hidden'); });
        document.getElementById('btn-pw-batal').addEventListener('click', function () { pw.classList.add('hidden'); });
    })();
</script>
@endsection