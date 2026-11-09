@extends('layouts.dashboard')
@section('title', 'Riwayat Aduan - BERES')
@section('content')

@php
    $tabs = ['semua' => 'Semua', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];

    // nomor halaman yang ditampilkan (jendela ±2 dari halaman aktif)
    $now  = $aduan->currentPage();
    $last = $aduan->lastPage();
    $from = max(1, $now - 2);
    $to   = min($last, $now + 2);
@endphp

{{-- Header + pencarian --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#0f2d5c]">Riwayat Aduan</h1>
        <p class="text-[#5d7aa8] mt-1">Pantau status aduan yang telah kamu kirim</p>
    </div>

    <form method="GET" action="{{ route('siswa.riwayat') }}" class="w-full sm:w-80 relative">
        @if($status !== 'semua') <input type="hidden" name="status" value="{{ $status }}"> @endif
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#4a86e8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari berdasarkan nomor tiket, fasilitas..."
               class="w-full rounded-xl border border-slate-100 bg-white pl-11 pr-4 py-3 text-sm shadow-[0_2px_14px_rgba(20,50,105,0.06)] outline-none placeholder:text-slate-400 focus:ring-2 focus:ring-[#1e3f7f]/20">
    </form>
</div>

<div class="{{ $card }} overflow-hidden">

    {{-- Tab filter --}}
    <div class="flex flex-wrap gap-2.5 p-5 sm:p-6">
        @foreach($tabs as $key => $label)
            <a href="{{ route('siswa.riwayat', array_filter(['status' => $key === 'semua' ? null : $key, 'q' => $q ?: null])) }}"
               class="px-5 py-2 rounded-xl text-sm font-semibold border transition
                      {{ $status === $key ? 'bg-[#0f3a7a] text-white border-[#0f3a7a] shadow-md shadow-blue-900/15' : 'bg-white text-[#0f2d5c] border-slate-200 hover:border-[#9db8e0]' }}">{{ $label }}</a>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="tbl-stack w-full text-sm min-w-[680px]">
            <thead>
                <tr class="bg-[#f1f5fc] text-left text-[#0f2d5c]">
                    <th class="font-bold px-5 sm:px-6 py-3.5">No. Tiket</th>
                    <th class="font-bold px-3 py-3.5">Fasilitas</th>
                    <th class="font-bold px-3 py-3.5">Tanggal</th>
                    <th class="font-bold px-3 py-3.5">Status</th>
                    <th class="font-bold px-5 sm:px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aduan as $item)
                    <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition">
                        <td class="px-5 sm:px-6 py-4 font-semibold text-[#1d4f9c] whitespace-nowrap">
                            <a href="{{ route('siswa.aduan.show', $item) }}" class="hover:underline">#{{ $item->kode_tiket }}</a>
                        </td>
                        <td class="px-3 py-4">
                            <p class="font-medium text-[#0f2d5c]">{{ Str::limit($item->deskripsi, 38) }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $item->kategori->icon ?? '' }} {{ $item->kategori->nama ?? '-' }} · {{ $item->lokasi->nama_ruangan ?? '-' }}</p>
                        </td>
                        <td class="px-3 py-4 text-[#0f2d5c] whitespace-nowrap">{{ $item->tanggal_kejadian->format('d M Y, H:i') }}</td>
                        <td class="px-3 py-4">@include('partials.status-badge', ['item' => $item])</td>
                        <td class="px-5 sm:px-6 py-4 text-right">
                            <a href="{{ route('siswa.aduan.show', $item) }}" aria-label="Lihat detail {{ $item->kode_tiket }}" class="inline-flex p-1.5 rounded-lg text-[#0f2d5c] hover:bg-[#eef3fb]">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-14 text-center text-slate-400">
                            {{ $q !== '' ? 'Tidak ada aduan yang cocok dengan pencarianmu.' : 'Tidak ada aduan dengan status ini.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Info + halaman --}}
    <div class="flex flex-wrap items-center justify-between gap-4 px-5 sm:px-6 py-5">
        <p class="text-sm text-[#5d7aa8]">
            @if($aduan->total() > 0)
                Menampilkan {{ $aduan->firstItem() }}–{{ $aduan->lastItem() }} dari {{ $aduan->total() }} data
            @else
                Menampilkan 0 data
            @endif
        </p>

        @if($aduan->hasPages())
            <div class="flex items-center gap-1.5">
                @if($aduan->onFirstPage())
                    <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-100 text-slate-300">‹</span>
                @else
                    <a href="{{ $aduan->previousPageUrl() }}" aria-label="Halaman sebelumnya" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 text-[#0f2d5c] hover:bg-[#eef3fb]">‹</a>
                @endif

                @for($p = $from; $p <= $to; $p++)
                    <a href="{{ $aduan->url($p) }}"
                       class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-sm font-semibold {{ $p === $now ? 'bg-[#0f3a7a] text-white shadow-md shadow-blue-900/15' : 'border border-slate-200 text-[#0f2d5c] hover:bg-[#eef3fb]' }}">{{ $p }}</a>
                @endfor

                @if($aduan->hasMorePages())
                    <a href="{{ $aduan->nextPageUrl() }}" aria-label="Halaman berikutnya" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 text-[#0f2d5c] hover:bg-[#eef3fb]">›</a>
                @else
                    <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-100 text-slate-300">›</span>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection