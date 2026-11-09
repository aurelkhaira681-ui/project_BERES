{{-- Badge status aduan versi siswa. Pakai: @include('partials.status-badge', ['item' => $aduan]) --}}
@php
    // label, warna pil, warna titik, apakah titik berdenyut
    $map = [
        'pending'  => ['Menunggu', 'bg-[#fff6e0] text-[#b36f00] ring-[#f7d98e]', 'bg-[#f5a524]', true],
        'approved' => ['Diproses', 'bg-[#e6f1ff] text-[#1a63c4] ring-[#b9d6fb]', 'bg-[#2f7cf6]', true],
        'selesai'  => ['Selesai',  'bg-[#e3f7ec] text-[#127a4b] ring-[#a9e2c4]', 'bg-[#22a06b]', false],
        'rejected' => ['Ditolak',  'bg-[#fdeceb] text-[#c2352d] ring-[#f5bcb8]', 'bg-[#e5484d]', false],
    ];
    [$label, $cls, $dot, $pulse] = $map[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-600 ring-slate-200', 'bg-slate-400', false];
@endphp
<span class="inline-flex items-center gap-2 rounded-full ring-1 ring-inset px-3 py-1 text-xs font-bold whitespace-nowrap {{ $cls }}">
    <span class="relative flex h-2 w-2">
        @if($pulse)<span class="absolute inline-flex h-full w-full rounded-full opacity-60 animate-ping {{ $dot }}"></span>@endif
        <span class="relative inline-flex h-2 w-2 rounded-full {{ $dot }}"></span>
    </span>{{ $label }}
</span>