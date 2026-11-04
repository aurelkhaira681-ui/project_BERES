@extends('layouts.dashboard')
@section('title', 'Master Data - BERES')
@section('content')

@php
    $card  = 'bg-white rounded-2xl border border-slate-100 shadow-[0_2px_14px_rgba(20,50,105,0.06)]';
    $field = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-[#1f6fe5] focus:ring-2 focus:ring-[#1f6fe5]/20';
    $tabs  = [
        'kategori' => ['Kategori Kerusakan', $kategori->count()],
        'lokasi'   => ['Lokasi / Ruangan', $lokasi->count()],
        'teknisi'  => ['Teknisi', $teknisi->count()],
        'pengguna' => ['Data Pengguna', $pengguna->total()],
    ];
    $tombolEdit  = 'w-8 h-8 inline-flex items-center justify-center rounded-lg bg-[#1f6fe5] hover:bg-[#1a5ec4] text-white';
    $tombolHapus = 'w-8 h-8 inline-flex items-center justify-center rounded-lg bg-[#ef4b43] hover:bg-[#d93d35] text-white';
    $ikonEdit  = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9l-4-4L4 16zM14 6l4 4"/></svg>';
    $ikonHapus = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/></svg>';
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-5">
    <div class="flex items-center gap-3">
        <span class="w-11 h-11 rounded-xl bg-[#0f3a9a] text-white flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3C7 3 3.5 4.5 3.5 6.5S7 10 12 10s8.5-1.5 8.5-3.5S17 3 12 3zM3.5 9v4c0 2 3.5 3.5 8.5 3.5s8.5-1.5 8.5-3.5V9c0 2-3.5 3.5-8.5 3.5S3.5 11 3.5 9zm0 6.5v2c0 2 3.5 3.5 8.5 3.5s8.5-1.5 8.5-3.5v-2c0 2-3.5 3.5-8.5 3.5s-8.5-1.5-8.5-3.5z"/></svg>
        </span>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0f2d5c]">Master Data</h1>
            <p class="text-[#5d7aa8] text-sm">Kelola kategori, lokasi, teknisi, dan data pengguna</p>
        </div>
    </div>
    <div class="flex items-center gap-2 w-full sm:w-auto">
        <input id="cari-data" type="search" placeholder="Cari data..." class="{{ $field }} sm:w-56">
        @if($tab !== 'pengguna')
            <button type="button" id="btn-tambah" class="shrink-0 rounded-xl bg-[#0f3a9a] hover:bg-[#0b2f7e] text-white text-sm font-bold px-4 py-2.5 transition whitespace-nowrap">+ Tambah Data</button>
        @endif
    </div>
</div>

{{-- Tab --}}
<div class="flex flex-wrap gap-2 mb-4 text-sm font-semibold">
    @foreach($tabs as $key => [$nama, $jml])
        <a href="{{ route('admin.master-data', ['tab' => $key]) }}"
           class="px-4 py-2 rounded-xl border transition {{ $tab === $key ? 'bg-[#0f3a9a] text-white border-[#0f3a9a]' : 'bg-white text-[#0f2d5c] border-slate-200 hover:bg-[#f0f5ff]' }}">{{ $nama }} ({{ $jml }})</a>
    @endforeach
</div>

<div class="{{ $card }} p-4 sm:p-5">

@if($errors->any())
    <div class="mb-4 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
        <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

@if($tab === 'kategori')
    <div class="overflow-x-auto"><table id="tabel-data" class="w-full text-sm min-w-[560px]">
        <thead><tr class="text-left text-[#40608f] text-xs font-bold border-b border-slate-100">
            <th class="py-3 pr-3 w-10">No.</th><th class="pr-3">Nama Kategori</th><th class="pr-3">Ikon</th><th class="pr-3">Jumlah Aduan</th><th class="pr-3">Status</th><th class="text-center">Aksi</th>
        </tr></thead>
        <tbody>
        @forelse($kategori as $k)
            <tr class="border-b border-slate-100 last:border-0">
                <td class="py-3.5 pr-3 text-[#40608f]">{{ $loop->iteration }}</td>
                <td class="pr-3 font-semibold text-[#0f2d5c]">{{ $k->nama }}</td>
                <td class="pr-3 text-lg">{{ $k->icon }}</td>
                <td class="pr-3">{{ $k->aduan_count }}</td>
                <td class="pr-3"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $k->aktif ? 'bg-[#d9f4e5] text-[#15935a]' : 'bg-slate-100 text-slate-500' }}">{{ $k->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td class="text-center whitespace-nowrap">
                    <button type="button" class="btn-edit {{ $tombolEdit }}" aria-label="Edit {{ $k->nama }}"
                            data-action="{{ route('admin.kategori.update', $k) }}"
                            data-nama="{{ $k->nama }}" data-icon="{{ $k->icon }}" data-aktif="{{ $k->aktif ? 1 : 0 }}">{!! $ikonEdit !!}</button>
                    <form method="POST" action="{{ route('admin.kategori.destroy', $k) }}" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
                        @csrf @method('DELETE')
                        <button class="{{ $tombolHapus }}" aria-label="Hapus {{ $k->nama }}">{!! $ikonHapus !!}</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-10 text-center text-slate-400">Belum ada kategori.</td></tr>
        @endforelse
        </tbody>
    </table></div>

@elseif($tab === 'lokasi')
    <div class="overflow-x-auto"><table id="tabel-data" class="w-full text-sm min-w-[620px]">
        <thead><tr class="text-left text-[#40608f] text-xs font-bold border-b border-slate-100">
            <th class="py-3 pr-3 w-10">No.</th><th class="pr-3">Nama Ruangan</th><th class="pr-3">Lantai</th><th class="pr-3">Jurusan</th><th class="pr-3">Jumlah Aduan</th><th class="text-center">Aksi</th>
        </tr></thead>
        <tbody>
        @forelse($lokasi as $l)
            <tr class="border-b border-slate-100 last:border-0">
                <td class="py-3.5 pr-3 text-[#40608f]">{{ $loop->iteration }}</td>
                <td class="pr-3 font-semibold text-[#0f2d5c]">{{ $l->nama_ruangan }}</td>
                <td class="pr-3">{{ $l->lantai ?: '-' }}</td>
                <td class="pr-3">
                    @if($l->jurusan)
                        <span class="rounded-full px-3 py-1 text-xs font-semibold bg-[#e6effb] text-[#1d4f9c]">{{ \App\Models\Teknisi::singkat($l->jurusan) }}</span>
                    @else
                        <span class="text-xs text-slate-400">Umum (ditentukan petugas)</span>
                    @endif
                </td>
                <td class="pr-3">{{ $l->aduan_count }}</td>
                <td class="text-center whitespace-nowrap">
                    <button type="button" class="btn-edit {{ $tombolEdit }}" aria-label="Edit {{ $l->nama_ruangan }}"
                            data-action="{{ route('admin.lokasi.update', $l) }}"
                            data-nama="{{ $l->nama_ruangan }}" data-lantai="{{ $l->lantai }}" data-jurusan="{{ $l->jurusan }}">{!! $ikonEdit !!}</button>
                    <form method="POST" action="{{ route('admin.lokasi.destroy', $l) }}" onsubmit="return confirm('Hapus lokasi ini?')" class="inline">
                        @csrf @method('DELETE')
                        <button class="{{ $tombolHapus }}" aria-label="Hapus {{ $l->nama_ruangan }}">{!! $ikonHapus !!}</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-10 text-center text-slate-400">Belum ada lokasi.</td></tr>
        @endforelse
        </tbody>
    </table></div>

@elseif($tab === 'teknisi')
    <div class="overflow-x-auto"><table id="tabel-data" class="w-full text-sm min-w-[620px]">
        <thead><tr class="text-left text-[#40608f] text-xs font-bold border-b border-slate-100">
            <th class="py-3 pr-3 w-10">No.</th><th class="pr-3">Nama Teknisi</th><th class="pr-3">Jurusan</th><th class="pr-3">Aduan Aktif</th><th class="pr-3">Status</th><th class="text-center">Aksi</th>
        </tr></thead>
        <tbody>
        @forelse($teknisi as $t)
            <tr class="border-b border-slate-100 last:border-0">
                <td class="py-3.5 pr-3 text-[#40608f]">{{ $loop->iteration }}</td>
                <td class="pr-3 font-semibold text-[#0f2d5c]">{{ $t->nama }}</td>
                <td class="pr-3">{{ $t->jurusan }}</td>
                <td class="pr-3">{{ $t->aktif_count }}</td>
                <td class="pr-3"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $t->aktif ? 'bg-[#d9f4e5] text-[#15935a]' : 'bg-[#fff1d6] text-[#c97f00]' }}">{{ $t->aktif ? 'Aktif' : 'Cuti' }}</span></td>
                <td class="text-center whitespace-nowrap">
                    <button type="button" class="btn-edit {{ $tombolEdit }}" aria-label="Edit {{ $t->nama }}"
                            data-action="{{ route('admin.teknisi.update', $t) }}"
                            data-nama="{{ $t->nama }}" data-jurusan="{{ $t->jurusan }}" data-aktif="{{ $t->aktif ? 1 : 0 }}">{!! $ikonEdit !!}</button>
                    <form method="POST" action="{{ route('admin.teknisi.destroy', $t) }}" onsubmit="return confirm('Hapus teknisi ini?')" class="inline">
                        @csrf @method('DELETE')
                        <button class="{{ $tombolHapus }}" aria-label="Hapus {{ $t->nama }}">{!! $ikonHapus !!}</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-10 text-center text-slate-400">Belum ada teknisi.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <p class="mt-4 text-xs text-[#5d7aa8]">Teknisi berstatus Cuti tidak dipilih otomatis. Jurusan lokasi diatur di tab Lokasi / Ruangan.</p>

@else
    <div class="overflow-x-auto"><table id="tabel-data" class="w-full text-sm min-w-[560px]">
        <thead><tr class="text-left text-[#40608f] text-xs font-bold border-b border-slate-100">
            <th class="py-3 pr-3 w-10">No.</th><th class="pr-3">Nama</th><th class="pr-3">NISN</th><th class="pr-3">Kelas</th><th>Email</th>
        </tr></thead>
        <tbody>
        @forelse($pengguna as $p)
            <tr class="border-b border-slate-100 last:border-0">
                <td class="py-3.5 pr-3 text-[#40608f]">{{ $pengguna->firstItem() + $loop->index }}</td>
                <td class="pr-3 font-semibold text-[#0f2d5c]">{{ $p->name }}</td>
                <td class="pr-3">{{ $p->nisn ?: '-' }}</td>
                <td class="pr-3">{{ $p->kelas ?: '-' }}</td>
                <td class="break-all">{{ $p->email }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-10 text-center text-slate-400">Belum ada pengguna.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="flex flex-wrap items-center justify-between gap-3 mt-4 text-xs text-[#5d7aa8]">
        <p>Menampilkan {{ $pengguna->firstItem() ?? 0 }}–{{ $pengguna->lastItem() ?? 0 }} dari {{ $pengguna->total() }} data</p>
        <div>{{ $pengguna->links() }}</div>
    </div>
@endif
</div>

{{-- Modal tambah / edit --}}
@if($tab !== 'pengguna')
<div id="modal-form" class="hidden fixed inset-0 z-[60] items-center justify-center p-4 bg-[#0b1d3d]/50" role="dialog" aria-modal="true" aria-labelledby="mf-judul">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 id="mf-judul" class="font-extrabold text-lg text-[#0f2d5c]"></h2>
            <button type="button" data-tutup class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500" aria-label="Tutup">✕</button>
        </div>
        <form id="mf-form" method="POST" action="">
            @csrf
            <input type="hidden" name="_method" id="mf-method" value="POST" disabled>

            @if($tab === 'kategori')
                <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Nama Kategori</label>
                <input name="nama" id="mf-nama" required maxlength="100" class="{{ $field }} mb-3">
                <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Ikon (emoji)</label>
                <input name="icon" id="mf-icon" maxlength="10" placeholder="🧩" class="{{ $field }} mb-3">
                <label id="mf-aktif-wrap" class="hidden items-center gap-2 text-sm text-[#0f2d5c] mb-3">
                    <input type="checkbox" name="aktif" id="mf-aktif" value="1" class="w-4 h-4 accent-[#1f6fe5]"> Kategori aktif
                </label>
            @elseif($tab === 'lokasi')
                <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Nama Ruangan</label>
                <input name="nama_ruangan" id="mf-nama" required maxlength="100" class="{{ $field }} mb-3">
                <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Lantai</label>
                <input name="lantai" id="mf-lantai" maxlength="50" class="{{ $field }} mb-3">
                <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Jurusan</label>
                <select name="jurusan" id="mf-jurusan" class="{{ $field }} mb-1">
                    <option value="">Umum (ditentukan petugas)</option>
                    @foreach(\App\Models\Teknisi::JURUSAN as $j)<option value="{{ $j }}">{{ $j }}</option>@endforeach
                </select>
                <p class="text-xs text-[#5d7aa8] mb-3">Menentukan teknisi otomatis di formulir siswa.</p>
            @else
                <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Nama Teknisi</label>
                <input name="nama" id="mf-nama" required maxlength="100" placeholder="Pak ..." class="{{ $field }} mb-3">
                <label class="block text-sm font-bold text-[#0f2d5c] mb-1.5">Jurusan</label>
                <select name="jurusan" id="mf-jurusan" required class="{{ $field }} mb-3">
                    @foreach(\App\Models\Teknisi::JURUSAN as $j)<option value="{{ $j }}">{{ $j }}</option>@endforeach
                </select>
                <label id="mf-aktif-t-wrap" class="hidden items-center gap-2 text-sm text-[#0f2d5c] mb-3">
                    <input type="checkbox" name="aktif" id="mf-aktif-t" value="1" class="w-4 h-4 accent-[#1f6fe5]"> Teknisi aktif (tidak cuti)
                </label>
            @endif

            <div class="flex justify-end gap-2 mt-2">
                <button type="button" data-tutup class="rounded-xl bg-[#e6effb] hover:bg-[#d8e5f8] text-[#0f2d5c] text-sm font-bold px-5 py-2.5">Batal</button>
                <button class="rounded-xl bg-[#0f3a9a] hover:bg-[#0b2f7e] text-white text-sm font-bold px-5 py-2.5">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
(function () {
    // Cari data (filter baris tabel di halaman ini)
    var cari = document.getElementById('cari-data');
    var tabel = document.getElementById('tabel-data');
    cari.addEventListener('input', function () {
        var q = cari.value.trim().toLowerCase();
        tabel.querySelectorAll('tbody tr').forEach(function (tr) {
            tr.style.display = tr.textContent.toLowerCase().indexOf(q) === -1 ? 'none' : '';
        });
    });

    var modal = document.getElementById('modal-form');
    if (!modal) return;
    var form = document.getElementById('mf-form');
    var method = document.getElementById('mf-method');
    var judul = document.getElementById('mf-judul');
    var tab = @json($tab);
    var urlTambah = {
        kategori: @json(route('admin.kategori.store')),
        lokasi: @json(route('admin.lokasi.store')),
        teknisi: @json(route('admin.teknisi.store'))
    }[tab];
    var namaTab = { kategori: 'Kategori', lokasi: 'Lokasi', teknisi: 'Teknisi' }[tab];
    var $ = function (id) { return document.getElementById(id); };

    function buka() { modal.classList.remove('hidden'); modal.classList.add('flex'); $('mf-nama').focus(); }
    function tutup() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

    function mode(edit, d) {
        form.action = edit ? d.action : urlTambah;
        method.disabled = !edit;
        method.value = 'PUT';
        judul.textContent = (edit ? 'Edit ' : 'Tambah ') + namaTab;
        $('mf-nama').value = edit ? d.nama : '';
        if (tab === 'kategori') {
            $('mf-icon').value = edit ? (d.icon || '') : '';
            $('mf-aktif').checked = edit ? d.aktif === '1' : true;
            var w = $('mf-aktif-wrap');
            w.classList.toggle('hidden', !edit); w.classList.toggle('flex', edit);
        } else if (tab === 'lokasi') {
            $('mf-lantai').value = edit ? (d.lantai || '') : '';
            $('mf-jurusan').value = edit ? (d.jurusan || '') : '';
        } else {
            $('mf-jurusan').selectedIndex = 0;
            if (edit) $('mf-jurusan').value = d.jurusan;
            $('mf-aktif-t').checked = edit ? d.aktif === '1' : true;
            var wt = $('mf-aktif-t-wrap');
            wt.classList.toggle('hidden', !edit); wt.classList.toggle('flex', edit);
        }
        buka();
    }

    $('btn-tambah').addEventListener('click', function () { mode(false); });
    document.querySelectorAll('.btn-edit').forEach(function (b) {
        b.addEventListener('click', function () { mode(true, b.dataset); });
    });
    modal.querySelectorAll('[data-tutup]').forEach(function (b) { b.addEventListener('click', tutup); });
    modal.addEventListener('click', function (e) { if (e.target === modal) tutup(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });
})();
</script>
@endsection