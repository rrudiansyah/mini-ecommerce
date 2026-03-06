<div class="section" style="max-width:680px">
    <form method="POST" action="<?= BASE_URL ?>/superadmin/stores/store">
    <?php echo $csrf_field ?? ''; ?>

        <h2 style="margin-bottom:24px">Informasi Toko</h2>

        <div class="form-row">
            <div class="form-group">
                <label>Nama Toko <span style="color:red">*</span></label>
                <input type="text" name="store_name" required placeholder="Contoh: Kopi Nusantara" autofocus>
            </div>
            <div class="form-group">
                <label>Niche / Jenis Usaha <span style="color:red">*</span></label>
                <select name="niche" required>
                    <option value="">-- Pilih Niche --</option>
                    <option value="coffee">☕ Coffee Shop</option>
                    <option value="restaurant">🍔 Restoran</option>
                    <option value="barbershop">✂️ Barbershop / Salon</option>
                    <option value="fashion">👕 Fashion / Thrift</option>
                    <option value="bakery">🍰 Bakery / Toko Kue</option>
                    <option value="laundry">🧺 Laundry</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Nomor WhatsApp</label>
                <input type="text" name="phone" placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group">
                <label>Warna Tema</label>
                <div style="display:flex; gap:8px; align-items:center">
                    <input type="color" name="theme_color" value="#3b82f6" style="width:48px; height:40px; padding:2px; border-radius:8px; cursor:pointer">
                    <span style="font-size:13px; color:#64748b">Warna aksen untuk halaman publik</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea name="address" rows="2" placeholder="Jl. Merdeka No. 1, Kota..."></textarea>
        </div>

        <hr style="border:none; border-top:1px solid #e2e8f0; margin:24px 0">

        <h2 style="margin-bottom:24px">Akun Admin Toko</h2>
        <p style="font-size:13px; color:#64748b; margin-bottom:16px">
            Admin ini yang akan login dan mengelola toko di atas.
        </p>

        <div class="form-row">
            <div class="form-group">
                <label>Nama Admin <span style="color:red">*</span></label>
                <input type="text" name="admin_name" required placeholder="Nama lengkap">
            </div>
            <div class="form-group">
                <label>Username <span style="color:red">*</span></label>
                <input type="text" name="username" required placeholder="Harus unik, tanpa spasi">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email (opsional)</label>
                <input type="email" name="email" placeholder="admin@toko.com">
            </div>
            <div class="form-group">
                <label>Password <span style="color:red">*</span></label>
                <input type="password" name="password" required placeholder="Min. 6 karakter" minlength="6">
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= BASE_URL ?>/superadmin/stores" class="btn">Batal</a>
            <button type="submit" class="btn btn-primary">🏪 Buat Toko</button>
        </div>
    </form>
</div>
