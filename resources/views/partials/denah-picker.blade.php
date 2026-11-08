{{--
    Pemilih lokasi lewat denah sekolah.
    Dipakai di form "Ajukan Aduan Baru" (siswa), tepat di bawah <select id="lokasi_id">.
    Variabel: $lokasi (koleksi model Lokasi). Data posisi ruangan ada di config/denah.php.
--}}
@php
    $denah = config('denah');
    $petaRuangan = collect($denah['ruangan'] ?? [])
        ->map(function ($r) use ($lokasi) {
            $r['id'] = optional($lokasi->firstWhere('nama_ruangan', $r['nama']))->id;
            return $r;
        })
        ->filter(fn ($r) => $r['id'])
        ->values();
@endphp

@if($petaRuangan->isNotEmpty())
    <button type="button" id="dnh-buka"
            class="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold text-[#0f4a9a] hover:text-[#0b3c7f] hover:underline focus-visible:outline-2 focus-visible:outline-[#4a86e8] rounded">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 4 3 6.5v13L9 17l6 3 6-2.5v-13L15 7z"/><path d="M9 4v13M15 7v13"/></svg>
        Bingung mencari ruangan? Pilih lewat denah
    </button>

    <style>
        .dnh-modal[hidden] { display: none; }
        .dnh-modal { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; }
        .dnh-backdrop { position: absolute; inset: 0; background: rgba(15, 45, 92, .55); }
        .dnh-panel { position: relative; display: flex; flex-direction: column; width: 100%; height: 100%; background: #fff; overflow: hidden; }
        @media (min-width: 768px) {
            .dnh-panel { width: min(760px, calc(100vw - 48px)); height: min(920px, calc(100vh - 48px)); border-radius: 18px; box-shadow: 0 24px 60px rgba(15, 45, 92, .35); }
        }
        .dnh-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 16px 18px 10px; }
        .dnh-head h3 { font-size: 17px; font-weight: 800; color: #0f2d5c; }
        .dnh-head p { font-size: 13px; color: #5d7aa8; margin-top: 2px; }
        .dnh-ikon { flex: none; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: 1px solid #dbe5f3; background: #fff; color: #0f2d5c; cursor: pointer; }
        .dnh-ikon:hover:not(:disabled) { background: #eef3fb; }
        .dnh-ikon:disabled { opacity: .4; cursor: default; }
        .dnh-ikon:focus-visible, .dnh-spot:focus-visible, .dnh-aksi:focus-visible { outline: 2px solid #4a86e8; outline-offset: 2px; }
        .dnh-tools { display: flex; align-items: center; gap: 8px; padding: 0 18px 10px; font-size: 13px; color: #5d7aa8; }
        .dnh-tools output { min-width: 44px; text-align: center; font-weight: 700; color: #0f2d5c; }
        .dnh-scroll { flex: 1 1 auto; min-height: 0; overflow: auto; background: #f4f7fc; border-top: 1px solid #e3ebf6; border-bottom: 1px solid #e3ebf6; overscroll-behavior: contain; }
        .dnh-head, .dnh-tools, .dnh-foot { flex: none; }
        .dnh-stage { position: relative; margin: 0 auto; }
        .dnh-stage img { display: block; width: 100%; max-width: none; height: auto; user-select: none; -webkit-user-drag: none; }
        .dnh-spot { position: absolute; padding: 0; margin: 0; border: 0; background: transparent; cursor: pointer; border-radius: 2px; transition: background-color .12s; }
        .dnh-spot:hover { background: rgba(15, 58, 122, .22); box-shadow: inset 0 0 0 1.5px #0f3a7a; }
        .dnh-spot.is-picked { background: rgba(245, 158, 11, .42); box-shadow: inset 0 0 0 2.5px #d97706; z-index: 1; }
        .dnh-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 18px max(14px, env(safe-area-inset-bottom)); }
        .dnh-status { font-size: 14px; color: #5d7aa8; min-width: 0; }
        .dnh-status strong { color: #0f2d5c; font-weight: 800; }
        .dnh-btns { display: flex; gap: 8px; margin-left: auto; }
        .dnh-aksi { padding: 10px 16px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: 1px solid #dbe5f3; background: #fff; color: #0f2d5c; }
        .dnh-aksi:hover { background: #eef3fb; }
        .dnh-aksi.utama { background: #0f3a7a; border-color: #0f3a7a; color: #fff; }
        .dnh-aksi.utama:hover:not(:disabled) { background: #0b2f66; }
        .dnh-aksi.utama:disabled { opacity: .45; cursor: not-allowed; }
        @media (prefers-reduced-motion: reduce) { .dnh-spot { transition: none; } }
    </style>

    <div id="dnh-modal" class="dnh-modal" hidden role="dialog" aria-modal="true" aria-labelledby="dnh-judul">
        <div class="dnh-backdrop" data-dnh-tutup></div>
        <div class="dnh-panel">
            <div class="dnh-head">
                <div>
                    <h3 id="dnh-judul">Denah SMK Negeri 5 Surakarta</h3>
                    <p>Ketuk ruangan yang bermasalah, lalu tekan “Pakai ruangan ini”.</p>
                </div>
                <button type="button" class="dnh-ikon" data-dnh-tutup aria-label="Tutup denah">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>

            <div class="dnh-tools">
                <button type="button" class="dnh-ikon" id="dnh-kecil" aria-label="Perkecil denah">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                </button>
                <output id="dnh-zoom" aria-live="polite">100%</output>
                <button type="button" class="dnh-ikon" id="dnh-besar" aria-label="Perbesar denah">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                </button>
                <span>Perbesar untuk memilih ruangan yang kecil.</span>
            </div>

            <div class="dnh-scroll" id="dnh-scroll">
                <div class="dnh-stage" id="dnh-stage" data-rasio="{{ $denah['rasio'][0] / $denah['rasio'][1] }}">
                    <img src="{{ asset($denah['gambar']) }}" alt="Denah ruang SMK Negeri 5 Surakarta" draggable="false">
                    @foreach($petaRuangan as $r)
                        <button type="button" class="dnh-spot"
                                data-id="{{ $r['id'] }}" data-nama="{{ $r['nama'] }}" data-lantai="{{ $r['lantai'] }}"
                                style="left: {{ $r['x'] }}%; top: {{ $r['y'] }}%; width: {{ $r['w'] }}%; height: {{ $r['h'] }}%;"
                                title="{{ $r['nama'] }}" aria-label="{{ $r['nama'] }}, {{ $r['lantai'] }}"></button>
                    @endforeach
                </div>
            </div>

            <div class="dnh-foot">
                <p class="dnh-status" id="dnh-status" aria-live="polite">Belum ada ruangan dipilih.</p>
                <div class="dnh-btns">
                    <button type="button" class="dnh-aksi" data-dnh-tutup>Batal</button>
                    <button type="button" class="dnh-aksi utama" id="dnh-pakai" disabled>Pakai ruangan ini</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dijalankan SETELAH halaman selesai dimuat: partial ini di-include sebelum <select id="lokasi_id">,
        // jadi kalau langsung jalan, select belum ada dan tombol denah tidak berfungsi.
        function bukaDenahInit() {
            var modal = document.getElementById('dnh-modal');
            var select = document.getElementById('lokasi_id');
            if (!modal || !select) return;
            document.body.appendChild(modal); // supaya tidak terpotong kartu form

            var buka = document.getElementById('dnh-buka');
            var scroll = document.getElementById('dnh-scroll');
            var stage = document.getElementById('dnh-stage');
            var status = document.getElementById('dnh-status');
            var pakai = document.getElementById('dnh-pakai');
            var zoomOut = document.getElementById('dnh-zoom');
            var btnKecil = document.getElementById('dnh-kecil');
            var btnBesar = document.getElementById('dnh-besar');
            var spots = Array.prototype.slice.call(stage.querySelectorAll('.dnh-spot'));
            var rasio = parseFloat(stage.getAttribute('data-rasio'));
            var langkah = [1, 1.5, 2, 3];
            var zi = 0;
            var terpilih = null;

            function lebarDasar() {
                // pas dengan tinggi area gulir, tetapi tidak melebihi lebarnya
                return Math.max(260, Math.min(scroll.clientWidth, scroll.clientHeight * rasio));
            }

            function terapkanZoom() {
                var pusatX = (scroll.scrollLeft + scroll.clientWidth / 2) / (stage.offsetWidth || 1);
                var pusatY = (scroll.scrollTop + scroll.clientHeight / 2) / (stage.offsetHeight || 1);
                stage.style.width = Math.round(lebarDasar() * langkah[zi]) + 'px';
                zoomOut.textContent = Math.round(langkah[zi] * 100) + '%';
                btnKecil.disabled = zi === 0;
                btnBesar.disabled = zi === langkah.length - 1;
                scroll.scrollLeft = pusatX * stage.offsetWidth - scroll.clientWidth / 2;
                scroll.scrollTop = pusatY * stage.offsetHeight - scroll.clientHeight / 2;
            }

            function pilih(spot) {
                if (terpilih) terpilih.classList.remove('is-picked');
                terpilih = spot;
                if (spot) {
                    spot.classList.add('is-picked');
                    status.innerHTML = '';
                    var b = document.createElement('strong');
                    b.textContent = spot.getAttribute('data-nama');
                    status.appendChild(b);
                    status.appendChild(document.createTextNode(' · ' + spot.getAttribute('data-lantai')));
                } else {
                    status.textContent = 'Belum ada ruangan dipilih.';
                }
                pakai.disabled = !spot;
            }

            function tampilkan(spot) {
                var x = spot.offsetLeft + spot.offsetWidth / 2;
                var y = spot.offsetTop + spot.offsetHeight / 2;
                scroll.scrollLeft = x - scroll.clientWidth / 2;
                scroll.scrollTop = y - scroll.clientHeight / 2;
            }

            function bukaModal() {
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                zi = 0;
                stage.style.width = Math.round(lebarDasar()) + 'px';
                terapkanZoom();

                var saatIni = spots.filter(function (s) { return s.getAttribute('data-id') === select.value; })[0] || null;
                pilih(saatIni);
                if (saatIni) { zi = 1; terapkanZoom(); tampilkan(saatIni); }
                modal.querySelector('.dnh-ikon').focus();
            }

            function tutupModal() {
                modal.hidden = true;
                document.body.style.overflow = '';
                buka.focus();
            }

            buka.addEventListener('click', bukaModal);
            Array.prototype.forEach.call(modal.querySelectorAll('[data-dnh-tutup]'), function (el) {
                el.addEventListener('click', tutupModal);
            });
            document.addEventListener('keydown', function (e) {
                if (!modal.hidden && e.key === 'Escape') tutupModal();
            });
            spots.forEach(function (s) { s.addEventListener('click', function () { pilih(s); }); });
            btnKecil.addEventListener('click', function () { if (zi > 0) { zi--; terapkanZoom(); } });
            btnBesar.addEventListener('click', function () { if (zi < langkah.length - 1) { zi++; terapkanZoom(); } });
            window.addEventListener('resize', function () { if (!modal.hidden) terapkanZoom(); });

            pakai.addEventListener('click', function () {
                if (!terpilih) return;
                select.value = terpilih.getAttribute('data-id');
                select.dispatchEvent(new Event('change', { bubbles: true }));
                tutupModal();
                select.focus();
            });
        }
        if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', bukaDenahInit); } else { bukaDenahInit(); }
    </script>
@endif
