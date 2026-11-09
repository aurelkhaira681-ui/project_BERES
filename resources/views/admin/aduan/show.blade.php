@extends('layouts.dashboard')
@section('title', $aduan->kode_tiket . ' - BERES')
@section('content')

<a href="{{ route('admin.aduan.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#1f6fe5] mb-3">← Kembali ke Data Aduan</a>
<h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0f2d5c]">{{ Str::limit($aduan->deskripsi, 50) }}</h1>
<p class="text-slate-500 text-sm mb-6">Diajukan oleh {{ $aduan->user->name }} · {{ $aduan->user->kelas }} · {{ $aduan->created_at->format('d M Y, H.i') }}</p>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="{{ $card }} p-5">
            <h2 class="font-semibold mb-4">Detail Kerusakan</h2>
            <div class="grid grid-cols-1 min-[420px]:grid-cols-2 gap-4 mb-4 text-sm [&>div]:min-w-0 [&_p]:break-words">
                <div><p class="text-slate-400 text-xs uppercase mb-1">Kategori</p><p class="font-medium">{{ $aduan->kategori->icon }} {{ $aduan->kategori->nama }}</p></div>
                <div><p class="text-slate-400 text-xs uppercase mb-1">Lokasi</p><p class="font-medium">{{ $aduan->lokasi->nama_lengkap }}</p></div>
                <div><p class="text-slate-400 text-xs uppercase mb-1">Teknisi</p><p class="font-medium">{{ $aduan->teknisi?->nama ?? 'Belum ditunjuk' }}</p></div>
                <div><p class="text-slate-400 text-xs uppercase mb-1">Tgl Kejadian</p><p class="font-medium">{{ $aduan->tanggal_kejadian->format('d M Y, H.i') }}</p></div>
                <div><p class="text-slate-400 text-xs uppercase mb-1">Status Saat Ini</p>@include('partials.admin-status-badge', ['item' => $aduan])</div>
            </div>
            <p class="text-sm text-slate-600 mb-4">{{ $aduan->deskripsi }}</p>
            @if(!empty($aduan->foto))
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($aduan->foto as $foto)
                        <img src="{{ asset('storage/' . $foto) }}" class="rounded-lg h-28 w-full object-cover">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="{{ $card }} p-5">
            <h2 class="font-semibold mb-3">Riwayat Status</h2>
            <p class="text-sm"><span class="text-amber-500">●</span> Menunggu verifikasi — {{ $aduan->created_at->format('d M Y, H.i') }}</p>
            @if($aduan->diverifikasi_at)
                <p class="text-sm mt-2"><span class="text-green-500">●</span> {{ $aduan->statusLabel() }} — {{ $aduan->diverifikasi_at->format('d M Y, H.i') }}</p>
            @endif
            @if($aduan->selesai_at)
                <p class="text-sm mt-2"><span class="text-blue-500">●</span> Selesai diperbaiki — {{ $aduan->selesai_at->format('d M Y, H.i') }}</p>
            @endif
        </div>
    </div>

    <div class="space-y-4">
        {{-- Penugasan teknisi --}}
        <div class="{{ $card }} p-5">
            <div class="flex items-center justify-between gap-2 mb-3">
                <h2 class="font-semibold">Penugasan Teknisi</h2>
                <span class="rounded-full bg-[#e6effb] text-[#1d4f9c] text-xs font-semibold px-2.5 py-1">{{ \App\Models\Teknisi::singkat($aduan->lokasi->jurusan) }}</span>
            </div>

            @if(in_array($aduan->status, ['pending', 'approved']))
                @if($calonTeknisi->isEmpty())
                    <p class="text-sm text-amber-600">Belum ada teknisi aktif. Aktifkan teknisi di Master Data.</p>
                @else
                    <form method="POST" action="{{ route('admin.aduan.teknisi', $aduan) }}">
                        @csrf
                        <div class="space-y-2">
                            @foreach($calonTeknisi as $t)
                                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5 cursor-pointer transition hover:bg-slate-50 has-[:checked]:border-[#1f6fe5] has-[:checked]:bg-[#e6effb]">
                                    <input type="radio" name="teknisi_id" value="{{ $t->id }}" class="accent-[#1f6fe5]" required
                                           @checked($aduan->teknisi_id ? $aduan->teknisi_id == $t->id : $loop->first)>
                                    <span class="flex-1 min-w-0">
                                        <span class="block text-sm font-semibold text-[#0f2d5c]">{{ $t->nama }}</span>
                                        <span class="block text-xs text-slate-400">{{ \App\Models\Teknisi::singkat($t->jurusan) }}@if(!$aduan->teknisi_id && $loop->first) · disarankan (beban teringan)@endif</span>
                                    </span>
                                    <span class="text-xs text-slate-500 whitespace-nowrap">{{ $t->aktif_count }} aktif</span>
                                </label>
                            @endforeach
                        </div>
                        @if($fallbackTeknisi)
                            <p class="text-xs text-amber-600 mt-2">Tidak ada teknisi aktif di jurusan lokasi ini, jadi semua teknisi aktif ditampilkan.</p>
                        @elseif(blank($aduan->lokasi->jurusan))
                            <p class="text-xs text-slate-400 mt-2">Lokasi umum: pilih teknisi mana pun.</p>
                        @else
                            <p class="text-xs text-slate-400 mt-2">Hanya teknisi jurusan lokasi ini yang ditampilkan.</p>
                        @endif
                        <button class="mt-3 w-full rounded-xl bg-[#1f6fe5] hover:bg-[#1a5ec4] text-white py-2.5 text-sm font-bold transition">
                            {{ $aduan->teknisi_id ? 'Ganti teknisi' : 'Simpan teknisi' }}
                        </button>
                    </form>
                @endif
            @else
                <p class="text-sm">{{ $aduan->teknisi?->nama ?? 'Tidak ada teknisi yang ditugaskan' }}</p>
            @endif
        </div>

        @if($aduan->status === 'pending')
            <div class="{{ $card }} p-5">
                <h2 class="font-semibold mb-3">Tindak Lanjut</h2>
                @if(!$aduan->teknisi_id)
                    <p class="text-xs text-amber-600 mb-2">Tugaskan teknisi terlebih dahulu sebelum menyetujui.</p>
                @endif
                <form method="POST" action="{{ route('admin.aduan.setujui', $aduan) }}" class="mb-3">
                    @csrf
                    <textarea name="catatan_admin" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm mb-2" placeholder="Contoh: Teknisi akan mengecek lokasi besok pagi."></textarea>
                    <button class="w-full bg-[#22b36b] text-white rounded-xl py-2.5 font-bold hover:bg-[#1c9a5c]">✓ Setujui</button>
                </form>
                <form method="POST" action="{{ route('admin.aduan.tolak', $aduan) }}" onsubmit="return confirm('Tolak aduan ini?')">
                    @csrf
                    <input type="hidden" name="catatan_admin" value="Aduan tidak dapat diverifikasi.">
                    <button class="w-full border border-red-200 text-red-600 rounded-xl py-2.5 font-bold hover:bg-red-50">✕ Tolak</button>
                </form>
            </div>
        @elseif($aduan->status === 'approved')
            <div class="{{ $card }} p-5">
                <h2 class="font-semibold mb-3">Tindak Lanjut</h2>
                <form method="POST" action="{{ route('admin.aduan.selesai', $aduan) }}">
                    @csrf
                    <button class="w-full bg-[#0f3a9a] text-white rounded-xl py-2.5 font-bold hover:bg-[#0b2f7e]">Tandai Selesai Diperbaiki</button>
                </form>
            </div>
        @endif

        @if($aduan->catatan_admin)
            <div class="{{ $card }} p-5 text-sm">
                <p class="text-xs uppercase text-slate-400 mb-1">Catatan Admin</p>
                {{ $aduan->catatan_admin }}
            </div>
        @endif

        <div class="{{ $card }} p-5">
            <h2 class="font-semibold mb-3">Data Pelapor</h2>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-sm font-semibold">
                    {{ collect(explode(' ', $aduan->user->name))->map(fn($w) => $w[0])->take(2)->implode('') }}
                </div>
                <div><p class="font-medium text-sm">{{ $aduan->user->name }}</p><p class="text-xs text-slate-400">{{ $aduan->user->kelas }} · No. Absen {{ $aduan->user->no_presensi }}</p></div>
            </div>
            <div class="text-sm space-y-1 border-t pt-3">
                <p class="flex justify-between gap-3"><span class="text-slate-400">NISN</span><span class="font-medium">{{ $aduan->user->nisn }}</span></p>
                <p class="flex justify-between gap-3"><span class="text-slate-400">Email</span><span class="font-medium break-all text-right">{{ $aduan->user->email }}</span></p>
                <p class="flex justify-between gap-3"><span class="text-slate-400">No. HP</span><span class="font-medium">{{ $aduan->user->no_hp }}</span></p>
                <p class="flex justify-between gap-3"><span class="text-slate-400">Total Aduan Diajukan</span><span class="font-medium">{{ $totalAduanPelapor }} Tiket</span></p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.aduan.destroy', $aduan) }}" onsubmit="return confirm('Hapus tiket aduan ini secara permanen?')">
            @csrf @method('DELETE')
            <button class="w-full text-red-600 text-sm py-2">🗑 Hapus Tiket Aduan Ini</button>
        </form>
    </div>
</div>
@endsection