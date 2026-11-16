<?php
/** Escape output agar aman dari XSS */
function e($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function waktu_relatif(string $datetime): string {
    $selisih = time() - strtotime($datetime);
    if ($selisih < 3600)  return max(1, (int)($selisih / 60)) . 'm yang lalu';
    if ($selisih < 86400) return (int)($selisih / 3600) . 'h yang lalu';
    return (int)($selisih / 86400) . ' hari yang lalu';
}

function waktu_dibuat(string $datetime): string {
    return date('Y-m-d') === date('Y-m-d', strtotime($datetime)) ? 'Dibuat hari ini' : waktu_relatif($datetime);
}

/** 0.5 -> "500 m", 2.0 -> "2 km", 1.5 -> "1,5 km" */
function format_jarak($km): string {
    if ($km === null || $km === '') return '-';
    $km = (float)$km;
    if ($km < 1) return (int)round($km * 1000) . ' m';
    return rtrim(rtrim(number_format($km, 1, ',', '.'), '0'), ',') . ' km';
}

/** Ikon SVG bergaya garis */
function icon(string $name, string $class = 'w-4 h-4'): string {
    $paths = [
        'arrow-left'   => '<path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>',
        'compass'      => '<circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>',
        'plus-circle'  => '<circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/>',
        'inbox'        => '<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
        'user'         => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'clock'        => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        'check'        => '<polyline points="20 6 9 17 4 12"/>',
        'x'            => '<path d="M18 6 6 18"/><path d="M6 6l12 12"/>',
        'map-pin'      => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'truck'        => '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'heart'        => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
        'image'        => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',
        'tag'          => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
        'calendar'     => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'shield-check' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
        'package'      => '<path d="M16.5 9.4 7.5 4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        'bell'         => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        'search'       => '<circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>',
        'chev-r'       => '<polyline points="9 18 15 12 9 6"/>',
        'msg'          => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
    ];
    $inner = $paths[$name] ?? '';
    return '<svg xmlns="http://www.w3.org/2000/svg" class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
}

/** Foto dari database; kalau kosong/gagal dimuat tampil kotak abu-abu */
function foto_atau_placeholder(?string $src, string $alt, string $class): string {
    if ($src) {
        return '<div class="' . e($class) . ' bg-slate-100 overflow-hidden"><img src="' . e($src) . '" alt="' . e($alt) . '" class="w-full h-full object-cover" onerror="this.remove()"></div>';
    }
    return '<div class="' . e($class) . ' bg-slate-100 flex items-center justify-center text-slate-300 overflow-hidden">' . icon('image', 'w-6 h-6') . '</div>';
}

/** Badge status donasi: pending | verified | atur_jadwal */
function badge_status(string $status): string {
    switch ($status) {
        case 'verified':
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#D1FAE5] text-[#059669]">' . icon('check-circle', 'w-3 h-3') . 'Verified</span>';
        case 'atur_jadwal':
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#D1FAE5] text-[#059669]">' . icon('check-circle', 'w-3 h-3') . 'Verified - Atur Jadwal ' . icon('truck', 'w-3.5 h-3.5 text-[#2563EB]') . '</span>';
        default:
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FEF3C7] text-[#D97706]">' . icon('clock', 'w-3 h-3') . 'Pending</span>';
    }
}

/** Badge status permintaan: menunggu | diterima | ditolak */
function badge_permintaan(string $status): string {
    switch ($status) {
        case 'diterima':
            return badge_status('verified');
        case 'ditolak':
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FECACA] text-[#B91C1C]">' . icon('x', 'w-3 h-3') . 'Ditolak</span>';
        default:
            return badge_status('pending');
    }
}

/** Kelompok untuk filter chip: pending | verified | ditolak */
function kelompok_donasi(string $status): string {
    return $status === 'pending' ? 'pending' : 'verified';
}
function kelompok_permintaan(string $status): string {
    return ['menunggu' => 'pending', 'diterima' => 'verified', 'ditolak' => 'ditolak'][$status] ?? 'pending';
}

/** Semua donasi milik pengguna (untuk daftar samping & halaman status) */
function ambil_donasi_user(?PDO $pdo, ?int $userId): array {
    if (!$pdo || !$userId) return [];
    $stmt = $pdo->prepare(
        "SELECT d.id, d.nama_barang, d.jumlah, d.foto, d.status, d.created_at,
                (SELECT COUNT(*) FROM requests r WHERE r.donation_id = d.id AND r.status = 'menunggu') AS jml_request
           FROM donations d WHERE d.user_id = ? ORDER BY d.created_at DESC"
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/** Tautan saat kartu donasi diklik */
function link_donasi(array $d): string {
    $id = (int)$d['id'];
    if ($d['status'] === 'atur_jadwal') return 'atur-jadwal.php?id=' . $id;
    if ((int)($d['jml_request'] ?? 0) > 0) return 'status-verifikasi.php?id=' . $id;
    return 'detail-barang.php?id=' . $id;
}

/** Inisial nama, mis. "Made Wijaya" -> "MW" */
function inisial(string $nama): string {
    $kata = preg_split('/\s+/', trim($nama)) ?: [];
    $hasil = '';
    foreach (array_slice($kata, 0, 2) as $k) {
        $hasil .= mb_strtoupper(mb_substr($k, 0, 1));
    }
    return $hasil !== '' ? $hasil : '?';
}

/** Warna avatar inisial berdasarkan id */
function warna_avatar(int $id): string {
    $w = ['bg-blue-100 text-[#2563EB]', 'bg-[#FEF3C7] text-[#D97706]', 'bg-[#D1FAE5] text-[#059669]', 'bg-[#FECACA] text-[#B91C1C]', 'bg-slate-200 text-slate-600'];
    return $w[$id % count($w)];
}
