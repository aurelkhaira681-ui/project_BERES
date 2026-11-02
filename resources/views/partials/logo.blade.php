{{--
    Logo BERES beranimasi.
    Parameter opsional:
      $box  => ukuran kotak, default 'w-10 h-10'
      $tone => 'light' (di atas latar biru, default) | 'solid' (di atas latar putih)
--}}
@php
    $box  = $box  ?? 'w-10 h-10';
    $tone = $tone ?? 'light';
    $bg   = $tone === 'solid' ? 'bg-[#16305c] text-white' : 'bg-white/15 text-white';
    $ring = $tone === 'solid' ? '#2a5091' : 'rgba(255,255,255,.7)';
@endphp

<span class="beres-logo relative inline-flex shrink-0 items-center justify-center rounded-lg {{ $box }} {{ $bg }}" style="--beres-ring: {{ $ring }}" aria-hidden="true">
    <span class="beres-ring"></span>
    <svg viewBox="0 0 24 24" class="relative w-3/5 h-3/5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
        <path class="beres-check" d="M5 12.5l4.5 4.5L19 7.5" pathLength="1"/>
    </svg>
</span>

@once
<style>
    .beres-logo  { animation: beres-float 3s ease-in-out infinite; }
    .beres-ring  { position: absolute; inset: 0; border-radius: inherit; border: 2px solid var(--beres-ring); opacity: 0; animation: beres-ring 2.6s ease-out infinite; }
    .beres-check { stroke-dasharray: 1; stroke-dashoffset: 1; animation: beres-draw 2.6s ease-in-out infinite; }

    @keyframes beres-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
    /* centang digambar -> ditahan -> dihapus, lalu ulang */
    @keyframes beres-draw  { 0% { stroke-dashoffset: 1; } 35%, 80% { stroke-dashoffset: 0; } 100% { stroke-dashoffset: -1; } }
    /* cincin memancar saat centang selesai */
    @keyframes beres-ring  { 0%, 34% { transform: scale(1); opacity: 0; } 38% { opacity: .8; } 100% { transform: scale(1.7); opacity: 0; } }

    @media (prefers-reduced-motion: reduce) {
        .beres-logo, .beres-ring, .beres-check { animation: none; }
        .beres-check { stroke-dashoffset: 0; }
    }
</style>
@endonce