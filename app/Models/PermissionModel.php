<?php

class PermissionModel extends Model
{
    protected string $table = 'permissions';

    /**
     * Get all permissions grouped by module
     */
    public function groupByModule(): array
    {
        $permissions = $this->all();
        $grouped = [];

        foreach ($permissions as $perm) {
            [$module, $action] = explode('.', $perm['name']);
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $perm;
        }

        return $grouped;
    }

    /**
     * Get all permissions as key-value (name => id)
     */
    public function getAsKeyValue(): array
    {
        $perms = $this->all();
        $result = [];
        foreach ($perms as $perm) {
            $result[$perm['name']] = $perm['id'];
        }
        return $result;
    }
}
