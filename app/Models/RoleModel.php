<?php

class RoleModel extends Model
{
    protected string $table = 'roles';

    /**
     * Get all roles for a specific store with their permissions
     */
    public function getByStoreWithPermissions(int $storeId): array
    {
        $roles = $this->query(
            "SELECT r.*, COUNT(rp.id) as permission_count
             FROM roles r
             LEFT JOIN role_permissions rp ON rp.role_id = r.id
             WHERE r.store_id = ?
             GROUP BY r.id
             ORDER BY r.name",
            [$storeId]
        );

        // Enrich with permission details
        foreach ($roles as &$role) {
            $role['permissions'] = $this->getPermissionsForRole($role['id']);
        }

        return $roles;
    }

    /**
     * Get all permissions assigned to a role
     */
    public function getPermissionsForRole(int $roleId): array
    {
        return $this->query(
            "SELECT p.id, p.name, p.description
             FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = ?
             ORDER BY p.name",
            [$roleId]
        );
    }

    /**
     * Assign a permission to a role
     */
    public function assignPermission(int $roleId, int $permissionId): bool
    {
        return $this->db->prepare(
            "INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)"
        )->execute([$roleId, $permissionId]);
    }

    /**
     * Remove a permission from a role
     */
    public function removePermission(int $roleId, int $permissionId): bool
    {
        return $this->db->prepare(
            "DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?"
        )->execute([$roleId, $permissionId]);
    }

    /**
     * Check if a role has a permission
     */
    public function hasPermission(int $roleId, int $permissionId): bool
    {
        $result = $this->queryOne(
            "SELECT 1 FROM role_permissions WHERE role_id = ? AND permission_id = ?",
            [$roleId, $permissionId]
        );
        return $result !== false;
    }
}
