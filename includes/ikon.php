<?php
// Ikon SVG form. Pemakaian: echo ikon('email')
function ikon(string $nama, int $u = 16): string {
  static $p = [
    'user'  => '<circle cx="12" cy="8" r="4"/><path d="M5 20c0-3.8 3.1-6.5 7-6.5s7 2.7 7 6.5"/>',
    'email' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
    'lock'  => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>',
    'eye'   => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>',
    'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
  ];
  $extra = $nama === 'arrow' ? ' style="display:inline-block;vertical-align:middle;margin-left:6px"' : '';
  $w = $nama === 'arrow' ? 2 : 1.8;
  return '<svg width="'.$u.'" height="'.$u.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="'.$w.'" stroke-linecap="round" stroke-linejoin="round"'.$extra.' aria-hidden="true">'.($p[$nama] ?? '').'</svg>';
}