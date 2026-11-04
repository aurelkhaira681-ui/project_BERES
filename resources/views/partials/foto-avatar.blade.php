{{--
    Avatar profil + tombol ganti foto. Dipakai di halaman Akun (siswa & admin).
    Foto dipotong persegi & dikompres di browser (maks 512px) sebelum diunggah.
    Params: $user, $initials
--}}
@php
    $fotoUrl = $user->fotoUrl();
    $errFoto = $errors->getBag('foto')->first('foto');
@endphp
<div class="shrink-0">
    <div class="relative w-[84px] h-[84px]">
        <div class="w-full h-full rounded-full p-[3px]" style="background: linear-gradient(135deg, #5aa7ff, #a5e3ff);">
            <div class="w-full h-full rounded-full overflow-hidden bg-[#b9d0ee] text-[#0f2d5c] flex items-center justify-center text-3xl font-semibold">
                <span id="foto-inisial" class="{{ $fotoUrl ? 'hidden' : '' }}">{{ $initials }}</span>
                <img id="foto-img" src="{{ $fotoUrl }}" alt="Foto profil {{ $user->name }}" class="{{ $fotoUrl ? '' : 'hidden' }} w-full h-full object-cover">
            </div>
        </div>

        <button type="button" id="foto-pilih" title="Ganti foto" aria-label="Ganti foto profil"
                class="absolute -bottom-0.5 -right-0.5 w-8 h-8 rounded-full bg-[#1f6fe5] hover:bg-[#1a5fc4] text-white border-2 border-white shadow flex items-center justify-center transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8a2 2 0 0 1 2-2h1.5l1.5-2h6l1.5 2H18a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><circle cx="12" cy="13" r="3.5"/></svg>
        </button>

        @if($fotoUrl)
            <form method="POST" action="{{ route('akun.foto.hapus') }}" id="foto-hapus-form" class="absolute -top-1 -right-1">
                @csrf @method('DELETE')
                <button type="submit" title="Hapus foto" aria-label="Hapus foto profil"
                        class="w-6 h-6 rounded-full bg-white text-red-500 border border-slate-200 shadow flex items-center justify-center hover:bg-red-50 transition">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </form>
        @endif
    </div>

    <form id="foto-form" method="POST" action="{{ route('akun.foto') }}" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" id="foto-input" name="foto" accept="image/jpeg,image/png,image/webp">
    </form>

    <div id="foto-bar" class="hidden gap-2 mt-3">
        <button type="submit" form="foto-form" class="rounded-lg bg-[#1f6fe5] hover:bg-[#1a5fc4] text-white text-xs font-bold px-3 py-1.5 transition" data-i18n>Simpan Foto</button>
        <button type="button" id="foto-batal" class="rounded-lg bg-[#e6effb] hover:bg-[#d8e5f8] text-[#0f2d5c] text-xs font-bold px-3 py-1.5 transition" data-i18n>Batal</button>
    </div>

    @if($errFoto)
        <p class="mt-2 text-xs text-red-500 max-w-[11rem] leading-snug">{{ $errFoto }}</p>
    @endif
</div>

<script>
(function () {
    var input = document.getElementById('foto-input');
    var img = document.getElementById('foto-img');
    var inisial = document.getElementById('foto-inisial');
    var bar = document.getElementById('foto-bar');
    var asal = img.getAttribute('src') || '';
    var T = function (x) { return (window.BERES_PREF && window.BERES_PREF.t) ? window.BERES_PREF.t(x) : x; };

    function tampil(src) {
        img.src = src;
        img.classList.remove('hidden');
        inisial.classList.add('hidden');
        bar.style.display = 'flex';
    }
    function batal() {
        input.value = '';
        bar.style.display = 'none';
        if (asal) { img.src = asal; } else { img.classList.add('hidden'); inisial.classList.remove('hidden'); }
    }

    document.getElementById('foto-pilih').addEventListener('click', function () { input.click(); });
    document.getElementById('foto-batal').addEventListener('click', batal);

    var hapus = document.getElementById('foto-hapus-form');
    if (hapus) hapus.addEventListener('submit', function (e) { if (!confirm(T('Hapus foto profil?'))) e.preventDefault(); });

    input.addEventListener('change', function () {
        var f = input.files && input.files[0];
        if (!f) return;
        if (!/^image\/(jpeg|png|webp)$/.test(f.type)) { alert(T('Format foto harus JPG, PNG, atau WebP.')); input.value = ''; return; }

        var url = URL.createObjectURL(f);
        var im = new Image();
        im.onload = function () {
            try {
                var s = Math.min(im.naturalWidth, im.naturalHeight);
                var sx = (im.naturalWidth - s) / 2, sy = (im.naturalHeight - s) / 2, out = Math.min(512, s);
                var c = document.createElement('canvas');
                c.width = c.height = out;
                c.getContext('2d').drawImage(im, sx, sy, s, s, 0, 0, out, out);
                c.toBlob(function (b) {
                    if (!b) { tampil(url); return; }
                    try {
                        var dt = new DataTransfer();
                        dt.items.add(new File([b], 'foto.jpg', { type: 'image/jpeg' }));
                        input.files = dt.files;
                    } catch (e) { /* biarkan berkas asli terkirim */ }
                    tampil(URL.createObjectURL(b));
                }, 'image/jpeg', 0.88);
            } catch (e) { tampil(url); }
        };
        im.onerror = function () { tampil(url); };
        im.src = url;
    });
})();
</script>