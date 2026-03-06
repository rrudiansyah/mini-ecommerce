#!/usr/bin/env php
<?php
/**
 * RBAC Migration Runner - Direct Database Connection
 */

echo "════════════════════════════════════════════════════════════════\n";
echo "  RBAC Migration Runner\n";
echo "════════════════════════════════════════════════════════════════\n\n";

try {
    // Connect to database directly (localhost for development)
    $db = new PDO(
        'mysql:host=localhost;dbname=ecommerce_builder;charset=utf8mb4',
        'ecommerce_user',
        'ecommerce_pass',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    echo "[OK] Connected to database: ecommerce_builder\n\n";

    // Step 1: Run SQL migration
    echo "[1/2] Running SQL schema migration...\n";
    $sqlFile = __DIR__ . '/migration_20260304_add_rbac_schema.sql';
    $sql = file_get_contents($sqlFile);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $count = 0;
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $db->exec($stmt);
            $count++;
        }
    }
    echo "      [OK] Executed $count SQL statements\n\n";

    // Step 2: Run PHP seed
    echo "[2/2] Running seed migration...\n";

    // Permissions
    echo "      Creating permissions...\n";

    $permissions = [
        ['dashboard.view', 'View dashboard'],
        ['products.read', 'View products list'],
        ['products.create', 'Create new product'],
        ['products.update', 'Edit existing product'],
        ['products.delete', 'Delete product'],
        ['categories.read', 'View categories list'],
        ['categories.create', 'Create new category'],
        ['categories.update', 'Edit existing category'],
        ['categories.delete', 'Delete category'],
        ['orders.read', 'View orders'],
        ['orders.create', 'Create new order'],
        ['orders.update', 'Edit order details'],
        ['orders.update_status', 'Update order status'],
        ['orders.record_payment', 'Record payment'],
        ['orders.view_invoice', 'View and print invoices'],
        ['reports.view', 'View reports'],
        ['reports.sales', 'View sales analytics'],
        ['admins.manage', 'Manage users and roles'],
        ['settings.view', 'View settings'],
        ['settings.update', 'Update settings'],
    ];

    $stmtPerm = $db->prepare("INSERT IGNORE INTO permissions (name, description) VALUES (?, ?)");
    foreach ($permissions as $perm) {
        $stmtPerm->execute([$perm[0], $perm[1]]);
    }
    echo "      [OK] Created " . count($permissions) . " permissions\n";

    // Get permission IDs
    echo "      Creating system roles...\n";
    $permStmt = $db->query("SELECT id, name FROM permissions");
    $permIds = [];
    while ($row = $permStmt->fetch()) {
        $permIds[$row['name']] = $row['id'];
    }

    // Get stores
    $storesStmt = $db->query("SELECT id FROM stores");
    $stores = $storesStmt->fetchAll();

    // Define roles
    $roles = [
        'Admin' => array_keys($permIds),
        'Manager' => ['dashboard.view', 'products.read', 'products.create', 'products.update', 'products.delete',
                      'categories.read', 'categories.create', 'categories.update', 'categories.delete',
                      'orders.read', 'orders.create', 'orders.update', 'orders.update_status', 'orders.record_payment', 'orders.view_invoice',
                      'reports.view', 'settings.view'],
        'Staff' => ['dashboard.view', 'products.read', 'categories.read',
                    'orders.read', 'orders.create', 'orders.update_status', 'orders.record_payment', 'orders.view_invoice'],
        'Viewer' => ['dashboard.view', 'products.read', 'categories.read', 'orders.read', 'reports.view'],
    ];

    // Create roles for each store
    $stmtRole = $db->prepare("INSERT IGNORE INTO roles (store_id, name, description, is_system) VALUES (?, ?, ?, 1)");
    $roleIds = [];

    foreach ($stores as $store) {
        foreach ($roles as $roleName => $perms) {
            $desc = match($roleName) {
                'Admin' => 'Full system access',
                'Manager' => 'Operational management',
                'Staff' => 'Basic operations and order management',
                'Viewer' => 'Read-only access',
            };
            $stmtRole->execute([$store['id'], $roleName, $desc]);
        }
    }

    // Get created role IDs
    $rolesStmt = $db->query("SELECT id, store_id, name FROM roles WHERE is_system = 1");
    foreach ($rolesStmt->fetchAll() as $role) {
        $key = $role['store_id'] . '-' . $role['name'];
        $roleIds[$key] = $role['id'];
    }

    echo "      [OK] Created roles for " . count($stores) . " store(s)\n";

    // Assign permissions to roles
    echo "      Assigning permissions to roles...\n";
    $stmtRolePerm = $db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");

    $count = 0;
    foreach ($stores as $store) {
        foreach ($roles as $roleName => $permNames) {
            $roleKey = $store['id'] . '-' . $roleName;
            if (isset($roleIds[$roleKey])) {
                $roleId = $roleIds[$roleKey];
                foreach ($permNames as $permName) {
                    if (isset($permIds[$permName])) {
                        $stmtRolePerm->execute([$roleId, $permIds[$permName]]);
                        $count++;
                    }
                }
            }
        }
    }
    echo "      [OK] Assigned $count permissions to roles\n";

    // Assign existing admins to Admin role
    echo "      Assigning existing admins to Admin role...\n";
    $adminsStmt = $db->query("SELECT id, store_id FROM admins WHERE is_active = 1");
    $admins = $adminsStmt->fetchAll();

    $stmtAdminRole = $db->prepare("INSERT IGNORE INTO admin_roles (admin_id, role_id) VALUES (?, ?)");
    foreach ($admins as $admin) {
        $roleKey = $admin['store_id'] . '-Admin';
        if (isset($roleIds[$roleKey])) {
            $stmtAdminRole->execute([$admin['id'], $roleIds[$roleKey]]);
        }
    }
    echo "      [OK] Assigned " . count($admins) . " admin(s) to Admin role\n\n";

    echo "✅ All migrations completed successfully!\n\n";
    echo "RBAC system is ready for use:\n";
    echo "  • Permissions: " . count($permissions) . "\n";
    echo "  • Stores: " . count($stores) . "\n";
    echo "  • Admins migrated: " . count($admins) . "\n";
    echo "\nNext steps:\n";
    echo "  1. Test login to verify permissions load\n";
    echo "  2. Create user management interface (Step 7)\n";
    echo "  3. Add permission checks to controllers (Step 9)\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
