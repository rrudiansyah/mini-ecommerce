<form method="POST" action="<?= BASE_URL ?>/login">
    <?php echo $csrf_field ?? ''; ?>
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required placeholder="Masukkan username" autocomplete="username" autofocus>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required placeholder="••••••••" autocomplete="current-password">
    </div>
    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
</form>
