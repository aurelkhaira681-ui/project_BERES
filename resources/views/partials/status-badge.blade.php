{{-- Badge status aduan versi siswa. Pakai: @include('partials.status-badge', ['item' => $aduan]) --}}
@php
    $map = [
        'pending'  => ['Menunggu', 'bg-[#fff1d6] text-[#c97f00]'],
        'approved' => ['Diproses', 'bg-[#dcebff] text-[#1d6fd8]'],
        'selesai'  => ['Selesai',  'bg-[#d9f4e5] text-[#15935a]'],
        'rejected' => ['Ditolak',  'bg-[#fde2e0] text-[#dc3f36]'],
    ];
    [$label, $cls] = $map[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-600'];
@endphp
<span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap {{ $cls }}">
    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $label }}
</span>