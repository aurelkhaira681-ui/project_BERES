{{-- Kartu Preferensi (Notifikasi, Tema, Bahasa) — dipakai di halaman Akun Saya siswa & petugas --}}
@php
    $kartuPref = 'bg-white rounded-2xl border border-slate-100 shadow-[0_2px_14px_rgba(20,50,105,0.06)]';
    $prefItems = [
        ['notif',   'Notifikasi', 'Atur notifikasi aduan', '<path d="M6 16V11a6 6 0 0 1 12 0v5l1.5 2h-15zM10 20a2 2 0 0 0 4 0"/>', 'text-slate-400', null],
        ['tema',    'Tema',       'Mode Terang',           '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5z"/>', 'text-[#6a8fc9]', 'tema'],
        ['bahasa',  'Bahasa',     'Indonesia',             '<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 9h6M11 9c0 3-2 5-4 6M9 12c.7 1.4 2 2.5 4 3M15 15l2-5 2 5M15.8 13.5h2.4"/>', 'text-[#1d6fd8]', 'bahasa'],
    ];
    $radio = 'flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 cursor-pointer hover:bg-slate-50';
@endphp

<div class="{{ $kartuPref }} p-5 sm:p-6">
    <h2 class="font-bold text-[#0f2d5c] mb-2" data-i18n>Preferensi</h2>
    <div class="divide-y divide-slate-100">
        @foreach($prefItems as [$kunci, $judul, $sub, $ikon, $warna, $dinamis])
            <button type="button" data-pref-open="{{ $kunci }}" class="w-full text-left flex items-center gap-3 py-3.5 hover:bg-slate-50/70 -mx-2 px-2 rounded-lg transition" style="width: calc(100% + 1rem)">
                <svg class="w-5 h-5 shrink-0 {{ $warna }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ikon !!}</svg>
                <span class="flex-1 min-w-0">
                    <span class="block text-sm font-semibold text-[#0f2d5c]" data-i18n>{{ $judul }}</span>
                    @if($dinamis)
                        <span class="block text-xs text-slate-400" data-pref-sub="{{ $dinamis }}">{{ $sub }}</span>
                    @else
                        <span class="block text-xs text-slate-400" data-i18n>{{ $sub }}</span>
                    @endif
                </span>
                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
            </button>
        @endforeach
    </div>
</div>

{{-- Modal pengaturan --}}
<div id="pref-modal" class="hidden fixed inset-0 z-[60] items-center justify-center p-4 bg-[#0b1d3d]/50" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 id="pref-judul" class="font-extrabold text-lg text-[#0f2d5c]"></h2>
            <button type="button" data-pref-tutup class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500" aria-label="Tutup">✕</button>
        </div>

        <div data-pane="notif" class="hidden">
            <label class="{{ $radio }}">
                <input type="checkbox" id="pref-notif" class="w-4 h-4 accent-[#1f6fe5]">
                <span class="text-sm font-semibold text-[#0f2d5c]" data-i18n>Terima notifikasi aduan</span>
            </label>
            <p class="text-xs text-slate-400 mt-3" data-i18n>Menampilkan lencana jumlah aduan pada menu dan lonceng.</p>
        </div>

        <div data-pane="tema" class="hidden space-y-2">
            <label class="{{ $radio }}"><input type="radio" name="pref-tema" value="light" class="accent-[#1f6fe5]"><span class="text-sm font-semibold text-[#0f2d5c]" data-i18n>Mode Terang</span></label>
            <label class="{{ $radio }}"><input type="radio" name="pref-tema" value="dark" class="accent-[#1f6fe5]"><span class="text-sm font-semibold text-[#0f2d5c]" data-i18n>Mode Gelap</span></label>
        </div>

        <div data-pane="bahasa" class="hidden space-y-2">
            <label class="{{ $radio }}"><input type="radio" name="pref-bahasa" value="id" class="accent-[#1f6fe5]"><span class="text-sm font-semibold text-[#0f2d5c]">Indonesia</span></label>
            <label class="{{ $radio }}"><input type="radio" name="pref-bahasa" value="en" class="accent-[#1f6fe5]"><span class="text-sm font-semibold text-[#0f2d5c]">English</span></label>
        </div>

        <div class="flex justify-end mt-5">
            <button type="button" data-pref-tutup class="rounded-xl bg-[#e6effb] hover:bg-[#d8e5f8] text-[#0f2d5c] text-sm font-bold px-5 py-2.5" data-i18n>Tutup</button>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('pref-modal');
    var judul = document.getElementById('pref-judul');
    var P = window.BERES_PREF;
    var judulMap = { notif: 'Notifikasi', tema: 'Tema', bahasa: 'Bahasa' };

    function buka(kunci) {
        var p = P.get();
        judul.textContent = P.t(judulMap[kunci]);
        modal.querySelectorAll('[data-pane]').forEach(function (el) {
            el.classList.toggle('hidden', el.dataset.pane !== kunci);
        });
        document.getElementById('pref-notif').checked = !!p.notif;
        modal.querySelector('input[name=pref-tema][value=' + p.tema + ']').checked = true;
        modal.querySelector('input[name=pref-bahasa][value=' + p.bahasa + ']').checked = true;
        modal.classList.remove('hidden'); modal.classList.add('flex');
        modal.dataset.aktif = kunci;
    }
    function tutup() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

    document.querySelectorAll('[data-pref-open]').forEach(function (b) {
        b.addEventListener('click', function () { buka(b.dataset.prefOpen); });
    });
    modal.querySelectorAll('[data-pref-tutup]').forEach(function (b) { b.addEventListener('click', tutup); });
    modal.addEventListener('click', function (e) { if (e.target === modal) tutup(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });

    document.getElementById('pref-notif').addEventListener('change', function (e) { P.set('notif', e.target.checked); });
    modal.querySelectorAll('input[name=pref-tema]').forEach(function (r) {
        r.addEventListener('change', function () { P.set('tema', r.value); });
    });
    modal.querySelectorAll('input[name=pref-bahasa]').forEach(function (r) {
        r.addEventListener('change', function () { P.set('bahasa', r.value); judul.textContent = P.t(judulMap[modal.dataset.aktif]); });
    });
})();
</script>