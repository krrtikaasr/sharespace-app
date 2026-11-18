<style>
:root { --biru:#2563eb; --teks:#0f172a; --muted:#475569; --bg:#f8fafc; --garis:#e2e8f0; }
* { box-sizing:border-box; margin:0; }
body { font-family:'Inter',sans-serif; color:var(--teks); background:var(--bg); line-height:1.6; }
a { color:var(--biru); text-decoration:none; font-weight:600; }
.auth { display:grid; grid-template-columns:1fr 1fr; min-height:100vh; }

/* ===== PANEL KIRI (latar biru) ===== */
.auth__kiri { background:var(--biru); color:#fff; padding:40px 56px; display:flex; flex-direction:column; align-items:flex-start; }
.auth__kiri .brand { margin-bottom:40px; }                 /* jarak logo -> judul */
.auth__kiri h1 { font-size:clamp(34px,4vw,46px); line-height:1.15; font-weight:700; letter-spacing:-0.02em; color:#e8eefc; }
.auth__kiri p { margin:10px 0 28px; font-weight:600; opacity:.75; } /* jarak judul -> gambar */
.auth__kiri img { width:100%; max-width:520px; aspect-ratio:4/3; object-fit:cover; border-radius:24px; background:rgba(255,255,255,.15); }

/* ===== PANEL KANAN (form) ===== */
.auth__kanan { display:flex; flex-direction:column; justify-content:center; align-items:center; padding:40px 24px; }
.form { width:min(380px,100%); }
.form h2 { font-size:26px; font-weight:700; letter-spacing:-0.01em; }
.form .sub { margin:6px 0 24px; font-size:13px; color:var(--muted); }

label.lbl { display:block; margin:14px 0 6px; font-size:12px; font-weight:600; }
.input { position:relative; }
.input input { width:100%; padding:13px 40px; border:1px solid var(--garis); border-radius:10px; background:#fff; font:inherit; font-size:13px; transition:border-color .2s, box-shadow .2s; }
.input input:focus { outline:none; border-color:var(--biru); box-shadow:0 0 0 3px rgba(37,99,235,.15); }
.input .ikon { position:absolute; left:13px; top:50%; transform:translateY(-50%); display:flex; color:var(--muted); }
.input button { position:absolute; right:10px; top:50%; transform:translateY(-50%); display:flex; background:none; border:0; cursor:pointer; color:var(--muted); }
.input button:hover { color:var(--biru); }

.baris { display:flex; justify-content:space-between; align-items:center; margin:22px 0 16px; font-size:12px; }
.cek { display:flex; gap:8px; align-items:flex-start; font-size:12px; margin:18px 0 16px; line-height:1.5; }
.cek input { margin-top:2px; accent-color:var(--biru); }
.tombol { width:100%; padding:14px; border:0; border-radius:10px; background:var(--biru); color:#fff; font:inherit; font-weight:600; cursor:pointer; transition:background .2s; }
.tombol:hover { background:#1d4ed8; }
.tombol:focus-visible { outline:2px solid var(--biru); outline-offset:2px; }
.pesan { padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:8px; }
.pesan--error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
.pesan--ok { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.bawah { margin-top:28px; text-align:center; font-size:12px; color:var(--muted); }

/* ===== MOBILE ===== */
@media (max-width:767px) {
  .auth { grid-template-columns:1fr; }
  .auth__kiri { display:none; }
}
</style>
<script>
function lihat(btn){ var i = btn.parentNode.querySelector('input'); i.type = i.type === 'password' ? 'text' : 'password'; }
</script>
</head>