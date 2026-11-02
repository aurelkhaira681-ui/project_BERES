@extends('layouts.dashboard')
@section('title', 'Beranda - BERES')
@section('content')

@php
    $card = 'bg-white rounded-2xl border border-slate-100 shadow-[0_2px_14px_rgba(20,50,105,0.06)]';
    $now  = now('Asia/Jakarta')->locale('id');
    $jam  = $now->hour;
    $sapaan = $jam < 11 ? 'pagi' : ($jam < 15 ? 'siang' : ($jam < 18 ? 'sore' : 'malam'));
    $namaDepan = explode(' ', trim(auth()->user()->name))[0];

    $statCards = [
        ['Total Aduan',          $stats['total'],    'bg-[#dcebff] text-[#1d6fd8]', '<path d="M3 10v4a1 1 0 0 0 1 1h2l5 4V5L6 9H4a1 1 0 0 0-1 1zm13.5 2a4.5 4.5 0 0 0-2.5-4v8a4.5 4.5 0 0 0 2.5-4zM14 3.2v2.1a7 7 0 0 1 0 13.4v2.1a9 9 0 0 0 0-17.6z"/>'],
        ['Menunggu Verifikasi',  $stats['menunggu'], 'bg-[#ffefcf] text-[#e29a10]', '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 5v5.2l3.6 2.1-.8 1.3L11 13V7z"/>'],
        ['Sedang Diproses',      $stats['diproses'], 'bg-[#dcebff] text-[#1d6fd8]', '<path d="M19.4 13a7.7 7.7 0 0 0 0-2l2.1-1.6-2-3.5-2.5 1a7.4 7.4 0 0 0-1.7-1L15 3h-4l-.4 2.9a7.4 7.4 0 0 0-1.7 1l-2.5-1-2 3.5L6.6 11a7.7 7.7 0 0 0 0 2l-2.1 1.6 2 3.5 2.5-1a7.4 7.4 0 0 0 1.7 1L11 21h4l.4-2.9a7.4 7.4 0 0 0 1.7-1l2.5 1 2-3.5zM13 15.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7z"/>'],
        ['Selesai',              $stats['selesai'],  'bg-[#d9f4e5] text-[#15935a]', '<path d="M9.5 16.6 5.4 12.5l-1.4 1.4 5.5 5.5L21 7.9l-1.4-1.4z" stroke="currentColor" stroke-width="1.2"/>'],
    ];

    $filterOpsi = ['semua' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Diproses', 'selesai' => 'Selesai', 'rejected' => 'Ditolak'];
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#0f2d5c]">Selamat {{ $sapaan }}, {{ $namaDepan }} 👋</h1>
        <p class="text-[#5d7aa8] mt-1">Berikut ringkasan aduan sarana yang sudah kamu ajukan</p>
    </div>
    <div class="inline-flex items-center gap-2 rounded-xl bg-[#e9f0fb] px-4 py-2.5 text-sm font-semibold text-[#0f2d5c]">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
        {{ $now->translatedFormat('l, d F Y') }}
    </div>
</div>

{{-- Statistik --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-5">
    @foreach($statCards as [$label, $angka, $warna, $ikon])
        <div class="{{ $card }} p-4 sm:p-5">
            <div class="flex items-center gap-3">
                <span class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center {{ $warna }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">{!! $ikon !!}</svg>
                </span>
                <p class="text-xs sm:text-[13px] font-semibold text-[#0f2d5c] leading-tight">{{ $label }}</p>
            </div>
            <p class="mt-3 text-right text-3xl font-bold text-[#1d4f9c] leading-none">{{ $angka }} <span class="text-xs font-normal text-slate-400">aduan</span></p>
        </div>
    @endforeach
</div>

{{-- Tombol ajukan --}}
<a href="{{ route('siswa.aduan.create') }}"
   class="flex items-center justify-between gap-3 rounded-xl bg-gradient-to-r from-[#0f3a7a] to-[#0b4fa3] text-white px-5 sm:px-6 py-4 mb-6 shadow-md shadow-blue-900/20 hover:opacity-95 transition">
    <span class="font-semibold text-sm sm:text-[15px]"><span class="mr-2 font-bold">+</span>Ajukan Aduan Baru — laporkan kerusakan fasilitas sekolah</span>
    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
</a>

<div class="grid lg:grid-cols-3 gap-5 sm:gap-6">

    {{-- Riwayat aduan --}}
    <div class="{{ $card }} p-5 sm:p-6 lg:col-span-2 flex flex-col">
        <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <h2 class="font-bold text-[#0f2d5c]">Riwayat Aduan</h2>
            <select onchange="location.href='{{ route('siswa.dashboard') }}?status=' + this.value"
                    class="rounded-lg bg-[#eef3fb] border-0 text-sm font-medium text-[#0f2d5c] py-2 pl-3 pr-8 cursor-pointer focus:ring-2 focus:ring-[#1e3f7f]/25 outline-none">
                @foreach($filterOpsi as $key => $label)
                    <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @forelse($riwayat as $item)
            <a href="{{ route('siswa.aduan.show', $item) }}" class="flex items-center gap-3 sm:gap-4 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50/70 -mx-2 px-2 rounded-lg transition">
                <span class="shrink-0 w-11 h-11 rounded-xl bg-[#eef3fb] flex items-center justify-center text-lg">{{ $item->kategori->icon ?? '🧰' }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block font-semibold text-sm text-[#0f2d5c] truncate">{{ Str::limit($item->deskripsi, 48) }}</span>
                    <span class="block text-xs text-slate-400 mt-0.5 truncate">#{{ $item->kode_tiket }} · {{ $item->lokasi->nama_lengkap }} · {{ $item->tanggal_kejadian->format('d M Y') }}</span>
                </span>
                @include('partials.status-badge', ['item' => $item])
            </a>
        @empty
            <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
                <svg viewBox="0 0 120 100" class="w-32 h-28" aria-hidden="true">
                    <ellipse cx="60" cy="58" rx="46" ry="34" fill="#e6f0fd"/>
                    <circle cx="20" cy="26" r="3" fill="#cfe1fb"/><circle cx="104" cy="30" r="2.5" fill="#cfe1fb"/>
                    <path d="M38 18h30l14 14v42a4 4 0 0 1-4 4H38a4 4 0 0 1-4-4V22a4 4 0 0 1 4-4z" fill="#fff" stroke="#2f6fd6" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M68 18v14h14" fill="none" stroke="#2f6fd6" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M42 44h22M42 53h30M42 62h18" stroke="#6ea6ee" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="80" cy="72" r="12" fill="#2f6fd6" stroke="#fff" stroke-width="3"/>
                    <path d="M74.5 72l4 4 7-8" stroke="#fff" stroke-width="2.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @if($status === 'semua')
                    <p class="mt-3 font-bold text-[#0f2d5c]">Belum ada aduan yang diajukan</p>
                    <p class="text-xs text-slate-400 mt-1">Klik tombol di atas untuk melapor kerusakan fasilitas sekolah.</p>
                    <a href="{{ route('siswa.aduan.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-[#0f3a7a] hover:bg-[#0b2f66] text-white text-sm font-semibold px-5 py-2.5 transition">
                        <span class="font-bold">+</span> Ajukan aduan pertama
                    </a>
                @else
                    <p class="mt-3 font-bold text-[#0f2d5c]">Tidak ada aduan dengan status ini</p>
                    <p class="text-xs text-slate-400 mt-1">Coba pilih status lain di pojok kanan atas.</p>
                @endif
            </div>
        @endforelse

        @if($riwayat->isNotEmpty())
            <a href="{{ route('siswa.riwayat') }}" class="mt-4 self-end text-sm font-semibold text-[#1d4f9c] hover:underline">Lihat semua riwayat →</a>
        @endif
    </div>
</div>
@endsection