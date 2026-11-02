{{--
    Logo BERES versi besar (perisai + centang + bintang), beranimasi.
    Parameter opsional:
      $class => ukuran, default 'w-24'
      $tone  => 'light' (di atas latar biru, default) | 'dark' (di atas latar terang)
--}}
@php
    $class = $class ?? 'w-24';
    $tone  = $tone  ?? 'light';
    $isDark = $tone === 'dark';
    $star   = $isDark ? '#1e4fa8' : '#ffffff';
    $from   = $isDark ? '#2a5aa8' : '#ffffff';
    $to     = $isDark ? '#16305c' : '#bcd3ff';
    $check  = $isDark ? '#ffffff' : '#1e4fa8';
    $gid    = 'bl-grad-' . $tone;
@endphp

<svg viewBox="0 0 125 110" class="bl-logo shrink-0 {{ $class }}" fill="none" aria-hidden="true">
    <defs>
        <linearGradient id="{{ $gid }}" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="{{ $from }}"/>
            <stop offset="1" stop-color="{{ $to }}"/>
        </linearGradient>
    </defs>

    <g fill="{{ $star }}">
        <polygon class="bl-star bl-s1" points="22.0,17.0 25.4,25.3 34.4,26.0 27.6,31.8 29.6,40.5 22.0,35.9 14.4,40.5 16.4,31.8 9.6,26.0 18.6,25.3"/>
        <polygon class="bl-star bl-s2" points="48.0,5.0 50.4,10.7 56.6,11.2 51.9,15.3 53.3,21.3 48.0,18.1 42.7,21.3 44.1,15.3 39.4,11.2 45.6,10.7"/>
        <polygon class="bl-star bl-s3" points="12.0,50.0 14.6,56.4 21.5,56.9 16.3,61.4 17.9,68.1 12.0,64.5 6.1,68.1 7.7,61.4 2.5,56.9 9.4,56.4"/>
        <polygon class="bl-star bl-s4" points="30.0,79.0 31.9,83.5 36.7,83.8 33.0,87.0 34.1,91.7 30.0,89.2 25.9,91.7 27.0,87.0 23.3,83.8 28.1,83.5"/>
    </g>

    <g class="bl-shield">
        <path d="M78 22C66 22 52 26 46 29V56C46 80 62 96 78 104C94 96 110 80 110 56V29C104 26 90 22 78 22Z" fill="url(#{{ $gid }})"/>
        <path class="bl-check" d="M60 58L74 72L102 38" pathLength="1" stroke="{{ $check }}" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
    </g>
</svg>

@once
<style>
    .bl-shield { animation: bl-float 3.2s ease-in-out infinite; }
    .bl-check  { stroke-dasharray: 1; stroke-dashoffset: 1; animation: bl-draw 3.2s ease-in-out infinite; }
    .bl-star   { transform-box: fill-box; transform-origin: center; animation: bl-twinkle 2.4s ease-in-out infinite; }
    .bl-s2 { animation-delay: .5s; } .bl-s3 { animation-delay: 1s; } .bl-s4 { animation-delay: 1.5s; }

    @keyframes bl-float   { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
    @keyframes bl-draw    { 0% { stroke-dashoffset: 1; } 30%, 82% { stroke-dashoffset: 0; } 100% { stroke-dashoffset: -1; } }
    @keyframes bl-twinkle { 0%, 100% { transform: scale(1) rotate(0); opacity: 1; } 50% { transform: scale(.55) rotate(25deg); opacity: .45; } }

    @media (prefers-reduced-motion: reduce) {
        .bl-shield, .bl-check, .bl-star { animation: none; }
        .bl-check { stroke-dashoffset: 0; }
    }
</style>
@endonce