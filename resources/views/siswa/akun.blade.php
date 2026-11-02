@extends('layouts.dashboard')
@section('title', 'Akun Saya - BERES')
@section('content')

@php
    $card = 'bg-white rounded-2xl border border-slate-100 shadow-[0_2px_14px_rgba(20,50,105,0.06)]';
    $field = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/20';
    $editing = $errors->any();
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');

    $ico = [
        'user'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
        'id'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.5-1.5 2-2 3-2s2.5.5 3 2M14 10h4M14 14h4"/>',
        'cap'   => '<path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
        'mail'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'phone' => '<rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18h2"/>',
        'hash'  => '<path d="M5 9h14M5 15h14M10 4 8 20M16 4l-2 16"/>',
        'work'  => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>',
    ];

    $profil = [
        ['Nama Lengkap',          $user->name,                   'user'],
        ['NISN',                  $user->nisn,                   'id'],
        ['Kelas',                 $user->kelas,                  'cap'],
        ['No. Presensi',          $user->no_presensi,            'hash'],
        ['Konsentrasi Keahlian',  $user->konsentrasi_keahlian,   'work'],
        ['Email',                 $user->email,                  'mail'],
        ['No. HP',                $user->no_hp,                  'phone'],
    ];

    $preferensi = [
        ['Notifikasi', 'Atur notifikasi aduan', '<path d="M6 16V11a6 6 0 0 1 12 0v5l1.5 2h-15zM10 20a2 2 0 0 0 4 0"/>', 'text-slate-400'],
        ['Tema',       'Light Mode',            '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5z"/>', 'text-[#6a8fc9]'],
        ['Bahasa',     'Indonesia',             '<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 9h6M11 9c0 3-2 5-4 6M9 12c.7 1.4 2 2.5 4 3M15 15l2-5 2 5M15.8 13.5h2.4"/>', 'text-[#1d6fd8]'],
    ];
    $bantuan = [
        ['Pusat Bantuan',       'Pertanyaan yang sering diajukan', '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.7.4-1 .9-1 1.7M12 17v.1"/>'],
        ['Syarat & Ketentuan',  'Ketentuan penggunaan aplikasi',   '<path d="M7 3h8l4 4v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM9 12h6M9 16h6M9 8h3"/>'],
    ];
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#0f2d5c]">Akun Saya</h1>
        <p class="text-[#5d7aa8] mt-1">Kelola biodata & preferensi akun kamu</p>
    </div>
    <a href="#" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-100 shadow-[0_2px_14px_rgba(20,50,105,0.06)] px-4 py-2.5 text-sm font-semibold text-[#0f2d5c] hover:bg-slate-50">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2zm2 0h6V7a3 3 0 0 0-6 0z"/></svg>
        Ubah Password
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-5 sm:gap-6 items-start">

    {{-- Informasi profil --}}
    <div class="{{ $card }} p-5 sm:p-6 lg:col-span-2">
        <div class="flex items-start justify-between gap-3">
            <h2 class="font-bold text-[#0f2d5c]">Informasi Profil</h2>
            <button type="button" id="btn-edit" class="{{ $editing ? 'hidden' : '' }} rounded-lg bg-[#e6effb] hover:bg-[#d8e5f8] px-5 py-2 text-sm font-bold text-[#0f2d5c] transition">Edit</button>
        </div>

        <div class="flex items-center gap-4 mt-4 mb-5">
            <div class="w-[72px] h-[72px] shrink-0 rounded-full bg-[#b9d0ee] text-[#0f2d5c] flex items-center justify-center text-3xl font-semibold">{{ $initials }}</div>
            <div class="min-w-0">
                <p class="font-bold text-lg text-[#0f2d5c] truncate">{{ $user->name }}</p>
                <p class="text-sm text-[#5d7aa8]">Siswa · Kelas {{ $user->kelas }}</p>
            </div>
        </div>

        {{-- Mode lihat --}}
        <div id="profil-view" class="{{ $editing ? 'hidden' : '' }}">
            @foreach($profil as [$label, $nilai, $ikon])
                <div class="flex items-start gap-3 py-3 text-sm">
                    <svg class="w-[18px] h-[18px] mt-0.5 shrink-0 text-[#5d7aa8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ico[$ikon] !!}</svg>
                    <span class="w-32 sm:w-44 shrink-0 text-[#40608f]">{{ $label }}</span>
                    <span class="min-w-0 break-words text-[#0f2d5c]">{{ $nilai ?: '-' }}</span>
                </div>
            @endforeach
        </div>

        {{-- Mode ubah --}}
        <form id="profil-edit" method="POST" action="{{ route('siswa.akun.update') }}" class="{{ $editing ? '' : 'hidden' }}">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
                    <ul class="list-disc pl-4">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                </div>
            @endif

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Nama Lengkap</label>
                    <input name="name" value="{{ old('name', $user->name) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Kelas</label>
                    <input name="kelas" value="{{ old('kelas', $user->kelas) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">No. Presensi</label>
                    <input name="no_presensi" value="{{ old('no_presensi', $user->no_presensi) }}" class="{{ $field }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Konsentrasi Keahlian</label>
                    <input name="konsentrasi_keahlian" value="{{ old('konsentrasi_keahlian', $user->konsentrasi_keahlian) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">No. HP</label>
                    <input name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="{{ $field }}">
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mt-5">
                <button class="rounded-xl bg-[#0f3a7a] hover:bg-[#0b2f66] text-white text-sm font-bold px-6 py-2.5 transition">Simpan Perubahan</button>
                <button type="button" id="btn-batal" class="rounded-xl bg-[#e6effb] hover:bg-[#d8e5f8] text-[#0f2d5c] text-sm font-bold px-6 py-2.5 transition">Batal</button>
            </div>
        </form>
    </div>

    {{-- Kolom kanan --}}
    <div class="space-y-5 sm:space-y-6">
        <div class="{{ $card }} p-5 sm:p-6">
            <h2 class="font-bold text-[#0f2d5c] mb-2">Preferensi</h2>
            <div class="divide-y divide-slate-100">
                @foreach($preferensi as [$judul, $sub, $ikon, $warna])
                    <a href="#" class="flex items-center gap-3 py-3.5 hover:bg-slate-50/70 -mx-2 px-2 rounded-lg transition">
                        <svg class="w-5 h-5 shrink-0 {{ $warna }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ikon !!}</svg>
                        <span class="flex-1 min-w-0">
                            <span class="block text-sm font-semibold text-[#0f2d5c]">{{ $judul }}</span>
                            <span class="block text-xs text-slate-400">{{ $sub }}</span>
                        </span>
                        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                    </a>
                @endforeach
            </div>
        </div>

        </div>
    </div>
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
    })();
</script>
@endsection