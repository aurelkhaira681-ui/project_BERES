{{-- Panel kiri halaman Daftar: sama dengan panel kiri halaman Masuk --}}
<div class="hidden lg:flex relative flex-col justify-between gap-8 overflow-hidden bg-gradient-to-b from-[#1c407a] via-[#0d2f66] to-[#0c2e65] text-white px-12 xl:px-16 py-10">
    <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-white/5"></div>
    <div class="absolute top-6 right-24 w-10 h-10 rounded-full bg-[#2a5091]/70"></div>
    <div class="absolute -bottom-6 -left-6 w-14 h-14 rounded-full bg-[#2a5091]/70"></div>
    <div class="absolute -bottom-40 -right-24 w-[480px] h-[300px] rounded-full bg-[#2a5bb0]/40 rotate-[-12deg]"></div>
    @include('partials.bubbles')

    <div class="relative">
        <div class="flex items-center gap-5">
            @include('partials.logo-brand', ['class' => 'w-24 xl:w-28'])
            <div>
                <h1 class="text-5xl xl:text-6xl font-extrabold tracking-tight leading-none">BERES</h1>
                <p class="text-white/85 mt-1.5">Sistem Pengaduan Sarana Sekolah</p>
            </div>
        </div>
        <p class="mt-8 text-xl xl:text-2xl leading-snug max-w-md text-white/95">
            Laporkan kerusakan sarana sekolah, pantau statusnya sampai selesai.
        </p>
        <div class="mt-4 h-1 w-16 rounded-full bg-[#4a86e8]"></div>
    </div>

    <div class="relative -mx-4">
        <img src="{{ asset('images/login-illustration.jpg') }}" alt="Siswa melaporkan kerusakan di SMK Negeri 5 Surakarta" class="login-illus w-full h-auto select-none" draggable="false">
        <div class="login-card-float absolute right-[3%] top-[2%] w-[27%] min-w-[150px] bg-white text-[#142b4e] rounded-xl shadow-xl shadow-black/20 px-3 py-2.5 flex items-center gap-2.5">
            <span class="shrink-0 w-8 h-8 rounded-full bg-[#2f63c8] flex items-center justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
            </span>
            <span class="leading-tight">
                <span class="block text-[13px] font-bold">Laporan terkirim</span>
                <span class="block text-[11px] text-[#2f63c8]">Terima kasih telah melapor!</span>
            </span>
        </div>
    </div>

    <div class="relative grid grid-cols-3">
        <div class="pr-4">
            <span class="mb-3 flex w-11 h-11 items-center justify-center rounded-xl bg-[#5c9cf2]">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9a2 2 0 0 0 0 6v2a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-2a2 2 0 0 1 0-6V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"/><path d="M9 9h6M9 12h6M9 15h3"/></svg>
            </span>
            <p class="font-bold text-[15px] leading-tight">Sistem Tiket Terpantau</p>
            <p class="text-white/65 text-[13px] mt-1.5 leading-relaxed">Setiap aduan punya nomor tiket &amp; status real-time.</p>
        </div>
        <div class="px-4 border-l border-white/10">
            <span class="mb-3 flex w-11 h-11 items-center justify-center rounded-xl bg-[#8db3f6]">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#fff"><path d="M13 2 4 14h6l-1 8 9-12h-6z"/></svg>
            </span>
            <p class="font-bold text-[15px] leading-tight">Tindak Lanjut Cepat</p>
            <p class="text-white/65 text-[13px] mt-1.5 leading-relaxed">Diverifikasi langsung oleh Admin/Petugas sekolah.</p>
        </div>
        <div class="pl-4 border-l border-white/10">
            <span class="mb-3 flex w-11 h-11 items-center justify-center rounded-xl bg-[#c9daf8]">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#2f63c8"><rect x="4" y="12" width="4" height="8" rx="1"/><rect x="10" y="7" width="4" height="13" rx="1"/><rect x="16" y="3" width="4" height="17" rx="1"/></svg>
            </span>
            <p class="font-bold text-[15px] leading-tight">Rekap Transparan</p>
            <p class="text-white/65 text-[13px] mt-1.5 leading-relaxed">Riwayat &amp; laporan tersimpan rapi untuk evaluasi.</p>
        </div>
    </div>
</div>