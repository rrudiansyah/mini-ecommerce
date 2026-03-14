<div class="section">
    <div class="permission-header">
        <h2>🔐 Kelola Permission: <?= htmlspecialchars($role['name']) ?></h2>
        <a href="<?= BASE_URL ?>/settings/roles" class="btn" style="background: #64748b; color: white;">← Kembali</a>
    </div>

    <div class="permission-container">
        <!-- Module Tabs -->
        <div class="module-tabs">
            <button class="module-tab active" data-module="all">Semua (<?= count(array_merge(...array_values($allPermissions))) ?>)</button>
            <?php foreach ($allPermissions as $module => $permissions): ?>
                <button class="module-tab" data-module="<?= $module ?>">
                    <?= ucfirst($module) ?> (<?= count($permissions) ?>)
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Permissions Grid -->
        <div class="permissions-grid" id="permissionsGrid">
            <?php foreach ($allPermissions as $module => $permissions): ?>
                <?php foreach ($permissions as $permission): ?>
                    <?php
                    $isChecked = in_array($permission['id'], $rolePermissionIds);
                    ?>
                    <div class="permission-item" data-module="<?= $module ?>">
                        <label class="permission-checkbox">
                            <input type="checkbox"
                                   class="permission-toggle"
                                   data-permission-id="<?= $permission['id'] ?>"
                                   data-permission-name="<?= htmlspecialchars($permission['name']) ?>"
                                   <?= $isChecked ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            <div class="permission-label">
                                <strong><?= htmlspecialchars($permission['name']) ?></strong>
                                <small><?= htmlspecialchars($permission['description'] ?? '') ?></small>
                            </div>
                        </label>
                        <div class="permission-status" style="display: none;">
                            <span class="status-saving">Menyimpan...</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.permission-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.permission-header h2 {
    margin: 0;
}

.permission-container {
    background: #f8fafc;
    border-radius: 8px;
    overflow: hidden;
}

.module-tabs {
    display: flex;
    border-bottom: 1px solid #e2e8f0;
    overflow-x: auto;
    background: white;
}

.module-tab {
    flex: 1;
    min-width: 140px;
    padding: 15px 20px;
    border: none;
    background: white;
    color: #64748b;
    cursor: pointer;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
    white-space: nowrap;
}

.module-tab:hover {
    color: #3b82f6;
    background: #f0f4f8;
}

.module-tab.active {
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

.permissions-grid {
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 15px;
}

.permission-item {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 15px;
    transition: all 0.2s;
}

.permission-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
}

.permission-item.hidden {
    display: none;
}

.permission-checkbox {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    gap: 12px;
}

.permission-checkbox input {
    margin-top: 4px;
    cursor: pointer;
    width: 18px;
    height: 18px;
}

.permission-label {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.permission-label strong {
    color: #1e293b;
    font-size: 14px;
}

.permission-label small {
    color: #94a3b8;
    font-size: 12px;
    line-height: 1.4;
}

.permission-status {
    margin-top: 8px;
    padding: 8px;
    border-radius: 4px;
    font-size: 12px;
    text-align: center;
}

.status-saving {
    color: #f59e0b;
}

.status-success {
    color: #10b981;
    display: block;
}

.status-error {
    color: #ef4444;
    display: block;
}

@media (max-width: 768px) {
    .permissions-grid {
        grid-template-columns: 1fr;
    }

    .module-tabs {
        flex-wrap: wrap;
    }

    .module-tab {
        min-width: 100px;
        padding: 12px 15px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Module tab filtering
    const moduleTabs = document.querySelectorAll('.module-tab');
    const permissionItems = document.querySelectorAll('.permission-item');

    moduleTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const selectedModule = this.dataset.module;

            // Update active tab
            moduleTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Filter items
            permissionItems.forEach(item => {
                if (selectedModule === 'all' || item.dataset.module === selectedModule) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    });

    // Permission toggle with AJAX
    const toggles = document.querySelectorAll('.permission-toggle');

    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const permissionId = this.dataset.permissionId;
            const permissionName = this.dataset.permissionName;
            const isChecked = this.checked;
            const action = isChecked ? 'add' : 'remove';
            const item = this.closest('.permission-item');
            const statusDiv = item.querySelector('.permission-status');

            // Show saving status
            statusDiv.style.display = 'block';
            statusDiv.innerHTML = '<span class="status-saving">Menyimpan...</span>';

            // Send AJAX request
            fetch('<?= BASE_URL ?>/settings/roles/<?= $role['id'] ?>/permissions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                },
                body: 'permission_id=' + permissionId + '&action=' + action + '&_csrf_token=' + document.querySelector('meta[name="csrf-token"]').content
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.innerHTML = '<span class="status-success">✓ ' + data.message + '</span>';
                    setTimeout(() => {
                        statusDiv.style.display = 'none';
                    }, 2000);
                } else {
                    statusDiv.innerHTML = '<span class="status-error">Error: ' + (data.error || 'Gagal menyimpan') + '</span>';
                    // Revert checkbox
                    this.checked = !isChecked;
                }
            })
            .catch(error => {
                statusDiv.innerHTML = '<span class="status-error">Error: ' + error.message + '</span>';
                // Revert checkbox
                this.checked = !isChecked;
            });
        });
    });
});
</script>
