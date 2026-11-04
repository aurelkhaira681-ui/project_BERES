@extends('layouts.dashboard')
@section('title', 'Ajukan Aduan - BERES')
@section('content')

@php
    $card = 'bg-white rounded-2xl border border-slate-100 shadow-[0_2px_14px_rgba(20,50,105,0.06)]';
    $field = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none placeholder:text-slate-400 focus:border-[#1e3f7f] focus:ring-2 focus:ring-[#1e3f7f]/20';
    $u = auth()->user();
    $initials = collect(explode(' ', $u->name))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp

{{-- Header --}}
<a href="{{ route('siswa.dashboard') }}" aria-label="Kembali ke beranda" title="Kembali"
   class="mb-3 inline-flex w-10 h-10 items-center justify-center rounded-xl bg-[#0f4a9a] text-white hover:bg-[#0b3c7f] transition">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
</a>
<h1 class="text-2xl sm:text-[30px] font-extrabold tracking-tight text-[#0f2d5c]">Ajukan Aduan Baru</h1>
<p class="text-[#5d7aa8] mt-1 mb-6">Isi detail kerusakan fasilitas selengkap mungkin agar cepat ditindaklanjuti</p>

@if($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 text-red-600 border border-red-200 px-4 py-3 text-sm">
        <ul class="list-disc pl-4">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('siswa.aduan.store') }}" enctype="multipart/form-data" class="grid lg:grid-cols-3 gap-5 sm:gap-6 items-start">
    @csrf

    {{-- Detail kerusakan --}}
    <div class="{{ $card }} p-5 sm:p-6 lg:col-span-2 space-y-5">
        <h2 class="font-bold text-[#0f2d5c]">Detail Kerusakan</h2>

        <div>
            <label class="block text-sm font-bold text-[#0f2d5c] mb-2.5">Kategori Kerusakan</label>
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

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="lokasi_id" class="block text-sm font-bold text-[#0f2d5c] mb-2">Lokasi / Ruangan</label>
                <select id="lokasi_id" name="lokasi_id" required class="{{ $field }}">
                    <option value="">Pilih lokasi</option>
                    @foreach($lokasi as $l)
                        <option value="{{ $l->id }}" {{ (string) old('lokasi_id') === (string) $l->id ? 'selected' : '' }}>{{ $l->nama_lengkap }}</option>
                    @endforeach
                </select>
                @include('partials.denah-picker', ['lokasi' => $lokasi])
            </div>
            <div>
                <label for="tanggal_kejadian" class="block text-sm font-bold text-[#0f2d5c] mb-2">Tanggal & Waktu Kejadian</label>
                <input id="tanggal_kejadian" type="datetime-local" name="tanggal_kejadian" required value="{{ old('tanggal_kejadian') }}" class="{{ $field }}">
            </div>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-bold text-[#0f2d5c] mb-2">Deskripsi Kerusakan</label>
            <textarea id="deskripsi" name="deskripsi" rows="7" required placeholder="Jelaskan kondisi kerusakan yang ditemukan..." class="{{ $field }} resize-y">{{ old('deskripsi') }}</textarea>
        </div>
    </div>

    {{-- Kolom kanan --}}
    <div class="space-y-5 sm:space-y-6">
        <div class="{{ $card }} p-5 sm:p-6">
            <h2 class="font-bold text-[#0f2d5c] mb-4">Foto Bukti</h2>

            <div class="relative rounded-xl border-2 border-dashed border-[#c9d8ee] bg-[#fafcff] hover:border-[#4a86e8] transition">
                <div class="flex flex-col items-center justify-center text-center px-4 py-8 text-sm text-[#5d7aa8]">
                    <svg class="w-6 h-6 text-[#4a86e8] mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h3l2-3h6l2 3h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/><circle cx="12" cy="13.5" r="3.5"/></svg>
                    <p id="foto-hint" class="leading-relaxed">Seret & lepas foto di sini,<br>atau klik untuk unggah<br>(maks. 3)</p>
                </div>
                <input id="foto" type="file" name="foto[]" multiple accept="image/*" aria-label="Unggah foto bukti" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
            </div>
            <div id="foto-preview" class="grid grid-cols-3 gap-2 mt-3"></div>
        </div>

        <div class="{{ $card }} p-5 sm:p-6">
            <h2 class="font-bold text-[#0f2d5c] mb-4">Data Pelapor</h2>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 shrink-0 rounded-full bg-[#b9d0ee] text-[#0f2d5c] flex items-center justify-center font-bold">{{ $initials }}</div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-[#0f2d5c] truncate">{{ $u->name }}</p>
                    <p class="text-xs text-[#5d7aa8] mt-0.5">{{ $u->kelas }} · No. Absen {{ $u->no_presensi }}</p>
                </div>
            </div>
            <p class="text-xs text-[#5d7aa8] mt-3">Terisi otomatis dari akun kamu.</p>
        </div>

        <button class="w-full flex items-center justify-center gap-2 rounded-xl bg-[#0f3a7a] hover:bg-[#0b2f66] active:scale-[.99] transition text-white font-bold py-3.5 shadow-lg shadow-blue-900/20">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M2.5 11.3 21 3l-4.2 18-5.3-5.6-3.4 3v-5.3zM9 12.5l7.4-5.3-9.2 6.7z" fill-rule="evenodd"/></svg>
            Kirim Aduan
        </button>
    </div>
</form>

<script>
    (function () {
        var input = document.getElementById('foto');
        var box = document.getElementById('foto-preview');
        var hint = document.getElementById('foto-hint');
        var original = hint.innerHTML;

        input.addEventListener('change', function () {
            var files = Array.prototype.slice.call(input.files, 0, 3);

            // batasi maksimal 3 foto
            if (input.files.length > 3) {
                var dt = new DataTransfer();
                files.forEach(function (f) { dt.items.add(f); });
                input.files = dt.files;
            }

            box.innerHTML = '';
            files.forEach(function (f) {
                var img = document.createElement('img');
                img.src = URL.createObjectURL(f);
                img.alt = f.name;
                img.className = 'w-full h-20 object-cover rounded-lg border border-slate-200';
                box.appendChild(img);
            });
            hint.innerHTML = files.length ? (files.length + ' foto dipilih — klik untuk mengganti') : original;
        });
    })();
</script>
@endsection
