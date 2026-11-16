<style>
:root { --biru:#2563eb; --teks:#0f172a; --bg:#f8fafc; --garis:#e2e8f0; }
* { box-sizing:border-box; margin:0; }
body { font-family:'Inter',sans-serif; color:var(--teks); background:var(--bg); }
a { color:var(--biru); text-decoration:none; font-weight:600; }
.auth { display:grid; grid-template-columns:1fr 1fr; min-height:100vh; }

.auth__kiri { background:var(--biru); color:#fff; padding:40px 48px; display:flex; flex-direction:column; }
.auth__kiri .brand { color:#fff; margin-bottom:56px; }
.auth__kiri h1 { font-size:clamp(34px,4vw,46px); line-height:1.15; font-weight:700; color:#e8eefc; }
.auth__kiri p { margin:8px 0 28px; font-weight:600; opacity:.7; }
.auth__kiri img { width:100%; max-width:480px; aspect-ratio:1; object-fit:cover; border-radius:24px; background:rgba(255,255,255,.15); }

.auth__kanan { display:flex; flex-direction:column; justify-content:center; align-items:center; padding:40px 24px; position:relative; }
.form { width:min(380px,100%); }
.form h2 { font-size:26px; font-weight:600; }
.form .sub { margin:6px 0 24px; font-size:13px; color:#475569; }
.brand { display:flex; align-items:center; gap:8px; font-weight:700; font-size:18px; }
.brand__logo { width:26px; height:26px; background:var(--biru); border-radius:7px; }
.brand--mobile { display:none; color:var(--teks); margin-bottom:24px; }

label.lbl { display:block; margin:14px 0 6px; font-size:12px; font-weight:600; }
.input { position:relative; }
.input input { width:100%; padding:13px 40px 13px 40px; border:1px solid var(--garis); border-radius:10px; background:#fff; font:inherit; font-size:13px; }
.input input:focus { outline:2px solid var(--biru); outline-offset:0; }
.input .ikon { position:absolute; left:13px; top:50%; transform:translateY(-50%); font-size:14px; }
.input button { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:0; cursor:pointer; font-size:15px; }

.baris { display:flex; justify-content:space-between; align-items:center; margin:22px 0 16px; font-size:12px; }
.cek { display:flex; gap:8px; align-items:flex-start; font-size:12px; margin:18px 0 16px; line-height:1.5; }
.cek input { margin-top:2px; accent-color:var(--biru); }
.tombol { width:100%; padding:14px; border:0; border-radius:10px; background:var(--biru); color:#fff; font:inherit; font-weight:600; cursor:pointer; }
.tombol:hover { background:#1d4ed8; }
.pesan { padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:8px; }
.pesan--error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
.pesan--ok { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.bawah { margin-top:28px; text-align:center; font-size:12px; color:#475569; }

@media (max-width:767px) {
  .auth { grid-template-columns:1fr; }
  .auth__kiri { display:none; }
  .brand--mobile { display:flex; }
}
</style>
<script>
function lihat(btn){ var i = btn.parentNode.querySelector('input'); i.type = i.type === 'password' ? 'text' : 'password'; }
</script>
</head>