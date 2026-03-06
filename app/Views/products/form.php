<form method="POST" action="<?= BASE_URL ?>/products/<?= $product ? 'update/' . $product['id'] : 'store' ?>" enctype="multipart/form-data">
    <?php echo $csrf_field ?? ''; ?>
    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($product['name'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Harga (Rp)</label>
            <input type="number" name="price" required value="<?= $product['price'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label>Kategori</label>
            <select name="category_id">
                <option value="">— Pilih Kategori —</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label>Foto Produk</label>
        <input type="file" name="image" accept="image/*">
        <?php if (!empty($product['image'])): ?>
        <img src="<?= BASE_URL ?>/<?= $product['image'] ?>" width="80" style="margin-top:8px;border-radius:6px">
        <?php endif; ?>
    </div>
    <?php if ($product): ?>
    <div class="form-group">
        <label>Status</label>
        <select name="is_available">
            <option value="1" <?= $product['is_available'] ? 'selected' : '' ?>>Tersedia</option>
            <option value="0" <?= !$product['is_available'] ? 'selected' : '' ?>>Habis</option>
        </select>
    </div>
    <?php endif; ?>
    <div class="form-actions">
        <a href="<?= BASE_URL ?>/products" class="btn">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>