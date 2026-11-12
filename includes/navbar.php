<?php
/** Navbar bawah. Set $navAktif = 'jelajahi' | 'bagikan' | 'aktivitas' | 'profil' sebelum include. */
$navAktif = $navAktif ?? 'aktivitas';
$menu = [
    'jelajahi'  => ['Jelajahi',  'compass',     'dashboard.php'],
    'bagikan'   => ['Bagikan',   'plus-circle', 'tambah-donasi.php'],
    'aktivitas' => ['Aktivitas', 'inbox',       'status-donasi.php'],
    'profil'    => ['Profil',    'user',        'profil.php'],
];
?>
<nav class="fixed bottom-0 inset-x-0 mx-auto max-w-[430px] z-30 border-t border-slate-100 bg-white pt-1.5 pb-3 px-3 flex justify-between items-center">
    <?php foreach ($menu as $key => [$label, $ikon, $href]):
        $aktif = ($key === $navAktif); ?>
        <a href="<?= e($href) ?>"
           class="flex-1 flex flex-col items-center gap-0.5 py-1.5 rounded-xl text-[10px] transition-colors duration-200
                  <?= $aktif ? 'text-[#2563EB] font-bold' : 'text-slate-400 hover:text-[#2563EB] hover:bg-blue-50' ?>">
            <?= icon($ikon, 'w-[18px] h-[18px]') ?>
            <span><?= e($label) ?></span>
        </a>
    <?php endforeach; ?>
</nav>
