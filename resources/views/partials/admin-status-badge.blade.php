{{-- Badge status aduan versi admin. Pakai: @include('partials.admin-status-badge', ['item' => $aduan]) --}}
@php
    $map = [
        'pending'  => ['Pending',   'bg-[#fff6e0] text-[#b36f00] ring-[#f7d98e]', 'bg-[#f5a524]'],
        'approved' => ['Disetujui', 'bg-[#e3f7ec] text-[#127a4b] ring-[#a9e2c4]', 'bg-[#22a06b]'],
        'selesai'  => ['Selesai',   'bg-[#e6f1ff] text-[#1a63c4] ring-[#b9d6fb]', 'bg-[#2f7cf6]'],
        'rejected' => ['Ditolak',   'bg-[#fdeceb] text-[#c2352d] ring-[#f5bcb8]', 'bg-[#e5484d]'],
    ];
    [$label, $cls, $dot] = $map[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-600 ring-slate-200', 'bg-slate-400'];
@endphp
<span class="inline-flex items-center gap-1.5 rounded-full ring-1 ring-inset px-3 py-1 text-xs font-bold whitespace-nowrap {{ $cls }}"><span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>{{ $label }}</span>