<form method="POST" action="<?= BASE_URL ?>/users/<?= $user ? 'update/' . $user['id'] : 'store' ?>">
    <?php echo $csrf_field ?? ''; ?>
    <?php $currentRoleId = $currentRoleId ?? null; ?>
    <fieldset style="border:none; padding:0">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Nama Lengkap *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Password <?= $user ? '(kosongkan jika tidak ingin mengubah)' : '(wajib)' ?> *</label>
                <input type="password" id="password" name="password" <?= $user ? '' : 'required' ?>>
            </div>
            <div class="form-group">
                <label for="role_id">Role *</label>
                <select id="role_id" name="role_id" required>
                    <option value="">— Pilih Role —</option>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= ($currentRoleId === (int)$role['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php if ($user): ?>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1" <?= $user['is_active'] ? 'checked' : '' ?>>
                <strong>Pengguna Aktif</strong>
            </label>
        </div>
        <?php endif; ?>

        <div class="form-actions" style="margin-top:20px; padding-top:20px; border-top:1px solid #ddd">
            <a href="<?= BASE_URL ?>/users" class="btn">Batal</a>
            <button type="submit" class="btn btn-primary"><?= $user ? 'Perbarui' : 'Buat' ?> Pengguna</button>
        </div>
    </fieldset>
</form>

<style>
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
.form-group { display: flex; flex-direction: column; }
.form-group label { font-weight: bold; margin-bottom: 5px; }
.form-group input, .form-group select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
.btn-primary { background: #2563eb; color: white; }
.btn-primary:hover { background: #1d4ed8; }
</style>
