<?php
session_start();
require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$userId     = current_user_id();
$donasi     = [];
$permintaan = [];
$dbError    = null;

if ($pdo && $userId) {
    try {
        // Tab "Donasi Saya"
        $donasi = ambil_donasi_user($pdo, $userId);

        // Tab "Permintaan Saya": permintaan dari pengguna lain terhadap donasi milikku
        $stmt = $pdo->prepare(
            "SELECT r.id, r.donation_id, r.status AS status_req, r.created_at,
                    d.nama_barang, d.foto, u.id AS id_peminta, u.nama AS nama_peminta
               FROM requests r
               JOIN donations d ON d.id = r.donation_id
               JOIN users u     ON u.id = r.requester_id
              WHERE d.user_id = ? ORDER BY r.created_at DESC"
        );
        $stmt->execute([$userId]);
        $permintaan = $stmt->fetchAll();
    } catch (Throwable $ex) {
        $dbError = $ex->getMessage();
    }
} elseif (!$pdo) {
    $dbError = 'Database belum terhubung. Cek config/database.php.';
}

$nDon         = count($donasi);
$nDonPending  = count(array_filter($donasi, fn($d) => $d['status'] === 'pending'));
$nDonVerified = $nDon - $nDonPending;
$nReq         = count($permintaan);
$nReqPending  = count(array_filter($permintaan, fn($r) => $r['status_req'] === 'menunggu'));
$nReqVerified = count(array_filter($permintaan, fn($r) => $r['status_req'] === 'diterima'));

$tabAwal = (($_GET['tab'] ?? '') === 'permintaan') ? 'permintaan' : 'donasi';
$aktifDon = ($tabAwal === 'donasi');

$judulHalaman = 'Status & Riwayat';
$navAktif     = 'aktivitas';
include __DIR__ . '/includes/header.php';

$chipAktif = 'bg-[#2563EB] text-white border-[#2563EB]';
$chipNon   = 'bg-white text-slate-600 border-slate-200';
?>

<?php if ($dbError): ?>
    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">Database bermasalah: <?= e($dbError) ?></div>
<?php endif; ?>

<!-- ===== Tab: pil beranimasi (HP) ===== -->
<div class="lg:hidden relative flex p-1 rounded-full border border-slate-400 bg-white mb-5" role="tablist">
    <span id="pillIndikator" class="absolute top-1 bottom-1 left-1 w-[calc(50%-4px)] bg-[#2563EB] rounded-full transition-transform duration-300 ease-out"
          style="transform: translateX(<?= $aktifDon ? '0' : '100%' ?>)"></span>
    <button type="button" data-tab="donasi" class="tab-m relative z-10 flex-1 py-2.5 rounded-full text-xs font-medium transition-colors duration-300 <?= $aktifDon ? 'text-white' : 'text-slate-900' ?>">Donasi Saya</button>
    <button type="button" data-tab="permintaan" class="tab-m relative z-10 flex-1 py-2.5 rounded-full text-xs font-medium transition-colors duration-300 <?= $aktifDon ? 'text-slate-900' : 'text-white' ?>">Permintaan Saya</button>
</div>

<!-- ===== Tab: garis bawah (desktop) ===== -->
<div class="hidden lg:flex items-end gap-8 border-b border-slate-200 mb-7">
    <?php foreach ([['donasi', 'Donasi Saya', $nDon], ['permintaan', 'Permintaan Saya', $nReq]] as [$kode, $label, $jumlah]):
        $a = ($kode === $tabAwal); ?>
        <button type="button" data-tab="<?= $kode ?>" class="tab-d -mb-px pb-3 flex items-center gap-2 text-[15px] border-b-[3px] transition-colors <?= $a ? 'border-[#2563EB] text-[#2563EB] font-bold' : 'border-transparent text-slate-500 font-medium hover:text-slate-800' ?>">
            <?= e($label) ?>
            <span class="tab-badge min-w-[22px] h-[22px] px-1.5 rounded-full text-[11.5px] font-bold flex items-center justify-center <?= $a ? 'bg-blue-100 text-[#2563EB]' : 'bg-slate-100 text-slate-500' ?>"><?= (int)$jumlah ?></span>
        </button>
    <?php endforeach; ?>
</div>

<!-- ===== Ringkasan (desktop) ===== -->
<?php
$statDon = [['Total Donasi', $nDon, 'package', 'bg-blue-100 text-[#2563EB]'], ['Menunggu Verifikasi', $nDonPending, 'clock', 'bg-[#FEF3C7] text-[#D97706]'], ['Terverifikasi', $nDonVerified, 'check', 'bg-[#D1FAE5] text-[#059669]']];
$statReq = [['Total Permintaan', $nReq, 'package', 'bg-blue-100 text-[#2563EB]'], ['Menunggu Verifikasi', $nReqPending, 'clock', 'bg-[#FEF3C7] text-[#D97706]'], ['Terverifikasi', $nReqVerified, 'check', 'bg-[#D1FAE5] text-[#059669]']];
foreach ([['donasi', $statDon], ['permintaan', $statReq]] as [$kode, $daftarStat]): ?>
    <div data-panel="<?= $kode ?>" class="<?= $kode === $tabAwal ? '' : 'hidden' ?>">
        <div class="hidden lg:grid grid-cols-3 gap-6 mb-7">
            <?php foreach ($daftarStat as [$lbl, $num, $ikon, $warna]): ?>
                <div class="bg-white border border-slate-200 rounded-3xl p-6 flex items-center gap-5 shadow-[0_3px_0_rgba(15,23,42,0.03)]">
                    <span class="w-12 h-12 rounded-2xl flex items-center justify-center <?= $warna ?>"><?= icon($ikon, 'w-6 h-6') ?></span>
                    <span>
                        <span class="block text-3xl font-extrabold leading-none"><?= (int)$num ?></span>
                        <span class="block text-[13px] text-slate-500 mt-2 font-medium"><?= e($lbl) ?></span>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<h2 class="lg:hidden font-bold text-lg mb-3">Status Card</h2>

<div class="lg:grid lg:grid-cols-[minmax(0,1fr)_360px] lg:gap-8 lg:items-start">
    <div class="min-w-0">

        <!-- ===== Panel: Donasi Saya ===== -->
        <section data-panel="donasi" class="<?= $aktifDon ? '' : 'hidden' ?>">
            <div data-chips data-aktif="semua" class="flex items-center gap-2 mb-4 lg:mb-6 overflow-x-auto">
                <?php foreach ([['semua', 'Semua', $nDon], ['pending', 'Pending', $nDonPending], ['verified', 'Verified', $nDonVerified]] as $i => [$f, $lbl, $jml]): ?>
                    <button type="button" data-filter="<?= $f ?>" class="chip shrink-0 px-4 h-9 lg:h-10 rounded-full border text-[13px] font-semibold transition <?= $i === 0 ? $chipAktif : $chipNon ?>"><?= e($lbl) ?> <?= (int)$jml ?></button>
                <?php endforeach; ?>
                <span class="ml-auto hidden lg:inline text-[13px] font-medium text-slate-500">Urutkan: Terbaru</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-7">
                <?php foreach ($donasi as $d): $req = (int)$d['jml_request']; ?>
                    <a href="<?= e(link_donasi($d)) ?>" data-item data-status="<?= kelompok_donasi($d['status']) ?>" data-cari="<?= e(mb_strtolower($d['nama_barang'])) ?>"
                       class="flex items-center gap-3.5 p-3 bg-white border border-slate-200 rounded-[20px] hover:border-blue-300 hover:shadow-sm transition lg:block lg:rounded-3xl">
                        <div class="relative shrink-0 lg:w-full">
                            <?= foto_atau_placeholder($d['foto'], $d['nama_barang'], 'w-[62px] h-[62px] rounded-xl lg:w-full lg:h-[270px] lg:rounded-2xl') ?>
                            <div class="hidden lg:block absolute top-3.5 left-3.5"><?= badge_status($d['status']) ?></div>
                        </div>
                        <div class="min-w-0 lg:px-3 lg:pt-4 lg:pb-2">
                            <h3 class="font-bold text-sm lg:text-[19px] truncate"><?= e($d['nama_barang']) ?></h3>
                            <p class="text-[11px] lg:text-[13.5px] text-slate-400 lg:text-slate-500 mt-0.5 lg:mt-1">
                                <?= (int)$d['jumlah'] ?> unit • <?= e($d['status'] === 'pending' ? waktu_dibuat($d['created_at']) : waktu_relatif($d['created_at'])) ?>
                            </p>
                            <div class="mt-1.5 lg:hidden"><?= badge_status($d['status']) ?></div>
                            <div class="hidden lg:flex items-center justify-between mt-5 pt-4 border-t border-slate-100">
                                <span class="flex items-center gap-2 text-[13px] font-medium text-slate-600">
                                    <?= icon('msg', 'w-[18px] h-[18px] text-slate-400') ?>
                                    <?= $req > 0 ? $req . ' permintaan masuk' : 'Belum ada permintaan' ?>
                                </span>
                                <span class="inline-flex items-center gap-1 pl-4 pr-3 py-2 rounded-full bg-blue-50 text-[#2563EB] text-[13px] font-semibold">Lihat detail <?= icon('chev-r', 'w-4 h-4') ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <p data-kosong class="<?= $donasi ? 'hidden' : '' ?> text-xs text-slate-400 text-center py-10">Belum ada donasi pada filter ini.</p>
        </section>

        <!-- ===== Panel: Permintaan Saya ===== -->
        <section data-panel="permintaan" class="<?= $aktifDon ? 'hidden' : '' ?>">
            <div data-chips data-aktif="semua" class="flex items-center gap-2 mb-4 lg:mb-6 overflow-x-auto">
                <?php foreach ([['semua', 'Semua', $nReq], ['pending', 'Pending', $nReqPending], ['verified', 'Verified', $nReqVerified]] as $i => [$f, $lbl, $jml]): ?>
                    <button type="button" data-filter="<?= $f ?>" class="chip shrink-0 px-4 h-9 lg:h-10 rounded-full border text-[13px] font-semibold transition <?= $i === 0 ? $chipAktif : $chipNon ?>"><?= e($lbl) ?> <?= (int)$jml ?></button>
                <?php endforeach; ?>
                <span class="ml-auto hidden lg:inline text-[13px] font-medium text-slate-500">Urutkan: Terbaru</span>
            </div>

            <div class="lg:bg-white lg:border lg:border-slate-200 lg:rounded-3xl lg:overflow-hidden">
                <div class="hidden lg:grid grid-cols-[minmax(0,2fr)_minmax(0,1.6fr)_96px_112px_24px] gap-4 px-8 py-4 text-[11.5px] font-semibold tracking-wider text-slate-400">
                    <span>BARANG</span><span>PEMINTA</span><span>JARAK</span><span>STATUS</span><span></span>
                </div>
                <div class="space-y-3 lg:space-y-0">
                    <?php foreach ($permintaan as $r): ?>
                        <a href="status-verifikasi.php?id=<?= (int)$r['donation_id'] ?>" data-item data-status="<?= kelompok_permintaan($r['status_req']) ?>"
                           data-cari="<?= e(mb_strtolower($r['nama_barang'] . ' ' . $r['nama_peminta'])) ?>"
                           class="flex items-center gap-3.5 p-3 bg-white border border-slate-200 rounded-[20px] hover:border-blue-300 transition
                                  lg:grid lg:grid-cols-[minmax(0,2fr)_minmax(0,1.6fr)_96px_112px_24px] lg:gap-4 lg:items-center lg:px-8 lg:py-3.5 lg:rounded-none lg:border-0 lg:border-t lg:border-slate-100 lg:hover:bg-slate-50">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <?= foto_atau_placeholder($r['foto'], $r['nama_barang'], 'w-[62px] h-[62px] rounded-xl shrink-0 lg:w-12 lg:h-12') ?>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-sm lg:text-[15px] truncate"><?= e($r['nama_barang']) ?></h3>
                                    <p class="lg:hidden text-[11px] text-slate-400 mt-0.5 truncate"><?= e($r['nama_peminta']) ?> • <?= e(format_jarak($r['jarak_km'])) ?> dari anda</p>
                                    <div class="lg:hidden mt-1.5"><?= badge_permintaan($r['status_req']) ?></div>
                                </div>
                            </div>
                            <div class="hidden lg:flex items-center gap-3 min-w-0">
                                <span class="w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shrink-0 <?= warna_avatar((int)$r['id_peminta']) ?>"><?= e(inisial($r['nama_peminta'])) ?></span>
                                <span class="text-sm font-medium text-slate-700 truncate"><?= e($r['nama_peminta']) ?></span>
                            </div>
                            <div class="hidden lg:flex items-center gap-1.5 text-sm font-semibold text-slate-700"><?= icon('map-pin', 'w-4 h-4 text-slate-500') ?><?= e(format_jarak($r['jarak_km'])) ?></div>
                            <div class="hidden lg:block"><?= badge_permintaan($r['status_req']) ?></div>
                            <div class="hidden lg:block text-slate-300"><?= icon('chev-r', 'w-5 h-5') ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <p data-kosong class="<?= $permintaan ? 'hidden' : '' ?> text-xs text-slate-400 text-center py-10">Belum ada permintaan masuk.</p>
        </section>

        <div class="lg:hidden mt-5"><?php include __DIR__ . '/includes/banner.php'; ?></div>
    </div>

    <!-- ===== Kolom kanan (desktop) ===== -->
    <aside class="hidden lg:block space-y-6">
        <?php include __DIR__ . '/includes/banner.php'; ?>
        <div class="bg-white border border-slate-200 rounded-3xl p-6">
            <h3 class="font-bold text-base mb-4">Aktivitas Terbaru</h3>
            <?php if (!$notifList): ?>
                <p class="text-xs text-slate-400 py-4">Belum ada aktivitas.</p>
            <?php endif; ?>
            <?php foreach (array_slice($notifList, 0, 3) as $i => $n): ?>
                <div class="flex gap-3 <?= $i < 2 ? 'pb-5' : '' ?> relative">
                    <?php if ($i < 2): ?><span class="absolute left-[5px] top-4 bottom-0 w-0.5 bg-slate-100"></span><?php endif; ?>
                    <span class="w-3 h-3 mt-1 rounded-full shrink-0 relative z-10 <?= $n['dibaca'] ? 'bg-slate-300' : 'bg-[#2563EB]' ?>"></span>
                    <div class="min-w-0">
                        <p class="text-[13px] font-semibold text-slate-800"><?= e($n['judul']) ?></p>
                        <p class="text-xs text-slate-400 mt-0.5"><?= e(waktu_relatif($n['created_at'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </aside>
</div>

<?php
$skripHalaman = <<<'JS'
<script>
(function () {
    var CHIP_AKTIF = ['bg-[#2563EB]', 'text-white', 'border-[#2563EB]'];
    var CHIP_NON   = ['bg-white', 'text-slate-600', 'border-slate-200'];
    var TAB_AKTIF  = ['border-[#2563EB]', 'text-[#2563EB]', 'font-bold'];
    var TAB_NON    = ['border-transparent', 'text-slate-500', 'font-medium'];
    var BADGE_AKTIF = ['bg-blue-100', 'text-[#2563EB]'];
    var BADGE_NON   = ['bg-slate-100', 'text-slate-500'];

    function setKelas(el, aktif, on, off) {
        on.forEach(function (c) { el.classList.toggle(c, aktif); });
        off.forEach(function (c) { el.classList.toggle(c, !aktif); });
    }

    function terapkanFilter(panel) {
        var chips = panel.querySelector('[data-chips]');
        var f = chips ? chips.getAttribute('data-aktif') : 'semua';
        var input = document.getElementById('cariBarang');
        var q = input ? input.value.trim().toLowerCase() : '';
        var tampil = 0;
        panel.querySelectorAll('[data-item]').forEach(function (el) {
            var ok = (f === 'semua' || el.dataset.status === f) && (q === '' || el.dataset.cari.indexOf(q) !== -1);
            el.style.display = ok ? '' : 'none';
            if (ok) tampil++;
        });
        var kosong = panel.querySelector('[data-kosong]');
        if (kosong) kosong.classList.toggle('hidden', tampil > 0);
    }

    function pilihTab(nama) {
        document.querySelectorAll('[data-panel]').forEach(function (el) { el.classList.toggle('hidden', el.dataset.panel !== nama); });
        var ind = document.getElementById('pillIndikator');
        if (ind) ind.style.transform = nama === 'donasi' ? 'translateX(0)' : 'translateX(100%)';
        document.querySelectorAll('.tab-m').forEach(function (b) {
            var a = b.dataset.tab === nama;
            b.classList.toggle('text-white', a); b.classList.toggle('text-slate-900', !a);
        });
        document.querySelectorAll('.tab-d').forEach(function (b) {
            var a = b.dataset.tab === nama;
            setKelas(b, a, TAB_AKTIF, TAB_NON);
            setKelas(b.querySelector('.tab-badge'), a, BADGE_AKTIF, BADGE_NON);
        });
    }

    document.querySelectorAll('[data-tab]').forEach(function (b) {
        b.addEventListener('click', function () { pilihTab(b.dataset.tab); });
    });

    document.querySelectorAll('[data-chips]').forEach(function (c) {
        c.addEventListener('click', function (e) {
            var b = e.target.closest('[data-filter]');
            if (!b) return;
            c.setAttribute('data-aktif', b.dataset.filter);
            c.querySelectorAll('[data-filter]').forEach(function (x) { setKelas(x, x === b, CHIP_AKTIF, CHIP_NON); });
            terapkanFilter(c.closest('section'));
        });
    });

    var cari = document.getElementById('cariBarang');
    if (cari) cari.addEventListener('input', function () {
        document.querySelectorAll('section[data-panel]').forEach(terapkanFilter);
    });
})();
</script>
JS;
include __DIR__ . '/includes/footer.php';
