{{-- Kartu standar BERES. Contoh: <x-card class="p-5"> ... </x-card> --}}
<div {{ $attributes->merge(['class' => $card]) }}>{{ $slot }}</div>