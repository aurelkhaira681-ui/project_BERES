<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-toggle-password]');
    if (!btn) return;
    var input = btn.parentElement.querySelector('input');
    var tampil = input.type === 'password';
    input.type = tampil ? 'text' : 'password';
    btn.setAttribute('aria-pressed', tampil ? 'true' : 'false');
    btn.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    btn.querySelector('[data-eye=off]').classList.toggle('hidden', tampil);
    btn.querySelector('[data-eye=on]').classList.toggle('hidden', !tampil);
});
</script>