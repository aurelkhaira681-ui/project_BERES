{{-- Gelembung naik pelan-pelan untuk panel biru (login / daftar). Taruh di dalam elemen "relative overflow-hidden". --}}
<div class="b-bubbles absolute inset-0 pointer-events-none" aria-hidden="true">
    @foreach([[6, 14, 15], [17, 8, 11], [29, 20, 19], [41, 10, 13], [53, 16, 17], [64, 7, 10], [74, 22, 21], [85, 12, 14], [93, 9, 12], [35, 6, 9]] as $i => [$kiri, $ukuran, $durasi])
        <span style="left: {{ $kiri }}%; width: {{ $ukuran }}px; height: {{ $ukuran }}px; animation-duration: {{ $durasi }}s; animation-delay: -{{ $i * 2.1 }}s"></span>
    @endforeach
    @foreach([
        ['<path d="M14.7 6.3a4 4 0 0 0-5.4 5.1L3 17.7 6.3 21l6.3-6.3a4 4 0 0 0 5.1-5.4l-2.6 2.6-2.4-.6-.6-2.4z"/>', 58, 27, 40, 0],
        ['<path d="M6 16V11a6 6 0 0 1 12 0v5l1.5 2h-15zM10 20a2 2 0 0 0 4 0"/>', 14, 40, 34, -4],
        ['<path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/><path d="M8.5 12l2.5 2.5L15.5 10"/>', 40, 73, 44, -7],
        ['<path d="M13 2 4 14h6l-1 8 9-12h-6z"/>', 80, 62, 30, -2],
    ] as [$ikonPath, $kiriIkon, $atasIkon, $ukuranIkon, $tundaIkon])
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
             style="left: {{ $kiriIkon }}%; top: {{ $atasIkon }}%; width: {{ $ukuranIkon }}px; height: {{ $ukuranIkon }}px; animation-delay: {{ $tundaIkon }}s">{!! $ikonPath !!}</svg>
    @endforeach
</div>