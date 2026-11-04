{{-- Menyalin bahasa pilihan pengguna (localStorage) ke cookie agar pesan dari server mengikuti bahasa tersebut. --}}
<script>
(function () {
    try {
        var p = JSON.parse(localStorage.getItem('beres_pref') || '{}');
        document.cookie = 'beres_lang=' + (p.bahasa === 'en' ? 'en' : 'id') + '; path=/; max-age=31536000; SameSite=Lax';
    } catch (e) {}
})();
</script>