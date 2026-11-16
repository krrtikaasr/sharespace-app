    </main>
</div>

<!-- ===== Navbar bawah (HP) ===== -->
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-slate-100 flex justify-between px-3 pt-1.5" style="padding-bottom: calc(0.5rem + env(safe-area-inset-bottom))">
    <?php foreach ($menu as $key => [$label, $ikon, $href]): $aktif = ($key === $navAktif); ?>
        <a href="<?= e($href) ?>" class="flex-1 flex flex-col items-center gap-0.5 py-1.5 rounded-xl text-[10px] transition-colors <?= $aktif ? 'text-[#2563EB] font-bold' : 'text-slate-400 hover:text-[#2563EB] hover:bg-blue-50' ?>">
            <?= icon($ikon, 'w-[18px] h-[18px]') ?><span><?= e($label) ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<script>
(function () {
    var btn = document.getElementById('btnNotif'), pnl = document.getElementById('panelNotif');
    if (btn && pnl) {
        btn.addEventListener('click', function (e) { e.stopPropagation(); pnl.classList.toggle('hidden'); });
        document.addEventListener('click', function (e) { if (!pnl.contains(e.target)) pnl.classList.add('hidden'); });
    }
    var baca = document.getElementById('btnBacaSemua');
    if (baca) {
        baca.addEventListener('click', function () {
            fetch('<?= e($BASE) ?>aksi-notifikasi.php', { method: 'POST', body: new URLSearchParams({ csrf: '<?= csrf_token() ?>' }) }).catch(function () {});
            document.querySelectorAll('[data-notif-dot]').forEach(function (el) { el.remove(); });
            var d = document.getElementById('dotNotif'); if (d) d.remove();
        });
    }
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('<?= e($BASE) ?>sw.js').catch(function () {});
        });
    }
})();
</script>
<?php if (!empty($skripHalaman)) echo $skripHalaman; ?>
</body>
</html>
