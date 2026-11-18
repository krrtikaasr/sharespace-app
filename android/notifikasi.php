
<?php
$isDrawer = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Data notifikasi (ganti dengan data dari database)
$notifications = [
    [
        'type'   => 'gift',
        'title'  => 'Permintaan donasi baru',
        'desc'   => 'Ayu tertarik pada donasi Lampu Meja Kayu kamu.',
        'time'   => '5 menit lalu',
        'unread' => true,
    ],
    [
        'type'   => 'gift',
        'title'  => 'Donasi berhasil dibagikan',
        'desc'   => 'Lampu Meja Kayu sudah tampil dan bisa ditemukan anggota.',
        'time'   => 'Kemarin, 15.30',
        'unread' => false,
    ],
    [
        'type'   => 'check',
        'title'  => 'Permintaan disetujui',
        'desc'   => 'Kipas Angin Portable disetujui. Atur jadwal pengambilan.',
        'time'   => 'Kemarin, 11.00',
        'unread' => true,
    ],
    [
        'type'   => 'send',
        'title'  => 'Permintaan terkirim',
        'desc'   => 'Permintaan Kipas Angin Portable telah dikirim ke Ni Luh Putri.',
        'time'   => 'Kemarin, 10.15',
        'unread' => false,
    ],
    [
        'type'   => 'heart',
        'title'  => 'Selamat datang di ShareSpace',
        'desc'   => 'Mulai berbagi barang dan sebarkan kebaikan di sekitarmu.',
        'time'   => '2 hari lalu',
        'unread' => false,
    ],
];

// Path ikon per tipe notifikasi
$icons = [
    'gift'  => 'M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',

    'check' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',

    'send'  => 'M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5',

    'heart' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
];

// Menghitung jumlah notifikasi yang belum dibaca
$unreadCount = count(array_filter($notifications, fn($n) => $n['unread']));

$lastIndex = count($notifications) - 1;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Notifikasi</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        ink: '#18223F',
                        brand: '#2F5FE3',
                        tint: '#E8F0FE',
                        tintIcon: '#D3E3FD',
                    },
                },
            },
        };
    </script>
</head>

<body class="<?= $isDrawer ? 'bg-white' : 'bg-[#1f2937]' ?> font-sans antialiased">

    <!--
        Mode mobile:
        Menggunakan frame terpusat dengan lebar 402px.

        Mode drawer:
        Mengisi seluruh panel desktop.
    -->

    <main class="relative <?= $isDrawer ? 'w-full min-h-screen' : 'mx-auto w-[402px] min-h-[874px]' ?> bg-white overflow-y-auto">

        <!-- Header -->
        <header class="px-4 pt-4">

            <div class="flex items-center justify-between">

                <div class="flex items-center gap-3">

                    <h1 class="text-[34px] leading-none font-bold text-ink tracking-tight">
                        Notifikasi
                    </h1>

                    <?php if ($unreadCount > 0): ?>
                        <span class="mt-1 inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full bg-tint text-[12px] font-semibold text-brand">
                            <?= $unreadCount ?>
                        </span>
                    <?php endif; ?>

                </div>

                <!-- Tombol tutup untuk mode drawer -->
                <?php if ($isDrawer): ?>

                    <button
                        type="button"
                        onclick="window.parent.postMessage('closeNotifications', '*')"
                        aria-label="Tutup"
                        class="p-1 text-ink hover:text-slate-500 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                <?php else: ?>

                    <!-- Tombol kembali untuk mode mobile -->
                    <a
                        href="beranda.php"
                        aria-label="Kembali ke beranda"
                        class="p-1 text-ink hover:text-slate-500 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>

                <?php endif; ?>

            </div>

            <!-- Tombol tandai semua dibaca -->
            <button
                type="button"
                class="mt-4 inline-flex items-center gap-2 h-[34px] px-4 rounded-full border border-slate-400 text-[14px] font-semibold text-brand hover:bg-tint transition-colors"
            >
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 13l4 4L14 8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 15l2 2 9-10"/>
                </svg>

                Tandai semua dibaca
            </button>

        </header>

        <!-- Daftar notifikasi -->
        <section class="mt-8 px-4" aria-label="Daftar notifikasi">

            <?php foreach ($notifications as $i => $n): ?>

                <?php
                    $isUnread = $n['unread'];

                    $showDivider = !$isUnread && $i !== $lastIndex;
                ?>

                <article class="relative flex items-start gap-4 px-5 py-[18px] <?= $isUnread ? 'bg-tint rounded-[28px]' : '' ?>">

                    <!-- Ikon notifikasi -->
                    <div class="shrink-0 w-11 h-11 mt-3 rounded-full flex items-center justify-center <?= $isUnread ? 'bg-tintIcon text-brand' : 'bg-slate-100 text-slate-500' ?>">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $icons[$n['type']] ?>"/>
                        </svg>

                    </div>

                    <!-- Konten notifikasi -->
                    <div class="min-w-0 flex-1">

                        <div class="flex items-baseline justify-between gap-2">

                            <h2 class="text-[15px] font-semibold text-ink truncate">
                                <?= htmlspecialchars($n['title']) ?>
                            </h2>

                            <time class="shrink-0 text-[11px] <?= $isUnread ? 'text-brand' : 'text-slate-500' ?>">
                                <?= htmlspecialchars($n['time']) ?>
                            </time>

                        </div>

                        <p class="mt-1 text-[13px] leading-[1.45] text-slate-500">
                            <?= htmlspecialchars($n['desc']) ?>
                        </p>

                    </div>

                    <?php if ($showDivider): ?>

                        <!-- Garis pemisah -->
                        <span class="absolute bottom-0 left-[84px] right-5 h-px bg-slate-200"></span>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

        </section>

    </main>

</body>
</html>
