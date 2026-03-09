<div class="page-header">
    <button class="btn btn-primary" onclick="showForm()">+ Tambah Kategori</button>
</div>

<div id="categoryForm" class="modal" style="display:none">
    <div class="modal-content">
        <button class="close" onclick="closeForm()" type="button">&times;</button>
        <h2 id="formTitle">Tambah Kategori</h2>
        <form id="categoryFormElement" method="POST" action="">
            <?php echo $csrf_field ?? ''; ?>
            <div class="form-group">
                <label for="name">Nama Kategori</label>
                <input type="text" id="name" name="name" required placeholder="contoh: Kopi, Laundry Kiloan...">
            </div>
            <div class="form-actions" style="margin-top:16px;padding-top:16px">
                <button type="button" class="btn" onclick="closeForm()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="section" style="padding:0;overflow:hidden">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Nama Kategori</th><th>Jumlah Produk</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="3" style="text-align:center;padding:24px;color:var(--muted)">
                        Belum ada kategori. <a href="javascript:showForm()" style="color:var(--gold)">Tambah kategori</a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                        <td><span class="badge"><?= $cat['product_count'] ?> Produk</span></td>
                        <td style="white-space:nowrap">
                            <button class="btn btn-sm" onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>')">Edit</button>
                            <form method="POST" action="<?= BASE_URL ?>/categories/delete/<?= $cat['id'] ?>" style="display:inline" onsubmit="return confirm('Hapus kategori ini?')">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showForm() {
    document.getElementById('categoryForm').style.display = 'flex';
    document.getElementById('formTitle').textContent = 'Tambah Kategori';
    document.getElementById('categoryFormElement').action = '<?= BASE_URL ?>/categories/store';
    document.getElementById('categoryFormElement').reset();
}
function editCategory(id, name) {
    document.getElementById('categoryForm').style.display = 'flex';
    document.getElementById('formTitle').textContent = 'Edit Kategori';
    document.getElementById('categoryFormElement').action = '<?= BASE_URL ?>/categories/update/' + id;
    document.getElementById('name').value = name;
}
function closeForm() { document.getElementById('categoryForm').style.display = 'none'; }
window.addEventListener('click', e => { if (e.target.id === 'categoryForm') closeForm(); });
</script>
