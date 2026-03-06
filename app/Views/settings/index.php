<div class="section">
    <h1>⚙️ Pengaturan</h1>
    <p>Kelola konfigurasi dan kontrol akses aplikasi Anda.</p>
</div>

<div class="settings-grid">
    <div class="settings-card">
        <div class="card-icon">👥</div>
        <h3>Kelola Role</h3>
        <p>Buat dan kelola role pengguna, serta atur permission untuk setiap role.</p>
        <a href="<?= BASE_URL ?>/settings/roles" class="btn btn-primary">Buka Pengaturan Role</a>
    </div>

    <div class="settings-card">
        <div class="card-icon">🔐</div>
        <h3>Permissions</h3>
        <p>Lihat semua permission yang tersedia dalam sistem.</p>
        <a href="javascript:void(0)" class="btn btn-primary" onclick="alert('Fitur ini akan segera tersedia')">Lihat Permissions</a>
    </div>

    <div class="settings-card">
        <div class="card-icon">🏪</div>
        <h3>Pengaturan Toko</h3>
        <p>Kelola informasi dan konfigurasi toko Anda.</p>
        <a href="javascript:void(0)" class="btn btn-primary" onclick="alert('Fitur ini akan segera tersedia')">Buka Toko</a>
    </div>
</div>

<style>
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.settings-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 25px;
    text-align: center;
    transition: all 0.3s ease;
}

.settings-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.card-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.settings-card h3 {
    margin: 15px 0;
    color: #1e293b;
}

.settings-card p {
    color: #64748b;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 20px;
}
</style>
