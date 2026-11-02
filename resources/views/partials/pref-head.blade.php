{{-- Preferensi tampilan (tema & bahasa). Disimpan di localStorage browser. --}}
<script>
(function () {
    var KEY = 'beres_pref';
    var DEF = { tema: 'light', bahasa: 'id', notif: true };

    // Kamus Indonesia -> English (kunci = teks asli Indonesia pada elemen [data-i18n])
    var EN = {
        'Menu Saya': 'My Menu', 'Menu Utama': 'Main Menu',
        'Beranda': 'Home', 'Ajukan Aduan': 'Submit Complaint', 'Riwayat Aduan': 'Complaint History',
        'Akun Saya': 'My Account', 'Dashboard': 'Dashboard', 'Data Aduan': 'Complaint Data',
        'Master Data': 'Master Data', 'Keluar': 'Log Out',
        'Kelola biodata & preferensi akun kamu': 'Manage your profile & account preferences',
        'Ubah Password': 'Change Password', 'Informasi Profil': 'Profile Information', 'Edit': 'Edit',
        'Nama Lengkap': 'Full Name', 'Kelas': 'Class', 'No. Presensi': 'Attendance No.',
        'Konsentrasi Keahlian': 'Major', 'Email': 'Email', 'No. HP': 'Phone No.', 'Peran': 'Role',
        'Preferensi': 'Preferences', 'Notifikasi': 'Notifications', 'Atur notifikasi aduan': 'Manage complaint notifications',
        'Tema': 'Theme', 'Bahasa': 'Language', 'Simpan Perubahan': 'Save Changes', 'Batal': 'Cancel', 'Tutup': 'Close',
        'Password Saat Ini': 'Current Password', 'Password Baru': 'New Password',
        'Ulangi Password Baru': 'Repeat New Password', 'Simpan Password': 'Save Password',
        'Terima notifikasi aduan': 'Receive complaint notifications',
        'Menampilkan lencana jumlah aduan pada menu dan lonceng.': 'Shows complaint count badges on the menu and bell.',
        'Mode Terang': 'Light Mode', 'Mode Gelap': 'Dark Mode',
        'Admin / Petugas': 'Admin / Officer'
    };

    function get() {
        try { return Object.assign({}, DEF, JSON.parse(localStorage.getItem(KEY) || '{}')); }
        catch (e) { return Object.assign({}, DEF); }
    }
    function save(p) { try { localStorage.setItem(KEY, JSON.stringify(p)); } catch (e) {} }

    function t(idText) { return get().bahasa === 'en' ? (EN[idText] || idText) : idText; }

    function translate() {
        var en = get().bahasa === 'en';
        document.querySelectorAll('[data-i18n]').forEach(function (el) {
            if (el.dataset.idText === undefined) el.dataset.idText = el.textContent.trim();
            el.textContent = en ? (EN[el.dataset.idText] || el.dataset.idText) : el.dataset.idText;
        });
        document.documentElement.lang = en ? 'en' : 'id';
        var p = get();
        document.querySelectorAll('[data-pref-sub]').forEach(function (el) {
            var k = el.dataset.prefSub;
            if (k === 'tema') el.textContent = p.tema === 'dark' ? t('Mode Gelap') : t('Mode Terang');
            if (k === 'bahasa') el.textContent = p.bahasa === 'en' ? 'English' : 'Indonesia';
        });
    }

    function apply() {
        var p = get();
        document.documentElement.classList.toggle('dark', p.tema === 'dark');
        document.querySelectorAll('.js-notif-badge').forEach(function (el) { el.style.display = p.notif ? '' : 'none'; });
        translate();
    }

    window.BERES_PREF = {
        get: get, t: t, apply: apply,
        set: function (k, v) { var p = get(); p[k] = v; save(p); apply(); }
    };

    // terapkan tema secepat mungkin supaya tidak berkedip
    if (get().tema === 'dark') document.documentElement.classList.add('dark');
    document.addEventListener('DOMContentLoaded', apply);
})();
</script>
<style>
    html.dark { color-scheme: dark; }
    html.dark body, html.dark body[class*="bg-gradient-to-"] { background: #0a1224 !important; color: #dbe6f7; }
    html.dark [class~="bg-white"], html.dark [class~="bg-white/80"], html.dark [class~="bg-slate-50"] { background-color: #14213d !important; }
    html.dark [class~="bg-[#e6effb]"], html.dark [class~="bg-[#eef3fb]"], html.dark [class~="bg-slate-100"], html.dark [class~="bg-slate-200"] { background-color: #1f3157 !important; }
    html.dark [class*="bg-gradient-to-"]:not(aside):not(body) { background-image: none !important; background-color: #16264a !important; border-color: #263b66 !important; }
    html.dark [class~="text-[#0f2d5c]"], html.dark [class~="text-slate-800"], html.dark [class~="text-slate-700"] { color: #e8f0ff !important; }
    html.dark [class~="text-[#40608f]"], html.dark [class~="text-[#5d7aa8]"], html.dark [class~="text-slate-600"], html.dark [class~="text-slate-500"] { color: #a5b9d8 !important; }
    html.dark [class~="text-slate-400"], html.dark [class~="text-slate-300"] { color: #8fa3c4 !important; }
    html.dark [class~="text-[#1d4f9c]"], html.dark [class~="text-[#1f4f9c]"] { color: #86b6ff !important; }
    html.dark [class~="border-slate-100"], html.dark [class~="border-slate-200"] { border-color: #263b66 !important; }
    html.dark [class~="divide-slate-100"] > * + * { border-color: #263b66 !important; }
    html.dark [class~="bg-red-50"] { background-color: #3a1a1f !important; }
    html.dark [class~="bg-green-50"] { background-color: #12301f !important; }
    html.dark input:not([type=checkbox]):not([type=radio]), html.dark select, html.dark textarea { background-color: #0f1a31 !important; color: #e8f0ff !important; border-color: #2a4070 !important; }
    html.dark [class*="hover:bg-[#f6f9ff]"]:hover, html.dark [class*="hover:bg-slate-50"]:hover, html.dark [class*="hover:bg-white"]:hover { background-color: #1a2b50 !important; }
    html.dark [class*="hover:bg-[#e6effb]"]:hover, html.dark [class*="hover:bg-[#d8e5f8]"]:hover { background-color: #2a4274 !important; }
</style>