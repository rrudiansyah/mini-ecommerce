<?php
/**
 * RBAC Seed Migration
 * Initializes system roles, permissions, and assigns existing admins to Admin role
 * Run this after running migration_20260304_add_rbac_schema.sql
 */

// Load database configuration
$config = require __DIR__ . '/config/database.php';

try {
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']}",
        $config['user'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_THROW]
    );

    echo "[1/4] Inserting permissions...\n";

    // Define all permissions in the system
    $permissions = [
        // Dashboard
        ['name' => 'dashboard.view', 'description' => 'View dashboard'],

        // Products
        ['name' => 'products.read', 'description' => 'View products list'],
        ['name' => 'products.create', 'description' => 'Create new product'],
        ['name' => 'products.update', 'description' => 'Edit existing product'],
        ['name' => 'products.delete', 'description' => 'Delete product'],
        ['name' => 'products.export', 'description' => 'Export products'],

        // Categories
        ['name' => 'categories.read', 'description' => 'View categories list'],
        ['name' => 'categories.create', 'description' => 'Create new category'],
        ['name' => 'categories.update', 'description' => 'Edit existing category'],
        ['name' => 'categories.delete', 'description' => 'Delete category'],

        // Orders
        ['name' => 'orders.read', 'description' => 'View orders'],
        ['name' => 'orders.create', 'description' => 'Create new order'],
        ['name' => 'orders.update', 'description' => 'Edit order details'],
        ['name' => 'orders.update_status', 'description' => 'Update order status'],
        ['name' => 'orders.record_payment', 'description' => 'Record payment'],
        ['name' => 'orders.view_invoice', 'description' => 'View and print invoices'],
        ['name' => 'orders.export', 'description' => 'Export orders'],

        // Reports
        ['name' => 'reports.view', 'description' => 'View reports'],
        ['name' => 'reports.sales', 'description' => 'View sales analytics'],

        // Users/Admins
        ['name' => 'admins.manage', 'description' => 'Manage users and roles'],
        ['name' => 'admins.view', 'description' => 'View users list'],
        ['name' => 'admins.create', 'description' => 'Create new user'],
        ['name' => 'admins.update', 'description' => 'Edit user'],
        ['name' => 'admins.delete', 'description' => 'Delete user'],

        // Settings
        ['name' => 'settings.view', 'description' => 'View settings'],
        ['name' => 'settings.update', 'description' => 'Update settings'],
    ];

    // Insert permissions (ignore duplicates)
    $stmtPerm = $db->prepare("
        INSERT IGNORE INTO permissions (name, description)
        VALUES (?, ?)
    ");

    foreach ($permissions as $perm) {
        $stmtPerm->execute([$perm['name'], $perm['description']]);
    }

    echo "   ✓ Inserted " . count($permissions) . " permissions\n";

    // Get all permission IDs mapped by name
    $permStmt = $db->query("SELECT id, name FROM permissions");
    $permIds = [];
    while ($row = $permStmt->fetch(PDO::FETCH_ASSOC)) {
        $permIds[$row['name']] = $row['id'];
    }

    echo "\n[2/4] Creating system roles...\n";

    // Get all store IDs to create roles for each store
    $storesStmt = $db->query("SELECT id FROM stores");
    $stores = $storesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Define role structures with their permissions
    $roles = [
        'Admin' => [
            'description' => 'Full system access',
            'permissions' => array_keys($permIds) // All permissions
        ],
        'Manager' => [
            'description' => 'Operational management',
            'permissions' => [
                'dashboard.view',
                'products.read', 'products.create', 'products.update', 'products.delete',
                'categories.read', 'categories.create', 'categories.update', 'categories.delete',
                'orders.read', 'orders.create', 'orders.update', 'orders.update_status', 'orders.record_payment', 'orders.view_invoice',
                'reports.view',
                'settings.view'
            ]
        ],
        'Staff' => [
            'description' => 'Basic operations and order management',
            'permissions' => [
                'dashboard.view',
                'products.read',
                'categories.read',
                'orders.read', 'orders.create', 'orders.update_status', 'orders.record_payment', 'orders.view_invoice'
            ]
        ],
        'Viewer' => [
            'description' => 'Read-only access',
            'permissions' => [
                'dashboard.view',
                'products.read',
                'categories.read',
                'orders.read',
                'reports.view'
            ]
        ]
    ];

    // Insert roles for each store
    $stmtRole = $db->prepare("
        INSERT IGNORE INTO roles (store_id, name, description, is_system)
        VALUES (?, ?, ?, 1)
    ");

    foreach ($stores as $store) {
        foreach ($roles as $roleName => $roleData) {
            $stmtRole->execute([$store['id'], $roleName, $roleData['description']]);
        }
    }

    echo "   ✓ Created " . count($roles) . " system roles for " . count($stores) . " store(s)\n";

    // Get all newly created role IDs
    $rolesStmt = $db->query("
        SELECT id, store_id, name FROM roles WHERE is_system = 1
    ");
    $roleIds = [];
    while ($row = $rolesStmt->fetch(PDO::FETCH_ASSOC)) {
        $key = $row['store_id'] . '-' . $row['name'];
        $roleIds[$key] = $row['id'];
    }

    echo "\n[3/4] Assigning permissions to roles...\n";

    // Assign permissions to roles
    $stmtRolePerm = $db->prepare("
        INSERT IGNORE INTO role_permissions (role_id, permission_id)
        VALUES (?, ?)
    ");

    $totalAssignments = 0;
    foreach ($stores as $store) {
        foreach ($roles as $roleName => $roleData) {
            $roleKey = $store['id'] . '-' . $roleName;
            $roleId = $roleIds[$roleKey];

            foreach ($roleData['permissions'] as $permName) {
                if (isset($permIds[$permName])) {
                    $stmtRolePerm->execute([$roleId, $permIds[$permName]]);
                    $totalAssignments++;
                }
            }
        }
    }

    echo "   ✓ Assigned $totalAssignments permission-to-role relationships\n";

    echo "\n[4/4] Assigning existing admins to Admin role...\n";

    // Get all existing admins
    $adminsStmt = $db->query("SELECT id, store_id FROM admins WHERE is_active = 1");
    $admins = $adminsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Assign each admin to their store's Admin role
    $stmtAdminRole = $db->prepare("
        INSERT IGNORE INTO admin_roles (admin_id, role_id)
        VALUES (?, ?)
    ");

    foreach ($admins as $admin) {
        $roleKey = $admin['store_id'] . '-Admin';
        if (isset($roleIds[$roleKey])) {
            $stmtAdminRole->execute([$admin['id'], $roleIds[$roleKey]]);
        }
    }

    echo "   ✓ Assigned " . count($admins) . " existing admin(s) to Admin role\n";

    echo "\n✅ RBAC setup completed successfully!\n";
    echo "\nSummary:\n";
    echo "  • Permissions: " . count($permissions) . "\n";
    echo "  • Stores: " . count($stores) . "\n";
    echo "  • Roles per store: " . count($roles) . "\n";
    echo "  • Existing admins migrated: " . count($admins) . "\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
