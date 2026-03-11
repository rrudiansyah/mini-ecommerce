<?php

/**
 * AuthHelper - Static authorization helper for permission checking
 * Designed to be used throughout controllers and views
 */
class AuthHelper
{
    /**
     * Check if current user has a specific permission
     */
    public static function can(string $permission): bool
    {
        return in_array($permission, $_SESSION['permissions'] ?? []);
    }

    /**
     * Check if current user has a specific role
     */
    public static function hasRole(string $role): bool
    {
        return in_array($role, $_SESSION['roles'] ?? []);
    }

    /**
     * Check if current user has any of the given permissions
     */
    public static function canAny(array $permissions): bool
    {
        $userPermissions = $_SESSION['permissions'] ?? [];
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if current user has all of the given permissions
     */
    public static function canAll(array $permissions): bool
    {
        $userPermissions = $_SESSION['permissions'] ?? [];
        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if current user is an admin
     */
    public static function isAdmin(): bool
    {
        return self::hasRole('Admin');
    }

    /**
     * Check if current session is Super Admin
     */
    public static function isSuperAdmin(): bool
    {
        return !empty($_SESSION['superadmin_id']);
    }

    /**
     * Get all menu permissions for navigation rendering
     * Returns associative array with module => visibility/actions
     */
    public static function getMenuPermissions(): array
    {
        return [
            'dashboard' => self::can('dashboard.view'),
            'products' => [
                'visible' => self::can('products.read'),
                'create' => self::can('products.create'),
                'edit' => self::can('products.update'),
                'delete' => self::can('products.delete'),
            ],
            'categories' => [
                'visible' => self::can('categories.read'),
                'create' => self::can('categories.create'),
                'edit' => self::can('categories.update'),
                'delete' => self::can('categories.delete'),
            ],
            'orders' => [
                'visible' => self::can('orders.read'),
                'create' => self::can('orders.create'),
                'update' => self::can('orders.update'),
                'update_status' => self::can('orders.update_status'),
                'record_payment' => self::can('orders.record_payment'),
                'view_invoice' => self::can('orders.view_invoice'),
            ],
            'reports' => self::can('reports.view'),
            'inventory' => [
                'visible' => self::can('inventory.read'),
                'manage'  => self::can('inventory.manage'),
                'stock_in'=> self::can('inventory.stock_in'),
                'logs'    => self::can('inventory.logs'),
            ],
            'users' => self::can('admins.manage'),
            'settings' => self::isSuperAdmin(), // hanya Super Admin
        ];
    }

    /**
     * Get current user's roles as array
     */
    public static function getRoles(): array
    {
        return $_SESSION['roles'] ?? [];
    }

    /**
     * Get current user's permissions as array
     */
    public static function getPermissions(): array
    {
        return $_SESSION['permissions'] ?? [];
    }

    /**
     * Authorize access to a permission - redirect if denied
     * Returns true if authorized, otherwise redirects and exits
     */
    public static function authorize(string $permission, string $redirectTo = 'dashboard'): bool
    {
        if (!self::can($permission)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses ke fitur ini.'
            ];
            header("Location: " . BASE_URL . "/{$redirectTo}");
            exit;
        }
        return true;
    }

    /**
     * Get role display name in Indonesian
     */
    public static function getRoleDisplayName(string $role): string
    {
        $names = [
            'Admin' => 'Administrator',
            'Manager' => 'Manajer',
            'Staff' => 'Staf',
            'Viewer' => 'Peninjau',
        ];
        return $names[$role] ?? $role;
    }

    /**
     * Get role badge HTML (for display purposes)
     */
    public static function getRoleBadge(string $role): string
    {
        $colors = [
            'Admin' => '#dc2626',
            'Manager' => '#2563eb',
            'Staff' => '#7c3aed',
            'Viewer' => '#6b7280',
        ];

        $color = $colors[$role] ?? '#6b7280';
        $displayName = self::getRoleDisplayName($role);

        return "<span style=\"background:{$color}; color:white; padding:4px 12px; border-radius:4px; font-weight:bold; font-size:12px;\">
            {$displayName}
        </span>";
    }
}
