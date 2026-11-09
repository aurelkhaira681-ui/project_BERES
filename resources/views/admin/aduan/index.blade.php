@extends('layouts.dashboard')
@section('title', 'Data Aduan - BERES')
@section('content')

@php
    $sel  = 'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-[#0f2d5c] outline-none focus:border-[#1f6fe5] focus:ring-2 focus:ring-[#1f6fe5]/20';

    $kartu = [
        ['Total Aduan', $stats['total'],     'from-[#e6f0ff] to-[#f4f8ff] border-[#d6e6ff]', 'bg-[#1f6fe5]', '<path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM9 12h6M9 16h6"/>'],
        ['Pending',     $stats['pending'],   'from-[#fff3dc] to-[#fffaf0] border-[#ffe6b3]', 'bg-[#f5a524]', '<circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/>'],
        ['Disetujui',   $stats['disetujui'], 'from-[#dff7e8] to-[#f2fcf6] border-[#c4eed6]', 'bg-[#22b36b]', '<path d="M5 12.5l4.5 4.5L19 7.5"/>'],
        ['Ditolak',     $stats['ditolak'],   'from-[#ffe3e1] to-[#fff4f3] border-[#ffd0cc]', 'bg-[#ef4b43]', '<path d="M6 6l12 12M18 6L6 18"/>'],
    ];
    $mendesakAktif = $stats['mendesak'];
    $filterAktif = request()->hasAny(['q', 'kategori', 'lokasi', 'teknisi', 'status', 'tanggal', 'prioritas']);
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-5">
    <div class="flex items-center gap-3">
        <span class="w-11 h-11 rounded-2xl text-white flex items-center justify-center shadow-md shadow-blue-700/30" style="background:linear-gradient(135deg,#2f7cf6,#4f46e5)">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M4 7a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v2H4zm0 4h16v6a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3z"/></svg>
        </span>
        <div>
            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#0f2d5c]">Data Aduan</h1>
            <p class="text-[#5d7aa8] text-sm">Informasi seluruh pengaduan sarana sekolah</p>
        </div>
    </div>
    <a href="{{ route('admin.aduan.export', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0f3a9a] hover:bg-[#0b2f7e] text-white text-sm font-bold px-4 py-2.5 transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
        Export CSV
    </a>
</div>

{{-- Kartu ringkas --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
    @foreach($kartu as [$nama, $angka, $bg, $ikonBg, $ikon])
        <div class="b-lift rounded-3xl border bg-gradient-to-br {{ $bg }} px-4 py-3.5 flex items-center gap-3">
            <span class="w-10 h-10 shrink-0 rounded-xl {{ $ikonBg }} text-white flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">{!! $ikon !!}</svg>
            </span>
            <div>
                <p class="text-2xl font-extrabold text-[#0f2d5c] leading-none">{{ $angka }}</p>
                <p class="text-xs text-[#40608f] mt-1">{{ $nama }}</p>
            </div>
        </div>
    @endforeach
</div>

@if($mendesakAktif > 0)
    <a href="{{ route('admin.aduan.index', ['prioritas' => 'mendesak']) }}" class="mb-5 flex items-center gap-3 rounded-3xl border border-[#ffd0cc] px-4 py-3 hover:shadow-md transition" style="background: linear-gradient(120deg,#fff0ee,#fff8f7)">
        <span class="relative flex h-3 w-3 shrink-0"><span class="absolute inline-flex h-full w-full rounded-full bg-[#ef4b43] opacity-70 animate-ping"></span><span class="relative inline-flex h-3 w-3 rounded-full bg-[#ef4b43]"></span></span>
        <span class="min-w-0 flex-1 text-sm text-[#7a1f1a]"><b>{{ $mendesakAktif }} aduan mendesak</b> belum selesai, misalnya korsleting atau atap bocor. Tampil paling atas di daftar.</span>
        <span class="text-sm font-bold text-[#c2352d] whitespace-nowrap">Lihat →</span>
    </a>
@endif

<div class="{{ $card }} !rounded-3xl p-4 sm:p-5">

    {{-- Filter --}}
    <form method="GET" class="grid sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-3 mb-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tiket, siswa, lokasi..." class="{{ $sel }} lg:col-span-2">

        <select name="tanggal" class="{{ $sel }}">
            @foreach(['' => 'Semua Tanggal', 'hari-ini' => 'Hari Ini', '7-hari' => '7 Hari Terakhir', 'bulan-ini' => 'Bulan Ini'] as $v => $l)
                <option value="{{ $v }}" @selected(request('tanggal', '') === $v)>{{ $l }}</option>
            @endforeach
        </select>

        <select name="kategori" class="{{ $sel }}">
            <option value="">Semua Kategori</option>
            @foreach($kategoriList as $k)
                <option value="{{ $k->id }}" @selected(request('kategori') == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>

        <select name="prioritas" class="{{ $sel }}">
            @foreach(['' => 'Semua Prioritas', 'mendesak' => 'Mendesak', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $v => $l)
                <option value="{{ $v }}" @selected(request('prioritas', '') === $v)>{{ $l }}</option>
            @endforeach
        </select>

        <select name="status" class="{{ $sel }}">
            @foreach(['' => 'Semua Status', 'pending' => 'Pending', 'disetujui' => 'Disetujui', 'selesai' => 'Selesai', 'rejected' => 'Ditolak'] as $v => $l)
                <option value="{{ $v }}" @selected(request('status', '') === $v)>{{ $l }}</option>
            @endforeach
        </select>

        <select name="lokasi" class="{{ $sel }}">
            <option value="">Semua Lokasi</option>
            @foreach($lokasiList as $l)
                <option value="{{ $l->id }}" @selected(request('lokasi') == $l->id)>{{ $l->nama_ruangan }}</option>
            @endforeach
        </select>

        <select name="teknisi" class="{{ $sel }}">
            <option value="">Semua Teknisi</option>
            <option value="belum" @selected(request('teknisi') === 'belum')>Belum ditunjuk</option>
            @foreach($teknisiList as $t)
                <option value="{{ $t->id }}" @selected(request('teknisi') == $t->id)>{{ $t->nama }}</option>
            @endforeach
        </select>

        <div class="flex gap-2 sm:col-span-2 lg:col-span-4 xl:col-span-8 lg:justify-end">
            @if($filterAktif)
                <a href="{{ route('admin.aduan.index') }}" class="rounded-xl bg-[#e6effb] hover:bg-[#d8e5f8] text-[#0f2d5c] text-sm font-bold px-5 py-2.5 transition">Reset</a>
            @endif
            <button class="rounded-xl text-white text-sm font-bold px-6 py-2.5 transition hover:opacity-90" style="background:linear-gradient(135deg,#2f7cf6,#4f46e5)">Filter</button>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="tbl-stack w-full text-sm min-w-[980px]">
            <thead>
                <tr class="text-left text-[#40608f] text-xs font-bold border-b border-slate-100">
                    <th class="py-3 pr-3 w-10">No.</th><th class="pr-3">Prioritas</th><th class="pr-3">No. Tiket</th><th class="pr-3">Siswa</th><th class="pr-3">Kategori</th><th class="pr-3">Lokasi</th><th class="pr-3">Teknisi</th><th class="pr-3">Tanggal</th><th class="pr-3">Status</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aduan as $item)
                    @php
                        $foto = collect($item->foto ?? [])->first();
                        $detail = [
                            'tiket' => $item->kode_tiket,
                            'siswa' => $item->user->name,
                            'kategori' => $item->kategori->nama,
                            'lokasi' => $item->lokasi->nama_lengkap,
                            'teknisi' => $item->teknisi?->nama ?? 'Belum ditunjuk',
                            'tanggal' => $item->tanggal_kejadian->locale('id')->translatedFormat('d M Y'),
                            'status' => $item->statusLabel(),
                            'prioritas' => $item->prioritasLabel(),
                            'rating' => $item->rating,
                            'badge' => match ($item->status) {
                                'pending' => 'bg-[#fff1d6] text-[#c97f00]',
                                'approved' => 'bg-[#d9f4e5] text-[#15935a]',
                                'selesai' => 'bg-[#dcebff] text-[#1d6fd8]',
                                default => 'bg-[#fde2e0] text-[#dc3f36]',
                            },
                            'deskripsi' => $item->deskripsi,
                            'foto' => $foto ? asset('storage/' . $foto) : null,
                            'url' => route('admin.aduan.show', $item),
                            'proses' => in_array($item->status, ['pending', 'approved']),
                            'timeline' => array_values(array_filter([
                                ['Pengaduan dibuat', $item->created_at->locale('id')->translatedFormat('d M Y, H:i'), 'bg-[#1f6fe5]'],
                                $item->diverifikasi_at ? [$item->status === 'rejected' ? 'Aduan ditolak' : 'Aduan disetujui', $item->diverifikasi_at->locale('id')->translatedFormat('d M Y, H:i'), $item->status === 'rejected' ? 'bg-[#ef4b43]' : 'bg-[#22b36b]'] : null,
                                $item->selesai_at ? ['Selesai diperbaiki', $item->selesai_at->locale('id')->translatedFormat('d M Y, H:i'), 'bg-[#2f7cf6]'] : null,
                                $item->status === 'pending' ? ['Menunggu verifikasi', null, 'bg-[#f5a524]'] : null,
                            ])),
                        ];
                    @endphp
                    @php $daruratBaris = $item->isMendesak() && in_array($item->status, ['pending', 'approved']); @endphp
                    <tr class="border-b border-slate-100 last:border-0 hover:bg-[#f6f9ff] {{ $daruratBaris ? 'bg-[#fff6f5]' : '' }}">
                        <td class="py-3.5 pr-3 text-[#40608f]">{{ $aduan->firstItem() + $loop->index }}</td>
                        <td class="pr-3">@include('partials.prioritas-badge', ['item' => $item])</td>
                        <td class="pr-3 font-bold text-[#0f2d5c] whitespace-nowrap">{{ $item->kode_tiket }}</td>
                        <td class="pr-3">{{ $item->user->name }}</td>
                        <td class="pr-3">{{ $item->kategori->nama }}</td>
                        <td class="pr-3">{{ $item->lokasi->nama_ruangan }}</td>
                        <td class="pr-3 whitespace-nowrap {{ $item->teknisi ? '' : 'text-slate-400' }}">{{ $item->teknisi?->nama ?? 'Belum ditunjuk' }}</td>
                        <td class="pr-3 whitespace-nowrap">{{ $item->tanggal_kejadian->locale('id')->translatedFormat('d M Y') }}</td>
                        <td class="pr-3">@include('partials.admin-status-badge', ['item' => $item])</td>
                        <td class="text-center">
                            <button type="button" class="btn-detail inline-flex w-9 h-9 items-center justify-center rounded-lg text-[#1f4f9c] hover:bg-[#e6effb]" aria-label="Lihat detail {{ $item->kode_tiket }}" data-detail="{{ json_encode($detail) }}">
                                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="py-10 text-center text-slate-400">Tidak ada aduan yang cocok dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 mt-4 text-xs text-[#5d7aa8]">
        <p>Menampilkan {{ $aduan->firstItem() ?? 0 }}–{{ $aduan->lastItem() ?? 0 }} dari {{ $aduan->total() }} data</p>
        <div>{{ $aduan->links() }}</div>
    </div>
</div>

{{-- Modal detail --}}
<div id="modal-detail" class="hidden fixed inset-0 z-[60] items-center justify-center p-4 bg-[#0b1d3d]/50" role="dialog" aria-modal="true" aria-labelledby="md-judul">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 id="md-judul" class="font-extrabold text-lg text-[#0f2d5c]">Detail Pengaduan</h2>
            <button type="button" data-tutup class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500" aria-label="Tutup">✕</button>
        </div>
        <div class="flex gap-4">
            <div id="md-foto-wrap" class="w-28 h-28 shrink-0 rounded-xl bg-[#e6effb] overflow-hidden flex items-center justify-center text-xs text-slate-400 text-center">Tidak ada foto</div>
            <dl class="text-sm grid grid-cols-[auto_1fr] gap-x-3 gap-y-1.5 content-start min-w-0">
                <dt class="text-[#5d7aa8]">No. Tiket</dt><dd id="md-tiket" class="font-semibold text-[#0f2d5c]"></dd>
                <dt class="text-[#5d7aa8]">Nama Siswa</dt><dd id="md-siswa" class="font-semibold text-[#0f2d5c] break-words"></dd>
                <dt class="text-[#5d7aa8]">Kategori</dt><dd id="md-kategori" class="font-semibold text-[#0f2d5c]"></dd>
                <dt class="text-[#5d7aa8]">Lokasi</dt><dd id="md-lokasi" class="font-semibold text-[#0f2d5c] break-words"></dd>
                <dt class="text-[#5d7aa8]">Teknisi</dt><dd id="md-teknisi" class="font-semibold text-[#0f2d5c]"></dd>
                <dt class="text-[#5d7aa8]">Tanggal</dt><dd id="md-tanggal" class="font-semibold text-[#0f2d5c]"></dd>
                <dt class="text-[#5d7aa8]">Prioritas</dt><dd id="md-prioritas" class="font-semibold text-[#0f2d5c]"></dd>
                <dt class="text-[#5d7aa8]">Rating</dt><dd id="md-rating" class="font-semibold text-[#e2a21a]"></dd>
                <dt class="text-[#5d7aa8]">Status</dt><dd><span id="md-status" class="inline-flex rounded-full px-3 py-0.5 text-xs font-semibold"></span></dd>
            </dl>
        </div>
        <h3 class="font-bold text-[#0f2d5c] mt-5 mb-1">Deskripsi</h3>
        <p id="md-deskripsi" class="text-sm text-[#40608f] break-words"></p>
        <h3 class="font-bold text-[#0f2d5c] mt-5 mb-2">Timeline</h3>
        <ul id="md-timeline" class="space-y-3 text-sm"></ul>
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" data-tutup class="rounded-xl bg-[#e6effb] hover:bg-[#d8e5f8] text-[#0f2d5c] text-sm font-bold px-5 py-2.5">Tutup</button>
            <a id="md-aksi" href="#" class="rounded-xl bg-[#0f3a9a] hover:bg-[#0b2f7e] text-white text-sm font-bold px-5 py-2.5"></a>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('modal-detail');
    var $ = function (id) { return document.getElementById(id); };

    function buka(d) {
        $('md-tiket').textContent = d.tiket;
        $('md-siswa').textContent = d.siswa;
        $('md-kategori').textContent = d.kategori;
        $('md-lokasi').textContent = d.lokasi;
        $('md-teknisi').textContent = d.teknisi;
        $('md-tanggal').textContent = d.tanggal;
        $('md-deskripsi').textContent = d.deskripsi;
        $('md-prioritas').textContent = d.prioritas;
        $('md-rating').textContent = d.rating ? '★'.repeat(d.rating) + ' (' + d.rating + '/5)' : '-';

        var st = $('md-status');
        st.textContent = d.status;
        st.className = 'inline-flex rounded-full px-3 py-0.5 text-xs font-semibold ' + d.badge;

        var wrap = $('md-foto-wrap');
        wrap.textContent = 'Tidak ada foto';
        if (d.foto) {
            var img = document.createElement('img');
            img.src = d.foto; img.alt = 'Foto aduan'; img.className = 'w-full h-full object-cover';
            wrap.textContent = ''; wrap.appendChild(img);
        }

        var tl = $('md-timeline');
        tl.textContent = '';
        d.timeline.forEach(function (t) {
            var li = document.createElement('li');
            li.className = 'flex items-start gap-3';
            var dot = document.createElement('i');
            dot.className = 'w-3 h-3 mt-1 rounded-full shrink-0 ' + t[2];
            var box = document.createElement('div');
            var a = document.createElement('p'); a.className = 'font-semibold text-[#0f2d5c]'; a.textContent = t[0];
            box.appendChild(a);
            if (t[1]) { var b = document.createElement('p'); b.className = 'text-xs text-slate-400'; b.textContent = t[1]; box.appendChild(b); }
            li.appendChild(dot); li.appendChild(box); tl.appendChild(li);
        });

        var aksi = $('md-aksi');
        aksi.href = d.url;
        aksi.textContent = d.proses ? 'Proses Aduan' : 'Lihat Detail';

        modal.classList.remove('hidden'); modal.classList.add('flex');
    }
    function tutup() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

    document.querySelectorAll('.btn-detail').forEach(function (b) {
        b.addEventListener('click', function () { buka(JSON.parse(b.dataset.detail)); });
    });
    modal.querySelectorAll('[data-tutup]').forEach(function (b) { b.addEventListener('click', tutup); });
    modal.addEventListener('click', function (e) { if (e.target === modal) tutup(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });
})();
</script>
@endsection