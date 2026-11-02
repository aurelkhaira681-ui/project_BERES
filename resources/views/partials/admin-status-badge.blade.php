{{-- Badge status aduan versi admin. Pakai: @include('partials.admin-status-badge', ['item' => $aduan]) --}}
@php
    $map = [
        'pending'  => ['Pending',   'bg-[#fff1d6] text-[#c97f00]'],
        'approved' => ['Disetujui', 'bg-[#d9f4e5] text-[#15935a]'],
        'selesai'  => ['Selesai',   'bg-[#dcebff] text-[#1d6fd8]'],
        'rejected' => ['Ditolak',   'bg-[#fde2e0] text-[#dc3f36]'],
    ];
    [$label, $cls] = $map[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-600'];
@endphp
<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap {{ $cls }}">{{ $label }}</span>