{{--
    Animasi halaman BERES — dipakai di layouts.dashboard & layouts.app.
    Fitur:
      - Efek mengetik pada judul   : otomatis untuk <h1> pertama di <main>, atau elemen ber-atribut data-typewriter
                                     (opsional data-speed="ms per huruf", data-start="ms tunda awal")
      - Muncul bertahap (stagger)  : anak langsung <main> / elemen ber-atribut data-stagger
      - Angka hitung naik          : elemen ber-atribut data-count="123"
      - Cincin progres             : elemen <circle data-ring="persen" class="b-ring">
      - Tips berganti otomatis     : elemen ber-atribut data-rotator (anak-anaknya = isi tips)
      - Jam digital WIB            : elemen ber-atribut data-clock
      - Dekorasi latar melayang    : otomatis di halaman ber-sidebar
    Semua animasi otomatis mati jika perangkat memakai "reduce motion".
--}}
<style>
    @keyframes bAnimUp    { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
    @keyframes bAnimCaret { 50% { opacity: 0; } }
    @keyframes bAnimGrow  { from { transform: scaleY(0); } to { transform: scaleY(1); } }
    @keyframes bAnimPop   { from { opacity: 0; transform: scale(.55) rotate(-80deg); } to { opacity: 1; transform: none; } }
    @keyframes bAnimWave  { 0%, 55%, 100% { transform: rotate(0); } 10%, 30% { transform: rotate(16deg); } 20%, 40% { transform: rotate(-10deg); } 50% { transform: rotate(8deg); } }
    @keyframes bAnimFloat { 0%, 100% { transform: translate3d(0, 0, 0) rotate(0); } 50% { transform: translate3d(0, -26px, 0) rotate(8deg); } }
    @keyframes bAnimSoft  { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
    @keyframes bAnimBlob  { 0%, 100% { transform: translate3d(0, 0, 0) scale(1); } 50% { transform: translate3d(36px, -28px, 0) scale(1.1); } }
    @keyframes bAnimShine { 0% { transform: translateX(-120%); } 35%, 100% { transform: translateX(120%); } }
    @keyframes bAnimNudge { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(6px); } }
    @keyframes bAnimStep  { 0%, 100% { box-shadow: 0 0 0 0 rgba(31, 111, 229, 0); } 10% { box-shadow: 0 0 0 8px rgba(31, 111, 229, .28); } 22% { box-shadow: 0 0 0 0 rgba(31, 111, 229, 0); } }
    @keyframes bAnimLine  { from { transform: scaleY(0); } to { transform: scaleY(1); } }
    @keyframes bAnimRise  { 0% { transform: translate(0, 0); opacity: 0; } 10% { opacity: 1; } 90% { opacity: .7; } 100% { transform: translate(34px, -115vh); opacity: 0; } }
    @keyframes bAnimBulb  { 0%, 100% { transform: scale(1) rotate(0); filter: none; } 50% { transform: scale(1.18) rotate(-8deg); filter: drop-shadow(0 0 6px rgba(245, 165, 36, .8)); } }

    .b-in      { animation: bAnimUp .65s cubic-bezier(.2, .7, .2, 1) backwards; animation-delay: var(--d, 0ms); }
    .b-caret::after { content: ''; display: inline-block; width: .075em; height: .95em; margin-left: .1em; background: currentColor; vertical-align: -.12em; animation: bAnimCaret .8s steps(1) infinite; }
    .b-bar     { transform-origin: bottom; animation: bAnimGrow .9s cubic-bezier(.2, .7, .2, 1) backwards; animation-delay: var(--d, 0ms); }
    .b-pop     { animation: bAnimPop .9s cubic-bezier(.2, .8, .2, 1) .35s backwards; }
    .b-wave    { transform-origin: 70% 75%; animation: bAnimWave 2.8s ease-in-out 1s infinite; }
    .b-float-soft { animation: bAnimSoft 4s ease-in-out infinite; }
    .b-nudge   { animation: bAnimNudge 1.6s ease-in-out infinite; }
    .b-bulb    { display: inline-block; animation: bAnimBulb 2.8s ease-in-out infinite; }
    .b-line    { transform-origin: top; animation: bAnimLine 1.2s ease-out .5s backwards; }
    .b-step    { animation: bAnimStep 7s ease-out infinite; animation-delay: calc(var(--i, 0) * 1.2s + 1.2s); }
    .b-ring    { transition: stroke-dashoffset 1.4s cubic-bezier(.2, .7, .2, 1) .4s; }
    html.dark .b-ring-track { stroke: #1f3157; }

    .b-lift    { transition: transform .25s ease, box-shadow .25s ease; }
    .b-lift:hover { transform: translateY(-3px); box-shadow: 0 12px 26px rgba(20, 50, 105, .13); }

    .b-shine   { position: relative; overflow: hidden; }
    .b-shine::after { content: ''; position: absolute; inset: 0; pointer-events: none; background: linear-gradient(105deg, transparent 35%, rgba(255, 255, 255, .22) 50%, transparent 65%); transform: translateX(-120%); animation: bAnimShine 4.5s ease-in-out 1.5s infinite; }

    /* tips berganti */
    .b-rot > * { grid-area: 1 / 1; opacity: 0; transform: translateY(10px); transition: opacity .5s ease, transform .5s ease; pointer-events: none; }
    .b-rot > .on { opacity: 1; transform: none; pointer-events: auto; }
    .b-dots { display: flex; gap: 6px; margin-top: 14px; }
    .b-dots span { width: 6px; height: 6px; border-radius: 9999px; background: #b7cdf0; transition: width .3s ease, background .3s ease; }
    .b-dots span.on { width: 18px; background: #1f6fe5; }
    html.dark .b-dots span { background: #2a4274; }
    html.dark .b-dots span.on { background: #86b6ff; }

    /* dekorasi latar halaman dashboard */
    .b-deco { position: fixed; inset: 0; z-index: -1; overflow: hidden; pointer-events: none; }
    .b-blob { position: absolute; border-radius: 9999px; animation: bAnimBlob 20s ease-in-out infinite; }
    .b-ico  { position: absolute; color: #1f6fe5; opacity: .1; animation: bAnimFloat 12s ease-in-out infinite; }
    .b-dots-bg { position: absolute; width: 220px; height: 220px; background-image: radial-gradient(#1f6fe5 1.6px, transparent 1.7px); background-size: 22px 22px; opacity: .13;
                 -webkit-mask-image: radial-gradient(circle, #000 30%, transparent 72%); mask-image: radial-gradient(circle, #000 30%, transparent 72%); }
    html.dark .b-blob { opacity: .18; }
    html.dark .b-ico  { color: #86b6ff; opacity: .09; }
    html.dark .b-dots-bg { opacity: .07; }

    /* gelembung naik (panel kiri halaman login / daftar) */
    .b-bubbles span { position: absolute; bottom: -50px; border-radius: 9999px; background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .2); animation: bAnimRise linear infinite; }

    @media (prefers-reduced-motion: reduce) {
        .b-in, .b-bar, .b-pop, .b-wave, .b-float-soft, .b-nudge, .b-bulb, .b-line, .b-step, .b-blob, .b-ico, .b-bubbles span { animation: none !important; }
        .b-shine::after, .b-caret::after { display: none; }
        .b-ring, .b-rot > *, .b-lift { transition: none; }
        .b-bubbles { display: none; }
    }
</style>

<script>
(function () {
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- efek mengetik ---------- */
    function typewriter(el) {
        var full = el.textContent.replace(/\s+/g, ' ').trim();
        if (!full) return;
        var chars = Array.from(full);                       // aman untuk emoji
        var speed = parseInt(el.getAttribute('data-speed') || '48', 10);
        var start = parseInt(el.getAttribute('data-start') || '250', 10);
        var late  = el.nextElementSibling && el.nextElementSibling.tagName === 'P' ? el.nextElementSibling : null;

        el.setAttribute('aria-label', full);
        el.style.minHeight = el.offsetHeight + 'px';        // cegah halaman "loncat"
        el.textContent = '';
        el.classList.add('b-caret');
        if (late) { late.setAttribute('data-late', ''); late.style.opacity = '0'; }

        var i = 0;
        function tick() {
            i++;
            el.textContent = chars.slice(0, i).join('');
            if (i < chars.length) { setTimeout(tick, speed + Math.random() * 22); return; }
            if (late) { late.style.opacity = ''; late.classList.add('b-in'); }
            setTimeout(function () { el.classList.remove('b-caret'); el.style.minHeight = ''; }, 1500);
        }
        setTimeout(tick, start);
    }

    /* ---------- muncul bertahap ---------- */
    function stagger(root, base) {
        var list = [];
        Array.prototype.forEach.call(root.children, function (k) {
            if (/^(SCRIPT|STYLE|TEMPLATE)$/.test(k.tagName)) return;
            if (k.hasAttribute('data-late') || (k.id && /modal/i.test(k.id))) return;
            var cs = getComputedStyle(k);
            if (cs.display === 'none' || cs.position === 'fixed') return;
            if (k.classList.contains('grid') && k.children.length > 1) {
                Array.prototype.forEach.call(k.children, function (c) {
                    var cc = getComputedStyle(c);
                    if (cc.display !== 'none' && cc.position !== 'fixed') list.push(c);
                });
            } else { list.push(k); }
        });
        list.forEach(function (el, i) {
            el.style.setProperty('--d', (base + Math.min(i * 90, 900)) + 'ms');
            el.classList.add('b-in');
        });
    }

    /* ---------- angka hitung naik ---------- */
    function countUp(el) {
        var end = parseFloat(el.getAttribute('data-count'));
        if (isNaN(end)) return;
        var dur = 1100, t0 = null;
        el.textContent = '0';
        function step(ts) {
            if (t0 === null) t0 = ts;
            var p = Math.min(1, (ts - t0) / dur), e = 1 - Math.pow(1 - p, 3);
            el.textContent = p < 1 ? Math.round(end * e) : String(end);
            if (p < 1) requestAnimationFrame(step);
        }
        setTimeout(function () { requestAnimationFrame(step); }, 400);
    }

    /* ---------- dekorasi latar ---------- */
    function deco() {
        var icons = {
            wrench: '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.1L3 17.7 6.3 21l6.3-6.3a4 4 0 0 0 5.1-5.4l-2.6 2.6-2.4-.6-.6-2.4z"/>',
            bolt:   '<path d="M13 2 4 14h6l-1 8 9-12h-6z"/>',
            bell:   '<path d="M6 16V11a6 6 0 0 1 12 0v5l1.5 2h-15zM10 20a2 2 0 0 0 4 0"/>',
            shield: '<path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/><path d="M8.5 12l2.5 2.5L15.5 10"/>',
            clip:   '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6v3H9zM9 12h6M9 16h4"/>',
            bulb:   '<path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1 2.1h5c0-.9.4-1.6 1-2.1A6 6 0 0 0 12 3z"/>'
        };
        // [ikon, kiri%, atas%, ukuran px, durasi s, tunda s]
        var pos = [
            ['wrench', 5, 14, 38, 13, 0], ['bolt', 92, 10, 30, 11, -3], ['bell', 88, 46, 34, 14, -6],
            ['shield', 6, 62, 42, 12, -2], ['clip', 94, 80, 36, 15, -8], ['bulb', 48, 92, 32, 12, -5],
            ['bolt', 30, 6, 26, 10, -4], ['wrench', 72, 90, 30, 13, -9]
        ];
        var html = '<div class="b-blob" style="width:520px;height:520px;left:-140px;top:-120px;background:radial-gradient(circle at 35% 35%,rgba(79,140,255,.32),transparent 68%)"></div>' +
                   '<div class="b-blob" style="width:460px;height:460px;right:-120px;top:30%;background:radial-gradient(circle at 40% 40%,rgba(120,190,255,.30),transparent 68%);animation-delay:-7s"></div>' +
                   '<div class="b-blob" style="width:420px;height:420px;left:35%;bottom:-180px;background:radial-gradient(circle at 40% 40%,rgba(150,130,255,.20),transparent 68%);animation-delay:-13s"></div>' +
                   '<div class="b-dots-bg" style="right:40px;top:70px"></div><div class="b-dots-bg" style="left:280px;bottom:40px"></div>';
        pos.forEach(function (p) {
            html += '<svg class="b-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" ' +
                    'style="left:' + p[1] + '%;top:' + p[2] + '%;width:' + p[3] + 'px;height:' + p[3] + 'px;animation-duration:' + p[4] + 's;animation-delay:' + p[5] + 's">' + icons[p[0]] + '</svg>';
        });
        var d = document.createElement('div');
        d.className = 'b-deco';
        d.setAttribute('aria-hidden', 'true');
        d.innerHTML = html;
        document.body.insertBefore(d, document.body.firstChild);
    }

    /* ---------- tips berganti otomatis ---------- */
    function rotator(box) {
        var items = Array.prototype.slice.call(box.children);
        if (items.length < 2) return;
        var dots = document.createElement('div');
        dots.className = 'b-dots';
        items.forEach(function (_, i) { var s = document.createElement('span'); if (i === 0) s.className = 'on'; dots.appendChild(s); });
        box.parentNode.insertBefore(dots, box.nextSibling);
        var cur = 0;
        setInterval(function () {
            if (document.hidden) return;
            items[cur].classList.remove('on'); dots.children[cur].classList.remove('on');
            cur = (cur + 1) % items.length;
            items[cur].classList.add('on'); dots.children[cur].classList.add('on');
        }, 5200);
    }

    /* ---------- jam digital WIB ---------- */
    function clock(el) {
        function pad(n) { return n < 10 ? '0' + n : '' + n; }
        function tick() {
            var s;
            try {
                s = new Date().toLocaleTimeString('en-GB', { timeZone: 'Asia/Jakarta', hour12: false });
            } catch (e) {
                var n = new Date(); s = pad(n.getHours()) + ':' + pad(n.getMinutes()) + ':' + pad(n.getSeconds());
            }
            el.textContent = s + ' WIB';
        }
        tick(); setInterval(tick, 1000);
    }

    function init() {
        // judul yang diketik
        var targets = Array.prototype.slice.call(document.querySelectorAll('[data-typewriter]'));
        var h1 = document.querySelector('main h1');
        if (h1 && targets.indexOf(h1) === -1 && !h1.hasAttribute('data-i18n') && h1.children.length === 0 && h1.textContent.trim().length <= 90) targets.push(h1);

        document.querySelectorAll('[data-clock]').forEach(clock);
        document.querySelectorAll('[data-ring]').forEach(function (c) {
            var p = Math.max(0, Math.min(100, parseFloat(c.getAttribute('data-ring')) || 0));
            var len = parseFloat(c.getAttribute('stroke-dasharray')) || 263.9;
            setTimeout(function () { c.style.strokeDashoffset = len * (1 - p / 100); }, reduce ? 0 : 60);
        });

        if (reduce) return;

        targets.forEach(typewriter);                        // dulu: menandai <p> pendamping agar tidak ikut stagger
        var main = document.querySelector('main');
        if (main) stagger(main, 60);
        document.querySelectorAll('[data-stagger]').forEach(function (r) { stagger(r, 120); });

        document.querySelectorAll('[data-count]').forEach(countUp);
        document.querySelectorAll('.b-bar').forEach(function (b) {
            var idx = Array.prototype.indexOf.call(b.parentNode.children, b);
            b.style.setProperty('--d', (450 + idx * 110) + 'ms');
        });
        document.querySelectorAll('[data-rotator]').forEach(rotator);
        if (document.getElementById('sidebar')) deco();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>