<?php

class AdminModel extends Model
{
    protected string $table = 'admins';

    public function findByUsername(string $username): array|false
    {
        return $this->queryOne("SELECT * FROM admins WHERE username = ?", [$username]);
    }

    // Tetap ada untuk backward compatibility
    public function findByEmail(string $email): array|false
    {
        return $this->queryOne("SELECT * FROM admins WHERE email = ?", [$email]);
    }

    public function getRoles(int $adminId): array
    {
        return $this->query(
            "SELECT r.id, r.name FROM roles r
             JOIN admin_roles ar ON ar.role_id = r.id
             WHERE ar.admin_id = ?",
            [$adminId]
        );
    }

    public function getPermissions(int $adminId): array
    {
        return $this->query(
            "SELECT DISTINCT p.id, p.name, p.description FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             JOIN admin_roles ar ON ar.role_id = rp.role_id
             WHERE ar.admin_id = ?
             ORDER BY p.name",
            [$adminId]
        );
    }

    public function getPermissionNames(int $adminId): array
    {
        $perms = $this->query(
            "SELECT DISTINCT p.name FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             JOIN admin_roles ar ON ar.role_id = rp.role_id
             WHERE ar.admin_id = ?",
            [$adminId]
        );
        return array_column($perms, 'name');
    }

    public function getRoleNames(int $adminId): array
    {
        $roles = $this->query(
            "SELECT DISTINCT r.name FROM roles r
             JOIN admin_roles ar ON ar.role_id = r.id
             WHERE ar.admin_id = ?",
            [$adminId]
        );
        return array_column($roles, 'name');
    }

    public function hasPermission(int $adminId, string $permission): bool
    {
        $result = $this->queryOne(
            "SELECT 1 FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             JOIN admin_roles ar ON ar.role_id = rp.role_id
             WHERE ar.admin_id = ? AND p.name = ?",
            [$adminId, $permission]
        );
        return $result !== false;
    }

    public function hasRole(int $adminId, string $role): bool
    {
        $result = $this->queryOne(
            "SELECT 1 FROM roles r
             JOIN admin_roles ar ON ar.role_id = r.id
             WHERE ar.admin_id = ? AND r.name = ?",
            [$adminId, $role]
        );
        return $result !== false;
    }
}
