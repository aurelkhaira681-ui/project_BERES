@extends('layouts.dashboard')
@section('title', $aduan->kode_tiket . ' - BERES')
@section('content')

<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0f2d5c]">{{ Str::limit($aduan->deskripsi, 50) }}</h1>
        <p class="text-slate-500 text-sm flex flex-wrap items-center gap-x-2 gap-y-1">
            <span>Tiket <span class="font-semibold text-[#0f2d5c]">#{{ $aduan->kode_tiket }}</span></span>
            <button type="button" data-salin="{{ $aduan->kode_tiket }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-0.5 text-xs font-semibold text-[#1e3f7f] hover:bg-[#f0f5ff] transition">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V6a2 2 0 0 1 2-2h9"/></svg>
                <span data-salin-label>Salin</span>
            </button>
            <span>· Diajukan {{ $aduan->created_at->format('d M Y, H.i') }}</span>
        </p>
    </div>
    @include('partials.status-badge', ['item' => $aduan])
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <div class="lg:col-span-2 lg:row-start-1 space-y-6">
        <div class="{{ $card }} p-5">
            <h2 class="font-semibold mb-5">Progres Penanganan</h2>

            @php
                $ditolak = $aduan->status === 'rejected';
                $selesai = $aduan->status === 'selesai';
                $verif   = (bool) $aduan->diverifikasi_at;
                $fmt     = 'd M Y · H.i';
                // state: done | current | todo | rejected
                $steps = [
                    ['Aduan Diajukan', $aduan->created_at->format($fmt), 'done'],
                    [
                        $ditolak ? 'Aduan Ditolak' : 'Diverifikasi Admin',
                        $ditolak ? ($aduan->diverifikasi_at?->format($fmt) ?? $aduan->updated_at->format($fmt)) : ($aduan->diverifikasi_at?->format($fmt) ?? 'Menunggu verifikasi'),
                        $ditolak ? 'rejected' : ($verif ? 'done' : 'current'),
                    ],
                    [
                        $aduan->teknisi && $verif && ! $ditolak ? 'Teknisi Menangani' : 'Penugasan Teknisi',
                        $ditolak ? 'Tidak dilanjutkan' : ($verif && $aduan->teknisi ? $aduan->teknisi->nama . ' · ' . \App\Models\Teknisi::singkat($aduan->teknisi->jurusan) : 'Ditentukan setelah verifikasi'),
                        $ditolak ? 'todo' : ($verif ? 'done' : 'todo'),
                    ],
                    [
                        'Perbaikan Selesai',
                        $ditolak ? 'Tidak dilanjutkan' : ($aduan->selesai_at?->format($fmt) ?? 'Menunggu tindak lanjut'),
                        $ditolak ? 'todo' : ($selesai ? 'done' : ($verif ? 'current' : 'todo')),
                    ],
                ];
            @endphp

            <ol class="text-sm">
                @foreach($steps as $i => [$judul, $waktu, $state])
                    @php $terakhir = $loop->last; @endphp
                    <li class="relative flex gap-4 {{ $terakhir ? '' : 'pb-6' }}">
                        @unless($terakhir)
                            <span class="absolute left-[13px] top-7 bottom-0 w-0.5 rounded-full {{ $state === 'done' ? 'bg-[#16305c]' : 'bg-slate-200' }}"></span>
                        @endunless

                        <span class="relative z-10 shrink-0 w-7 h-7 rounded-full flex items-center justify-center
                            {{ match($state) {
                                'done'     => 'bg-[#16305c] text-white',
                                'current'  => 'bg-white border-2 border-[#2f7cf6]',
                                'rejected' => 'bg-[#e5484d] text-white',
                                default    => 'bg-slate-100 border border-slate-200',
                            } }}">
                            @if($state === 'done')
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                            @elseif($state === 'rejected')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                            @elseif($state === 'current')
                                <span class="absolute inline-flex h-full w-full rounded-full bg-[#2f7cf6] opacity-30 animate-ping"></span>
                                <span class="relative w-2.5 h-2.5 rounded-full bg-[#2f7cf6]"></span>
                            @endif
                        </span>

                        <div class="pt-0.5">
                            <p class="font-semibold {{ $state === 'todo' ? 'text-slate-400' : ($state === 'rejected' ? 'text-[#c2352d]' : '') }}">{{ $judul }}</p>
                            <p class="text-slate-400 mt-0.5">{{ $waktu }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    {{-- Detail Kerusakan & Hasil Perbaikan: selebar halaman, berdampingan di layar lebar, atas-bawah di HP --}}
    @php $adaHasil = $aduan->status === 'selesai' && (filled($aduan->catatan_perbaikan) || !empty($aduan->foto_selesai)); @endphp
    <div class="grid grid-cols-1 {{ $adaHasil ? 'lg:grid-cols-2' : '' }} gap-6 items-start lg:col-span-3 lg:row-start-2">
        <div class="{{ $card }} p-5">
            <h2 class="font-semibold mb-4">Detail Kerusakan</h2>
            <div class="grid grid-cols-1 min-[420px]:grid-cols-2 {{ $adaHasil ? 'lg:grid-cols-1 xl:grid-cols-2' : '' }} gap-4 mb-4 text-sm [&>div]:min-w-0 [&_p]:break-words">
                <div><p class="text-slate-400 text-xs uppercase mb-1">Kategori</p><p class="font-medium">{{ $aduan->kategori->icon }} {{ $aduan->kategori->nama }}</p></div>
                <div><p class="text-slate-400 text-xs uppercase mb-1">Lokasi</p><p class="font-medium">{{ $aduan->lokasi->nama_lengkap }}</p></div>
                <div><p class="text-slate-400 text-xs uppercase mb-1">Teknisi</p><p class="font-medium">{{ $aduan->teknisi?->nama ?? 'Ditentukan petugas' }}@if($aduan->teknisi) <span class="text-xs text-slate-400 font-normal">· {{ \App\Models\Teknisi::singkat($aduan->teknisi->jurusan) }}</span>@endif</p></div>
            </div>
            <p class="text-sm text-slate-600 mb-4">{{ $aduan->deskripsi }}</p>

            @if(!empty($aduan->foto))
                <div class="grid grid-cols-2 sm:grid-cols-3 {{ $adaHasil ? 'lg:grid-cols-2 xl:grid-cols-3' : 'lg:grid-cols-4' }} gap-3">
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

        @if($aduan->status === 'selesai' && (filled($aduan->catatan_perbaikan) || !empty($aduan->foto_selesai)))
            <div class="{{ $card }} p-5">
                <h2 class="font-semibold mb-3">Hasil Perbaikan</h2>
                @if($aduan->catatan_perbaikan)
                    <p class="text-sm text-slate-600 mb-3">{{ $aduan->catatan_perbaikan }}</p>
                @endif
                @if(!empty($aduan->foto_selesai))
                    <div class="grid grid-cols-2 sm:grid-cols-3 {{ $adaHasil ? 'lg:grid-cols-2 xl:grid-cols-3' : 'lg:grid-cols-4' }} gap-3">
                        @foreach($aduan->foto_selesai as $foto)
                            <img src="{{ asset('storage/' . $foto) }}" alt="Foto hasil perbaikan" class="rounded-lg h-28 w-full object-cover">
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="space-y-4 lg:col-start-3 lg:row-start-1">
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
                <p class="flex justify-between gap-3"><span class="text-slate-400">No. HP</span><span class="font-medium">{{ $aduan->user->no_hp }}</span></p>
            </div>
        </div>

        <a href="{{ route('siswa.aduan.bukti', $aduan) }}" class="w-full inline-flex items-center justify-center gap-2 bg-[#16305c] text-white rounded-lg py-2.5 font-medium hover:bg-[#0f2d5c] transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11"/><path d="M7 11l5 5 5-5"/><path d="M5 20h14"/></svg>
            {{ __('Unduh Bukti Laporan (PDF)') }}
        </a>

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
@if(session('unduh_bukti'))
    {{-- Laporan baru saja dikirim: mulai unduh bukti PDF otomatis (tombol di sidebar sebagai cadangan) --}}
    <iframe src="{{ route('siswa.aduan.bukti', $aduan) }}" class="hidden" aria-hidden="true" tabindex="-1"></iframe>
@endif
<script>
    // Tombol "Salin" nomor tiket
    document.querySelectorAll('[data-salin]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var teks = btn.dataset.salin, label = btn.querySelector('[data-salin-label]');
            function selesai() { label.textContent = 'Tersalin!'; setTimeout(function () { label.textContent = 'Salin'; }, 1600); }
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(teks).then(selesai);
            } else {
                var t = document.createElement('textarea'); t.value = teks; document.body.appendChild(t);
                t.select(); document.execCommand('copy'); t.remove(); selesai();
            }
        });
    });
</script>
@endsection