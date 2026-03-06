<div class="section" style="max-width: 600px;">
    <h2><?= $isEdit ? '✏️ Edit Role' : '✨ Buat Role Baru' ?></h2>

    <form method="POST" action="<?= BASE_URL ?>/settings/roles/<?= $isEdit ? 'update/' . $role['id'] : 'store' ?>">
    <?php echo $csrf_field ?? ''; ?>
        <div class="form-group">
            <label for="name">Nama Role *</label>
            <input type="text" id="name" name="name" required
                   value="<?= $isEdit ? htmlspecialchars($role['name']) : '' ?>"
                   placeholder="Contoh: Supervisor, Kasir, dll">
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Jelaskan tanggung jawab dan akses role ini (opsional)"><?= $isEdit ? htmlspecialchars($role['description'] ?? '') : '' ?></textarea>
        </div>

        <div class="form-actions">
            <a href="<?= BASE_URL ?>/settings/roles" class="btn" style="background: #64748b; color: white;">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Simpan Perubahan' : 'Buat Role' ?>
            </button>
        </div>
    </form>
</div>

<style>
textarea {
    font-family: inherit;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 14px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn:hover {
    opacity: 0.9;
}
</style>
