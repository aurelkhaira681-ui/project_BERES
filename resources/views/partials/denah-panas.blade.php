{{--
    Peta panas denah sekolah: ruangan diberi warna sesuai jumlah aduan (semakin merah = semakin sering rusak).
    Dipakai di admin/dashboard. Variabel: $panas (koleksi ruangan + 'jumlah'), $panasMaks (jumlah tertinggi).
    Posisi ruangan dari config/denah.php.
--}}
@php
    $denahCfg = config('denah');
    $adaPanas = $panas->where('jumlah', '>', 0)->isNotEmpty();
    $totalPanas = (int) $panas->sum('jumlah');
@endphp

<div class="{{ $card }} p-5">
    <div class="flex items-start justify-between gap-3 mb-3">
        <div>
            <h2 class="font-bold text-[#0f2d5c] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1f6fe5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4 3 6.5v13L9 17l6 3 6-2.5v-13L15 7z"/><path d="M9 4v13M15 7v13"/></svg>
                Peta Panas Kerusakan
            </h2>
            <p class="text-xs text-[#5d7aa8] mt-0.5">Jumlah aduan per ruangan pada denah sekolah</p>
        </div>
        <span class="text-xs font-semibold text-[#40608f] border border-slate-200 rounded-lg px-3 py-1.5 whitespace-nowrap">{{ $totalPanas }} aduan</span>
    </div>

    @if($adaPanas)
        <div class="relative mx-auto w-full max-w-[380px] rounded-lg overflow-hidden border border-slate-100 bg-slate-50">
            <img src="{{ asset($denahCfg['gambar']) }}" alt="Denah ruang sekolah" class="block w-full h-auto select-none" draggable="false">
            @foreach($panas as $r)
                @continue($r['jumlah'] < 1)
                @php $inten = $r['jumlah'] / $panasMaks; @endphp
                <button type="button"
                        class="js-panas absolute p-0 m-0 border-0 cursor-pointer rounded-[2px] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#0f3a7a]"
                        style="left: {{ $r['x'] }}%; top: {{ $r['y'] }}%; width: {{ $r['w'] }}%; height: {{ $r['h'] }}%; background: rgba({{ $inten > .66 ? '220,38,38' : ($inten > .33 ? '245,120,36' : '245,175,36') }}, {{ number_format(0.35 + $inten * 0.5, 2, '.', '') }}); box-shadow: inset 0 0 0 1px rgba(15,45,92,.35);"
                        title="{{ $r['nama'] }}: {{ $r['jumlah'] }} aduan"
                        aria-label="{{ $r['nama'] }}: {{ $r['jumlah'] }} aduan"
                        data-nama="{{ $r['nama'] }}" data-jumlah="{{ $r['jumlah'] }}"></button>
            @endforeach
        </div>

        <p id="panas-info" class="mt-3 text-sm text-center text-[#5d7aa8] min-h-[1.25rem]" aria-live="polite">Sentuh atau arahkan kursor ke ruangan berwarna untuk melihat jumlah aduan.</p>

        <div class="mt-3 flex items-center justify-center gap-2 text-[11px] text-[#40608f]">
            <span>Sedikit</span>
            <span class="h-2.5 w-32 rounded-full" style="background: linear-gradient(90deg, rgba(245,175,36,.6), rgba(245,120,36,.75), rgba(220,38,38,.85));"></span>
            <span>Banyak</span>
        </div>
    @else
        <div class="rounded-xl bg-slate-50 border border-dashed border-slate-200 py-10 text-center text-sm text-slate-400">
            Belum ada aduan pada ruangan di denah.
        </div>
    @endif
</div>

@if($adaPanas)
<script>
    (function () {
        var info = document.getElementById('panas-info');
        if (!info) return;
        var awal = info.textContent;
        function tampil(el) { info.innerHTML = '<strong class="text-[#0f2d5c]"></strong> &mdash; <span></span>';
            info.querySelector('strong').textContent = el.dataset.nama;
            info.querySelector('span').textContent = el.dataset.jumlah + ' aduan'; }
        document.querySelectorAll('.js-panas').forEach(function (el) {
            el.addEventListener('mouseenter', function () { tampil(el); });
            el.addEventListener('focus', function () { tampil(el); });
            el.addEventListener('click', function () { tampil(el); });
            el.addEventListener('mouseleave', function () { info.textContent = awal; });
        });
    })();
</script>
@endif