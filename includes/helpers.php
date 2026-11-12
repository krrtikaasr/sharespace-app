<?php
/** Escape output agar aman dari XSS */
function e($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/** "Dibuat hari ini" / "4h yang lalu" dari timestamp database */
function waktu_relatif(string $datetime): string {
    $selisih = time() - strtotime($datetime);
    if ($selisih < 3600)   return max(1, (int)($selisih / 60)) . 'm yang lalu';
    if ($selisih < 86400)  return (int)($selisih / 3600) . 'h yang lalu';
    return (int)($selisih / 86400) . ' hari yang lalu';
}

function waktu_dibuat(string $datetime): string {
    return date('Y-m-d') === date('Y-m-d', strtotime($datetime))
        ? 'Dibuat hari ini'
        : waktu_relatif($datetime);
}

/** Kumpulan ikon SVG (gaya garis, sesuai prototype) */
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
        'signal'       => '<path d="M2 20h.01"/><path d="M7 20v-4"/><path d="M12 20v-8"/><path d="M17 20V8"/><path d="M22 4v16"/>',
        'wifi'         => '<path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><path d="M12 20h.01"/>',
        'battery'      => '<rect x="2" y="7" width="16" height="10" rx="2"/><path d="M22 11v2"/>',
    ];
    $inner = $paths[$name] ?? '';
    return '<svg xmlns="http://www.w3.org/2000/svg" class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
}

/** Gambar dari database; jika kosong tampil placeholder ikon */
function foto_atau_placeholder(?string $src, string $alt, string $class): string {
    if ($src) {
        return '<div class="' . e($class) . ' bg-slate-100 overflow-hidden"><img src="' . e($src) . '" alt="' . e($alt) . '" class="w-full h-full object-cover" onerror="this.remove()"></div>';
    }
    return '<div class="' . e($class) . ' bg-slate-100 flex items-center justify-center text-slate-300">' . icon('image', 'w-6 h-6') . '</div>';
}

/** Badge status donasi */
function badge_status(string $status): string {
    switch ($status) {
        case 'verified':
            return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-[#D1FAE5] text-[#059669]">' . icon('check-circle', 'w-3 h-3') . 'Verified</span>';
        case 'atur_jadwal':
            return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-[#D1FAE5] text-[#059669]">' . icon('check-circle', 'w-3 h-3') . 'Verified - Atur Jadwal ' . icon('truck', 'w-3.5 h-3.5 text-[#2563EB]') . '</span>';
        default:
            return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-[#FEF3C7] text-[#D97706]">' . icon('clock', 'w-3 h-3') . 'Pending</span>';
    }
}
