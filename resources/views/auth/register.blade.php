@extends('layouts.app')
@section('title', 'Daftar - BERES')
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
    // ikon (gaya garis) untuk kolom input
    $icons = [
        'user'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
        'id'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.5-1.5 2-2 3-2s2.5.5 3 2M14 10h4M14 14h4"/>',
        'cap'   => '<path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
        'work'  => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'mail'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'lock'  => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
    ];

    // name, label, ikon, placeholder, tipe, lebar penuh?, atribut tambahan
    $fields = [
        ['name' => 'name',                  'label' => 'Nama Lengkap',          'icon' => 'user',  'ph' => 'Ditulis sesuai data sekolah','type' => 'text',     'full' => true,  'ac' => 'name'],
        ['name' => 'nisn',                  'label' => 'NISN (10 digit)',        'icon' => 'id',    'ph' => 'Contoh: 0086944390',                    'type' => 'text',     'full' => false, 'ac' => 'off', 'mode' => 'numeric'],
        ['name' => 'kelas',                 'label' => 'Kelas',                 'icon' => 'cap',   'ph' => 'XII RPL B',               'type' => 'text',     'full' => false, 'ac' => 'off'],
        ['name' => 'no_presensi',           'label' => 'No. Presensi',          'icon' => 'id',    'ph' => '11',                      'type' => 'text',     'full' => false, 'ac' => 'off', 'mode' => 'numeric'],
        ['name' => 'konsentrasi_keahlian',  'label' => 'Konsentrasi Keahlian',  'icon' => 'work',  'ph' => 'Rekayasa Perangkat Lunak','type' => 'text',     'full' => false, 'ac' => 'off'],
        ['name' => 'no_hp',                 'label' => 'No. HP / WhatsApp',     'icon' => 'phone', 'ph' => '0812-3456-7890',          'type' => 'text',     'full' => true,  'ac' => 'tel', 'mode' => 'tel'],
        ['name' => 'email',                 'label' => 'Email',                 'icon' => 'mail',  'ph' => 'nama@siswa.sch.id',       'type' => 'email',    'full' => true,  'ac' => 'email'],
        ['name' => 'password',              'label' => 'Kata Sandi',            'icon' => 'lock',  'ph' => '••••••••',                'type' => 'password', 'full' => false, 'ac' => 'new-password'],
        ['name' => 'password_confirmation', 'label' => 'Konfirmasi Kata Sandi', 'icon' => 'lock',  'ph' => '••••••••',                'type' => 'password', 'full' => false, 'ac' => 'new-password'],
    ];
@endphp

<div class="reg-font min-h-screen grid grid-cols-1 md:grid-cols-2">

    {{-- ================= PANEL KIRI ================= --}}
    @include('partials.reg-panel', [
        'judul'      => 'Buat Akun Siswa',
        'sub'        => 'Lengkapi biodata kamu sekali saja — dipakai otomatis setiap kali mengajukan aduan.',
        'langkah'    => ['Daftar', 'Masuk', 'Lapor'],
        'floatJudul' => 'Akun siap dipakai',
        'floatKet'   => 'Yuk, mulai melapor!',
        'chip'       => 'Status aduan: Diproses',
        'fitur'      => [
            ['card', 'Biodata Tersimpan', 'Nama, kelas, & kontak otomatis terisi di setiap tiket.'],
            ['lock', 'Akun Terverifikasi', 'Terhubung dengan data induk siswa sekolah.'],
            ['bolt', 'Pantau Real-time', 'Lihat status aduanmu dari menunggu sampai selesai.'],
            ['bell', 'Notifikasi Status', 'Dapat lencana saat aduanmu diperbarui petugas.'],
        ],
    ])

    {{-- ================= PANEL KANAN: FORM ================= --}}
    <div class="relative flex items-center justify-center overflow-hidden bg-[#f6f9fd] px-5 py-8 sm:px-10">
        <div class="absolute -top-40 -right-32 w-[380px] h-[380px] rounded-full bg-[#e8eefb]"></div>
        <div class="absolute -bottom-48 -right-40 w-[420px] h-[420px] rounded-full bg-[#edf2fc]"></div>

        <div class="relative w-full max-w-xl">

            {{-- tombol kembali (HP) --}}
            <a href="{{ route('login') }}" class="md:hidden inline-flex items-center gap-2 mb-6 -ml-1 px-3 py-2 rounded-lg text-[#16305c] font-semibold hover:bg-slate-100">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                Kembali
            </a>

            <h2 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#142b4e] leading-tight">Lengkapi Data Diri</h2>
            <p class="text-slate-500 text-sm mt-1 mb-6">Biodata Siswa</p>

            @if($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                @csrf

                @foreach($fields as $f)
                    <div class="{{ $f['full'] ? 'sm:col-span-2' : '' }}">
                        <label for="{{ $f['name'] }}" class="block text-sm font-bold text-[#142b4e] mb-1.5">{{ $f['label'] }}</label>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$f['icon']] !!}</svg>
                            <input id="{{ $f['name'] }}" name="{{ $f['name'] }}" type="{{ $f['type'] }}" required
                                   @if($f['type'] !== 'password') value="{{ old($f['name']) }}" @endif
                                   autocomplete="{{ $f['ac'] }}"
                                   @if(!empty($f['mode'])) inputmode="{{ $f['mode'] }}" @endif
                                   placeholder="{{ $f['ph'] }}"
                                   class="w-full rounded-xl border border-slate-200 bg-white pl-11 {{ $f['type'] === 'password' ? 'pr-11' : 'pr-3' }} py-3 text-sm shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/25">
                            @if($f['type'] === 'password')
                                @include('partials.password-toggle')
                            @endif
                        </div>
                    </div>
                @endforeach

                <button class="sm:col-span-2 mt-2 flex items-center justify-center gap-2 rounded-xl bg-[#16305c] hover:bg-[#0f2547] active:scale-[.99] transition text-white font-bold py-3.5 shadow-lg shadow-blue-900/20">
                    Lanjutkan
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>

                <p class="sm:col-span-2 text-center text-sm text-slate-500">
                    Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-[#1e3f7f] hover:underline">Masuk</a>
                </p>
            </form>
        </div>
    </div>
</div>
@include('partials.password-toggle-script')
@endsection