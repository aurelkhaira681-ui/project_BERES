{{-- Lencana prioritas aduan. Pakai: @include('partials.prioritas-badge', ['item' => $aduan]) atau ['item' => $aduan, 'kecil' => true] --}}
@php
    $pMap = [
        'mendesak' => ['Mendesak', 'bg-[#ef4b43] text-white ring-[#ef4b43]', true],
        'sedang'   => ['Sedang',   'bg-[#fff6e0] text-[#b36f00] ring-[#f7d98e]', false],
        'rendah'   => ['Rendah',   'bg-[#e8f1ff] text-[#3a6fb8] ring-[#c3d8f6]', false],
    ];
    [$pLabel, $pCls, $pDenyut] = $pMap[$item->prioritas ?? 'sedang'] ?? $pMap['sedang'];
@endphp
<span class="inline-flex items-center gap-1 rounded-full ring-1 ring-inset {{ ($kecil ?? false) ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs' }} font-bold whitespace-nowrap {{ $pCls }}">
    @if($pDenyut)
        <span class="relative flex h-1.5 w-1.5"><span class="absolute inline-flex h-full w-full rounded-full bg-white opacity-70 animate-ping"></span><span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-white"></span></span>
    @endif
    {{ $pLabel }}
</span>