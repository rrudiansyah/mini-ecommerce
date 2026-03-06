<div class="page-header">
    <button class="btn btn-primary" onclick="showForm()">+ Tambah Kategori</button>
</div>

<!-- Form Add/Edit Category -->
<div id="categoryForm" class="modal" style="display:none">
    <div class="modal-content">
        <span class="close" onclick="closeForm()">&times;</span>
        <h2 id="formTitle">Tambah Kategori</h2>
        <form id="categoryFormElement" method="POST" action="">
    <?php echo $csrf_field ?? ''; ?>
            <div class="form-group">
                <label for="name">Nama Kategori</label>
                <input type="text" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<!-- Table Categories -->
<table class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Jumlah Produk</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($categories)): ?>
        <tr>
            <td colspan="3" style="text-align:center; padding:20px;">Tidak ada kategori. <a href="javascript:showForm()">Tambah kategori</a></td>
        </tr>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td><span class="badge"><?= $cat['product_count'] ?> Produk</span></td>
                <td>
                    <button class="btn btn-sm" onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name']) ?>')">Edit</button>
                    <form method="POST" action="<?= BASE_URL ?>/categories/delete/<?= $cat['id'] ?>" style="display:inline" onsubmit="return confirm('Hapus kategori ini?')">
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<style>
.modal { position:fixed; z-index:1; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.4); }
.modal-content { background-color:#fefefe; margin:10% auto; padding:20px; border:1px solid #888; width:400px; border-radius:8px; }
.close { color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer; }
.close:hover { color:#000; }
.form-group { margin-bottom:15px; }
.form-group label { display:block; margin-bottom:5px; font-weight:bold; }
.form-group input { width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; }
</style>

<script>
function showForm() {
    document.getElementById('categoryForm').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Tambah Kategori';
    document.getElementById('categoryFormElement').action = '<?= BASE_URL ?>/categories/store';
    document.getElementById('categoryFormElement').reset();
    document.getElementById('name').value = '';
}

function editCategory(id, name) {
    document.getElementById('categoryForm').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Edit Kategori';
    document.getElementById('categoryFormElement').action = '<?= BASE_URL ?>/categories/update/' + id;
    document.getElementById('name').value = name;
}

function closeForm() {
    document.getElementById('categoryForm').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('categoryForm');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
