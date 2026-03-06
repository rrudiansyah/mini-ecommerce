<form method="POST" action="<?= BASE_URL ?>/superadmin/login">
    <?php echo $csrf_field ?? ''; ?>
    <h2>🔐 Super Admin</h2>
    <div class="form-group" style="margin-top:8px">
        <label>Username</label>
        <input type="text" name="username" required placeholder="superadmin" autofocus>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required placeholder="••••••••">
    </div>
    <button type="submit" class="btn btn-primary btn-block">Masuk sebagai Super Admin</button>
    <div style="text-align:center; margin-top:16px">
        <a href="<?= BASE_URL ?>/login" style="font-size:13px; color:#64748b">← Login sebagai Admin Toko</a>
    </div>
</form>
