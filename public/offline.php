<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tidak Ada Koneksi</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Plus Jakarta Sans', Arial, sans-serif;
    background: #1a1a2e;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    text-align: center;
    padding: 24px;
}
.container { max-width: 360px; }
.icon { font-size: 80px; margin-bottom: 24px; }
h1 { font-size: 24px; font-weight: 800; margin-bottom: 12px; }
p { color: #9ca3af; font-size: 15px; line-height: 1.6; margin-bottom: 32px; }
.btn {
    background: #c9a84c;
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}
.btn:active { opacity: 0.85; }
.cached-links { margin-top: 32px; }
.cached-links p { font-size: 13px; margin-bottom: 12px; }
.cached-links a {
    display: block;
    color: #c9a84c;
    text-decoration: none;
    padding: 10px 0;
    border-bottom: 1px solid #2d2d4e;
    font-size: 14px;
}
.status {
    margin-top: 24px;
    font-size: 12px;
    color: #6b7280;
}
.online-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ef4444;
    margin-right: 6px;
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
</style>
</head>
<body>
<div class="container">
    <div class="icon">📶</div>
    <h1>Tidak Ada Koneksi</h1>
    <p>Periksa koneksi internet Anda dan coba lagi. Beberapa halaman yang sudah dibuka sebelumnya mungkin masih tersedia.</p>

    <button class="btn" onclick="retry()">🔄 Coba Lagi</button>

    <div class="cached-links">
        <p>Halaman yang mungkin tersedia offline:</p>
        <a href="/dashboard">📊 Dashboard</a>
        <a href="/orders">🛒 Pesanan</a>
        <a href="/products">📦 Produk</a>
    </div>

    <div class="status">
        <span class="online-indicator" id="indicator"></span>
        <span id="statusText">Tidak terhubung</span>
    </div>
</div>

<script>
function retry() {
    window.location.reload();
}

// Monitor koneksi
function updateStatus() {
    const indicator = document.getElementById('indicator');
    const statusText = document.getElementById('statusText');
    if (navigator.onLine) {
        indicator.style.background = '#22c55e';
        statusText.textContent = 'Koneksi tersedia — mengalihkan...';
        setTimeout(() => window.history.back(), 1500);
    } else {
        indicator.style.background = '#ef4444';
        statusText.textContent = 'Tidak terhubung';
    }
}

window.addEventListener('online', updateStatus);
window.addEventListener('offline', updateStatus);
updateStatus();
</script>
</body>
</html>
