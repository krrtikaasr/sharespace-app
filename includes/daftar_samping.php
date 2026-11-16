<?php /* Butuh: $daftarSamping (array donasi), $idAktif (int). Hanya tampil di desktop. */ ?>
<aside class="hidden lg:block w-[420px] shrink-0 space-y-4">
    <?php foreach ($daftarSamping as $s): $aktif = ((int)$s['id'] === (int)$idAktif); ?>
        <a href="<?= e(link_donasi($s)) ?>" class="flex items-center gap-4 p-4 rounded-[20px] transition <?= $aktif ? 'border-2 border-[#2563EB] bg-[#F5F9FF]' : 'border border-slate-200 bg-white hover:border-blue-300' ?>">
            <?= foto_atau_placeholder($s['foto'], $s['nama_barang'], 'w-[72px] h-[72px] rounded-2xl shrink-0') ?>
            <span class="min-w-0 flex-1">
                <span class="block font-bold text-base truncate"><?= e($s['nama_barang']) ?></span>
                <span class="block text-xs text-slate-500 mt-0.5"><?= (int)$s['jumlah'] ?> unit • <?= e(waktu_relatif($s['created_at'])) ?></span>
                <span class="block mt-1.5"><?= badge_status($s['status']) ?></span>
            </span>
            <?= icon('chev-r', 'w-[18px] h-[18px] shrink-0 ' . ($aktif ? 'text-[#2563EB]' : 'text-slate-300')) ?>
        </a>
    <?php endforeach; ?>
    <?php include __DIR__ . '/banner.php'; ?>
</aside>
