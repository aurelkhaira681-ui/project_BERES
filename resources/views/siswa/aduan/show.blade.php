@extends('layouts.dashboard')
@section('title', $aduan->kode_tiket . ' - BERES')
@section('content')

<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold">{{ Str::limit($aduan->deskripsi, 50) }}</h1>
        <p class="text-slate-500 text-sm">Tiket #{{ $aduan->kode_tiket }} · Diajukan {{ $aduan->created_at->format('d M Y, H.i') }}</p>
    </div>
    @include('partials.status-badge', ['item' => $aduan])
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border rounded-xl p-5">
            <h2 class="font-semibold mb-4">Progres Penanganan</h2>
            <ul class="space-y-4 text-sm">
                <li class="flex gap-3">
                    <span class="w-2.5 h-2.5 mt-1.5 rounded-full bg-[#16305c]"></span>
                    <div><p class="font-medium">Aduan Diajukan</p><p class="text-slate-400">{{ $aduan->created_at->format('d M Y · H.i') }}</p></div>
                </li>
                <li class="flex gap-3">
                    <span class="w-2.5 h-2.5 mt-1.5 rounded-full {{ $aduan->diverifikasi_at ? 'bg-[#16305c]' : 'bg-slate-200' }}"></span>
                    <div>
                        <p class="font-medium {{ !$aduan->diverifikasi_at ? 'text-slate-400' : '' }}">Diverifikasi Admin</p>
                        <p class="text-slate-400">{{ $aduan->diverifikasi_at?->format('d M Y · H.i') ?? 'Menunggu verifikasi' }}</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="w-2.5 h-2.5 mt-1.5 rounded-full {{ $aduan->status === 'selesai' ? 'bg-[#16305c]' : 'bg-slate-200' }}"></span>
                    <div>
                        <p class="font-medium {{ $aduan->status !== 'selesai' ? 'text-slate-400' : '' }}">Perbaikan Selesai</p>
                        <p class="text-slate-400">{{ $aduan->selesai_at?->format('d M Y · H.i') ?? 'Menunggu tindak lanjut' }}</p>
                    </div>
                </li>
            </ul>
        </div>

        <div class="bg-white border rounded-xl p-5">
            <h2 class="font-semibold mb-4">Detail Kerusakan</h2>
            <div class="grid grid-cols-1 min-[420px]:grid-cols-2 gap-4 mb-4 text-sm [&>div]:min-w-0 [&_p]:break-words">
                <div><p class="text-slate-400 text-xs uppercase mb-1">Kategori</p><p class="font-medium">{{ $aduan->kategori->icon }} {{ $aduan->kategori->nama }}</p></div>
                <div><p class="text-slate-400 text-xs uppercase mb-1">Lokasi</p><p class="font-medium">{{ $aduan->lokasi->nama_lengkap }}</p></div>
                <div><p class="text-slate-400 text-xs uppercase mb-1">Teknisi</p><p class="font-medium">{{ $aduan->teknisi?->nama ?? 'Ditentukan petugas' }}@if($aduan->teknisi) <span class="text-xs text-slate-400 font-normal">· {{ \App\Models\Teknisi::singkat($aduan->teknisi->jurusan) }}</span>@endif</p></div>
            </div>
            <p class="text-sm text-slate-600 mb-4">{{ $aduan->deskripsi }}</p>

            @if(!empty($aduan->foto))
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($aduan->foto as $foto)
                        <img src="{{ asset('storage/' . $foto) }}" class="rounded-lg h-28 w-full object-cover">
                    @endforeach
                </div>
            @endif

            @if($aduan->catatan_admin)
                <div class="mt-4 rounded-lg bg-slate-50 border p-3 text-sm">
                    <p class="text-xs uppercase text-slate-400 mb-1">Catatan Admin</p>
                    {{ $aduan->catatan_admin }}
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white border rounded-xl p-5">
            <h2 class="font-semibold mb-3">Data Pelapor</h2>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-sm font-semibold">
                    {{ collect(explode(' ', $aduan->user->name))->map(fn($w) => $w[0])->take(2)->implode('') }}
                </div>
                <div><p class="font-medium text-sm">{{ $aduan->user->name }}</p><p class="text-xs text-slate-400">{{ $aduan->user->kelas }} · No. Absen {{ $aduan->user->no_presensi }}</p></div>
            </div>
            <div class="text-sm space-y-1 border-t pt-3">
                <p class="flex justify-between gap-3"><span class="text-slate-400">NISN</span><span class="font-medium">{{ $aduan->user->nisn }}</span></p>
                <p class="flex justify-between gap-3"><span class="text-slate-400">No. HP</span><span class="font-medium">{{ $aduan->user->no_hp }}</span></p>
            </div>
        </div>

        @if($aduan->status === 'pending')
            <form method="POST" action="{{ route('siswa.aduan.batalkan', $aduan) }}" onsubmit="return confirm('Batalkan pengajuan ini?')">
                @csrf @method('DELETE')
                <button class="w-full bg-white border border-red-200 text-red-600 rounded-lg py-2.5 font-medium hover:bg-red-50">
                    Batalkan Pengajuan
                </button>
            </form>
        @endif
    </div>
</div>
@endsection