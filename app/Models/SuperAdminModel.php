<?php

class SuperAdminModel extends Model
{
    protected string $table = 'super_admins';

    public function findByUsername(string $username): array|false
    {
        return $this->queryOne(
            "SELECT * FROM super_admins WHERE username = ?",
            [$username]
        );
    }
}
