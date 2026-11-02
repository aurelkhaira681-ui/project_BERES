{{-- Gelembung naik pelan-pelan untuk panel biru (login / daftar). Taruh di dalam elemen "relative overflow-hidden". --}}
<div class="b-bubbles absolute inset-0 pointer-events-none" aria-hidden="true">
    @foreach([[6, 14, 15], [17, 8, 11], [29, 20, 19], [41, 10, 13], [53, 16, 17], [64, 7, 10], [74, 22, 21], [85, 12, 14], [93, 9, 12], [35, 6, 9]] as $i => [$kiri, $ukuran, $durasi])
        <span style="left: {{ $kiri }}%; width: {{ $ukuran }}px; height: {{ $ukuran }}px; animation-duration: {{ $durasi }}s; animation-delay: -{{ $i * 2.1 }}s"></span>
    @endforeach
</div>