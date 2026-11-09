@extends('layouts.dashboard')
@section('title', 'Ajukan Aduan - BERES')
@section('content')

@php
    $field = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/20';
    $u = auth()->user();
    $initials = collect(explode(' ', $u->name))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp

{{-- Header --}}
<a href="{{ route('siswa.dashboard') }}" aria-label="Kembali ke beranda" title="Kembali"
   class="mb-3 inline-flex w-10 h-10 items-center justify-center rounded-2xl text-white hover:scale-105 transition shadow-md shadow-blue-700/30" style="background:linear-gradient(135deg,#2f7cf6,#4f46e5)">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
</a>
<h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#0f2d5c]" data-i18n>Ajukan Aduan Baru</h1>
<p class="text-[#5d7aa8] mt-1 mb-6" data-i18n>Isi detail kerusakan fasilitas selengkap mungkin agar cepat ditindaklanjuti</p>

@if($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
        <ul class="list-disc pl-4">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('siswa.aduan.store') }}" enctype="multipart/form-data" class="grid lg:grid-cols-3 gap-5 sm:gap-6 items-start">
    @csrf

    {{-- Detail kerusakan --}}
    <div class="{{ $card }} !rounded-3xl p-5 sm:p-6 lg:col-span-2 space-y-5">
        <h2 class="font-bold text-[#0f2d5c]" data-i18n>Detail Kerusakan</h2>

        <div>
            <label class="block text-sm font-bold text-[#0f2d5c] mb-2.5" data-i18n>Kategori Kerusakan</label>
            <div class="flex gap-2.5 flex-wrap">
                @foreach($kategori as $k)
                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-white text-sm font-medium text-slate-600 cursor-pointer shadow-sm transition
                                  hover:border-[#9db8e0] has-[:checked]:bg-[#0f3a7a] has-[:checked]:text-white has-[:checked]:border-[#0f3a7a] has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-[#4a86e8]">
                        <input type="radio" name="kategori_kerusakan_id" value="{{ $k->id }}" class="sr-only" required {{ (string) old('kategori_kerusakan_id') === (string) $k->id ? 'checked' : '' }}>
                        <span>{{ $k->icon }}</span> {{ $k->nama }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Denah klik: pilih ruangan langsung di peta sekolah --}}
        @include('partials.denah-picker', ['lokasi' => $lokasi, 'terpilih' => old('lokasi_id'), 'selectId' => 'lokasi_id'])

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="lokasi_id" class="block text-sm font-bold text-[#0f2d5c] mb-2">Lokasi / Ruangan <span class="font-normal text-[#5d7aa8]">(atau pilih dari daftar)</span></label>
                <select id="lokasi_id" name="lokasi_id" required class="{{ $field }}">
                    <option value="">Pilih lokasi</option>
                    @foreach($lokasiGrup as $grup => $daftar)
                        <optgroup label="{{ $grup }}">
                            @foreach($daftar as $l)
                                <option value="{{ $l->id }}" {{ (string) old('lokasi_id') === (string) $l->id ? 'selected' : '' }}>{{ $l->nama_lengkap }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tanggal_kejadian" class="block text-sm font-bold text-[#0f2d5c] mb-2" data-i18n>Tanggal & Waktu Kejadian</label>
                <input id="tanggal_kejadian" type="datetime-local" name="tanggal_kejadian" required value="{{ old('tanggal_kejadian') }}" class="{{ $field }}">
            </div>
        </div>

        {{-- Peringatan aduan ganda: ruangan + kategori sama yang belum selesai --}}
        <div id="ganda-box" class="hidden rounded-2xl border border-[#ffd98a] p-4" style="background: linear-gradient(120deg,#fff6df,#fffbf1)" role="alert">
            <div class="flex items-start gap-3">
                <span class="w-9 h-9 shrink-0 rounded-xl bg-[#ffe3a3] flex items-center justify-center text-lg">⚠️</span>
                <div class="min-w-0 flex-1">
                    <p class="font-extrabold text-sm text-[#7a4b00]">Sepertinya kerusakan ini sudah dilaporkan</p>
                    <p class="text-xs text-[#a56a10] mt-0.5">Ruangan dan kategori yang sama masih punya aduan yang belum selesai. Cek dulu supaya tidak dobel. Kamu tetap boleh mengirim kalau ini kerusakan yang berbeda.</p>
                    <ul id="ganda-list" class="mt-2.5 space-y-1.5"></ul>
                </div>
            </div>
        </div>

        {{-- Prioritas / urgensi --}}
        <div>
            <label class="block text-sm font-bold text-[#0f2d5c] mb-2.5">Tingkat Prioritas</label>
            @php
                $prioritasOpsi = [
                    'rendah'   => ['Rendah',   'Mengganggu, tapi masih bisa dipakai', '🟢', 'has-[:checked]:border-[#3a8dde] has-[:checked]:bg-[#eaf3ff]'],
                    'sedang'   => ['Sedang',   'Perlu diperbaiki dalam waktu dekat',  '🟡', 'has-[:checked]:border-[#f5a524] has-[:checked]:bg-[#fff6e0]'],
                    'mendesak' => ['Mendesak', 'Berbahaya, contoh: korsleting, atap bocor', '🔴', 'has-[:checked]:border-[#ef4b43] has-[:checked]:bg-[#fff0ee]'],
                ];
                $prioritasLama = old('prioritas', 'sedang');
            @endphp
            <div class="grid sm:grid-cols-3 gap-2.5">
                @foreach($prioritasOpsi as $nilai => [$namaP, $ketP, $ikonP, $kelasP])
                    <label class="relative flex items-start gap-2.5 rounded-2xl border-2 border-slate-200 bg-white px-3.5 py-3 cursor-pointer transition hover:border-[#9db8e0] has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-[#4a86e8] {{ $kelasP }}">
                        <input type="radio" name="prioritas" value="{{ $nilai }}" class="sr-only" {{ $prioritasLama === $nilai ? 'checked' : '' }}>
                        <span class="text-lg leading-none mt-0.5">{{ $ikonP }}</span>
                        <span class="min-w-0">
                            <span class="block text-sm font-extrabold text-[#0f2d5c] leading-tight">{{ $namaP }}</span>
                            <span class="block text-[11px] text-[#5d7aa8] mt-0.5 leading-snug">{{ $ketP }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <p id="prioritas-info" class="hidden mt-2 text-xs font-semibold text-[#c2352d]">Aduan mendesak akan tampil paling atas di dashboard petugas dengan lencana merah. Pakai hanya untuk bahaya nyata ya.</p>
        </div>

        {{-- Teknisi: otomatis sesuai kategori & jurusan lokasi, tidak bisa dipilih siswa --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-bold text-[#0f2d5c]" data-i18n>Teknisi Penanggung Jawab</label>
                <span class="inline-flex items-center gap-1 text-xs text-[#5d7aa8]">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10.5" width="14" height="10" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/></svg>
                    Otomatis
                </span>
            </div>
            <div class="flex items-center gap-3 rounded-xl border border-dashed border-[#9db8e0] bg-slate-50 px-4 py-3">
                <div id="teknisi-avatar" class="w-10 h-10 shrink-0 rounded-full bg-[#e6effb] text-[#1d4f9c] flex items-center justify-center text-sm font-bold">?</div>
                <div class="min-w-0 flex-1">
                    <p id="teknisi-nama" class="font-bold text-sm text-[#0f2d5c]">Pilih lokasi terlebih dahulu</p>
                    <p id="teknisi-ket" class="text-xs text-[#5d7aa8] mt-0.5">Teknisi ditentukan otomatis sesuai jenis kerusakan dan lokasi.</p>
                </div>
                <span id="teknisi-beban" class="hidden shrink-0 rounded-full bg-[#d9f4e5] text-[#15935a] text-xs font-semibold px-2.5 py-1"></span>
            </div>
            <p class="text-xs text-[#5d7aa8] mt-2">Teknisi dipilih sistem berdasarkan kategori kerusakan, jurusan lokasi, dan beban kerja, jadi tidak bisa diubah.</p>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-bold text-[#0f2d5c] mb-2" data-i18n>Deskripsi Kerusakan</label>
            <textarea id="deskripsi" name="deskripsi" rows="7" required placeholder="Jelaskan kondisi kerusakan yang ditemukan..." class="{{ $field }} resize-y">{{ old('deskripsi') }}</textarea>
            <p id="deskripsi-hitung" class="text-xs text-[#5d7aa8] mt-1.5 text-right">0 karakter · minimal 10</p>
        </div>
    </div>

    {{-- Kolom kanan --}}
    <div class="space-y-5 sm:space-y-6">
        <div class="{{ $card }} !rounded-3xl p-5 sm:p-6">
            <h2 class="font-bold text-[#0f2d5c] mb-4" data-i18n>Foto Bukti</h2>

            <div id="foto-drop" class="relative rounded-xl border-2 border-dashed border-[#c9d8ee] bg-[#fafcff] hover:border-[#4a86e8] transition">
                <div class="flex flex-col items-center justify-center text-center px-4 py-8 text-sm text-[#5d7aa8]">
                    <svg class="w-6 h-6 text-[#4a86e8] mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h3l2-3h6l2 3h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/><circle cx="12" cy="13.5" r="3.5"/></svg>
                    <p id="foto-hint" class="leading-relaxed">Seret & lepas foto di sini,<br>atau klik untuk unggah<br>(maks. 3)</p>
                </div>
                <input id="foto" type="file" name="foto[]" multiple accept="image/*" aria-label="Unggah foto bukti" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
            </div>
            <div id="foto-preview" class="grid grid-cols-3 gap-2 mt-3"></div>
        </div>

        <div class="{{ $card }} !rounded-3xl p-5 sm:p-6">
            <h2 class="font-bold text-[#0f2d5c] mb-4" data-i18n>Data Pelapor</h2>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 shrink-0 rounded-full bg-[#b9d0ee] text-[#0f2d5c] flex items-center justify-center font-bold">{{ $initials }}</div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-[#0f2d5c] truncate">{{ $u->name }}</p>
                    <p class="text-xs text-[#5d7aa8] mt-0.5">{{ $u->kelas }} · No. Absen {{ $u->no_presensi }}</p>
                </div>
            </div>
            <p class="text-xs text-[#5d7aa8] mt-3" data-i18n>Terisi otomatis dari akun kamu.</p>
        </div>

        <button id="btn-kirim" class="b-shine w-full flex items-center justify-center gap-2 rounded-2xl hover:opacity-95 active:scale-[.99] transition text-white font-bold py-3.5 shadow-lg shadow-blue-700/30 disabled:opacity-70 disabled:cursor-wait" style="background:linear-gradient(135deg,#2f7cf6,#4f46e5)">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M2.5 11.3 21 3l-4.2 18-5.3-5.6-3.4 3v-5.3zM9 12.5l7.4-5.3-9.2 6.7z" fill-rule="evenodd"/></svg>
            <span id="btn-kirim-teks">Kirim Aduan</span>
        </button>
    </div>
</form>

<script>
    // Pratinjau teknisi menurut lokasi/jurusan ruangan
    // (penugasan sebenarnya ditentukan server saat aduan dikirim)
    (function () {
        var data = @json((object) $penugasan);
        var sel = document.getElementById('lokasi_id');
        var $ = function (id) { return document.getElementById(id); };

        function tampil() {
            var p = data[sel.value];
            var avatar = '?', nama = 'Pilih lokasi terlebih dahulu', ket = 'Teknisi ditentukan otomatis sesuai lokasi ruangan.', beban = '';

            if (sel.value && p) {
                if (!p.teknisi) {
                    avatar = '–'; nama = 'Menunggu petugas';
                    ket = 'Belum ada teknisi aktif untuk lokasi ini. Petugas akan menunjuk teknisi saat verifikasi.';
                } else {
                    avatar = p.teknisi.inisial; nama = p.teknisi.nama;
                    ket = (p.teknisi.bidang ? 'Bidang ' : 'Jurusan ') + p.teknisi.jurusan;
                    beban = p.teknisi.aktif + ' aduan aktif';
                }
            }

            $('teknisi-avatar').textContent = avatar;
            $('teknisi-nama').textContent = nama;
            $('teknisi-ket').textContent = ket;
            var b = $('teknisi-beban');
            b.textContent = beban;
            b.classList.toggle('hidden', !beban);
        }

        sel.addEventListener('change', tampil);
        tampil();
    })();

    (function () {
        var MAKS = 3, MAKS_MB = 2;
        var input = document.getElementById('foto');
        var box = document.getElementById('foto-preview');
        var hint = document.getElementById('foto-hint');
        var drop = document.getElementById('foto-drop');
        var original = hint.innerHTML;
        var daftar = [];   // berkas yang dipilih (bisa dihapus satu per satu)

        function sinkron() {
            var dt = new DataTransfer();
            daftar.forEach(function (f) { dt.items.add(f); });
            input.files = dt.files;
        }

        function gambar() {
            box.innerHTML = '';
            daftar.forEach(function (f, i) {
                var sel = document.createElement('div');
                sel.className = 'relative group';
                var img = document.createElement('img');
                img.src = URL.createObjectURL(f);
                img.alt = f.name;
                img.className = 'w-full h-20 object-cover rounded-lg border border-slate-200';
                var x = document.createElement('button');
                x.type = 'button';
                x.setAttribute('aria-label', 'Hapus foto ' + f.name);
                x.className = 'absolute -top-1.5 -right-1.5 w-6 h-6 rounded-full bg-[#e5484d] text-white shadow flex items-center justify-center text-sm leading-none';
                x.textContent = '×';
                x.addEventListener('click', function () { daftar.splice(i, 1); sinkron(); gambar(); });
                sel.appendChild(img); sel.appendChild(x); box.appendChild(sel);
            });
            hint.textContent = daftar.length
                ? daftar.length + ' dari ' + MAKS + ' foto dipilih. Klik untuk menambah.'
                : '';
            if (!daftar.length) hint.innerHTML = original;
        }

        input.addEventListener('change', function () {
            var ditolak = [];
            Array.prototype.forEach.call(input.files, function (f) {
                if (f.size > MAKS_MB * 1024 * 1024) { ditolak.push(f.name); return; }
                if (daftar.length < MAKS) daftar.push(f);
            });
            sinkron(); gambar();
            if (ditolak.length) hint.textContent = 'Terlalu besar (maks. ' + MAKS_MB + ' MB): ' + ditolak.join(', ');
        });

        // sorot kotak saat file diseret ke atasnya
        ['dragenter', 'dragover'].forEach(function (e) {
            input.addEventListener(e, function () { drop.classList.add('border-[#4a86e8]', 'bg-[#eef5ff]'); });
        });
        ['dragleave', 'drop', 'change'].forEach(function (e) {
            input.addEventListener(e, function () { drop.classList.remove('border-[#4a86e8]', 'bg-[#eef5ff]'); });
        });
    })();


    // Prioritas "Mendesak": tampilkan catatan singkat
    (function () {
        var info = document.getElementById('prioritas-info');
        var radios = document.querySelectorAll('input[name="prioritas"]');
        function tampil() {
            var v = document.querySelector('input[name="prioritas"]:checked');
            info.classList.toggle('hidden', !(v && v.value === 'mendesak'));
        }
        radios.forEach(function (r) { r.addEventListener('change', tampil); });
        tampil();
    })();

    // Deteksi aduan ganda: ruangan + kategori yang sama dan belum selesai
    (function () {
        var box = document.getElementById('ganda-box');
        var list = document.getElementById('ganda-list');
        var lokasi = document.getElementById('lokasi_id');
        var url = '{{ route('siswa.aduan.cek-ganda') }}';
        var timer = null, seq = 0;

        function kategori() {
            var k = document.querySelector('input[name="kategori_kerusakan_id"]:checked');
            return k ? k.value : '';
        }
        function tampil(data) {
            list.textContent = '';
            if (!data || !data.jumlah) { box.classList.add('hidden'); return; }
            data.aduan.forEach(function (a) {
                var li = document.createElement('li');
                li.className = 'flex flex-wrap items-center gap-x-2 gap-y-0.5 rounded-xl bg-white/80 border border-[#ffe2a8] px-3 py-1.5 text-xs text-[#7a4b00]';
                var kode = document.createElement(a.url ? 'a' : 'span');
                kode.className = 'font-extrabold' + (a.url ? ' underline' : '');
                kode.textContent = '#' + a.kode;
                if (a.url) kode.href = a.url;
                var ket = document.createElement('span');
                ket.textContent = '· ' + a.status + ' · ' + a.tanggal + (a.milikmu ? ' · aduanmu sendiri' : ' · dilaporkan siswa lain');
                li.appendChild(kode); li.appendChild(ket); list.appendChild(li);
            });
            box.classList.remove('hidden');
        }
        function cek() {
            clearTimeout(timer);
            timer = setTimeout(function () {
                if (!lokasi.value || !kategori()) { tampil(null); return; }
                var id = ++seq;
                fetch(url + '?lokasi_id=' + encodeURIComponent(lokasi.value) + '&kategori_kerusakan_id=' + encodeURIComponent(kategori()), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function (r) { return r.ok ? r.json() : null; })
                  .then(function (d) { if (id === seq) tampil(d); })
                  .catch(function () {});
            }, 250);
        }
        lokasi.addEventListener('change', cek);
        document.querySelectorAll('input[name="kategori_kerusakan_id"]').forEach(function (r) { r.addEventListener('change', cek); });
        cek();
    })();

    // Penghitung karakter deskripsi
    (function () {
        var ta = document.getElementById('deskripsi'), info = document.getElementById('deskripsi-hitung');
        function hitung() {
            var n = ta.value.trim().length, cukup = n >= 10;
            info.textContent = n + ' karakter' + (cukup ? ' ✓' : ' · minimal 10');
            info.classList.toggle('text-[#15935a]', cukup);
            info.classList.toggle('text-[#5d7aa8]', !cukup);
        }
        ta.addEventListener('input', hitung); hitung();
    })();

    // Tombol kirim: tampil "Mengirim..." agar tidak terklik dua kali
    (function () {
        var form = document.querySelector('form[action="{{ route('siswa.aduan.store') }}"]');
        var btn = document.getElementById('btn-kirim'), teks = document.getElementById('btn-kirim-teks');
        form.addEventListener('submit', function () {
            if (!form.checkValidity()) return;
            btn.disabled = true;
            teks.textContent = 'Mengirim…';
            btn.insertAdjacentHTML('afterbegin', '<svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 3a9 9 0 1 0 9 9" opacity=".9"/></svg>');
        });
    })();
</script>
@endsection