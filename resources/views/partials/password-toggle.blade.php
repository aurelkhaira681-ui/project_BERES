{{-- Tombol mata untuk input kata sandi. Pakai: letakkan di dalam div.relative setelah <input>. --}}
<button type="button" data-toggle-password aria-label="Tampilkan kata sandi" aria-pressed="false"
        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-400 hover:text-[#1e3f7f]">
    <svg data-eye="off" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10 10 0 0 1 12 6c5 0 8.5 4 10 6-.6.8-1.6 2-3 3.1M6.5 7.6C4.4 9 2.9 10.9 2 12c1.5 2 5 6 10 6 1.5 0 2.8-.4 4-.9"/><path d="M9.9 10a3 3 0 0 0 4.1 4.1"/></svg>
    <svg data-eye="on" class="w-[18px] h-[18px] hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12c1.5-2 5-6 10-6s8.5 4 10 6c-1.5 2-5 6-10 6S3.5 14 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
</button>