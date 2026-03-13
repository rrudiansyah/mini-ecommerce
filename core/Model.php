<?php

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = "id";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(string $orderBy = "id", string $dir = "ASC"): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$dir}");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) { $this->handleError($e, "all:{$this->table}"); }
    }

    public function find(int $id): array|false
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) { $this->handleError($e, "find:{$this->table}"); }
    }

    public function where(string $column, mixed $value): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
            $stmt->execute([$value]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { $this->handleError($e, "where:{$this->table}"); }
    }

    public function create(array $data): int
    {
        try {
            $columns      = implode(", ", array_keys($data));
            $placeholders = implode(", ", array_fill(0, count($data), "?"));
            $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
            $stmt->execute(array_values($data));
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) { $this->handleError($e, "create:{$this->table}"); }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $sets = implode(", ", array_map(fn($col) => "{$col} = ?", array_keys($data)));
            $stmt = $this->db->prepare("UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([...array_values($data), $id]);
        } catch (PDOException $e) { $this->handleError($e, "update:{$this->table}"); }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) { $this->handleError($e, "delete:{$this->table}"); }
    }

    public function count(string $whereCol = null, mixed $value = null): int
    {
        try {
            if ($whereCol && $value !== null) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$whereCol} = ?");
                $stmt->execute([$value]);
            } else {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table}");
                $stmt->execute();
            }
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) { $this->handleError($e, "count:{$this->table}"); }
    }

    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) { $this->handleError($e, "query"); }
    }

    public function queryOne(string $sql, array $params = []): array|false
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) { $this->handleError($e, "queryOne"); }
    }

    // ── Error Handler ──────────────────────────────────────────────
    protected function handleError(PDOException $e, string $context = ""): never
    {
        // PDO error code bisa berupa SQLSTATE string (misal "42S22") atau MySQL int
        $rawCode = $e->getCode();
        $code    = is_numeric($rawCode) ? (int)$rawCode : 0;

        // Ambil MySQL error code dari errorInfo jika ada
        $errorInfo = $e->errorInfo ?? [];
        if (!empty($errorInfo[1])) {
            $code = (int)$errorInfo[1];
        }

        $message = $this->friendlyMessage($code, $e->getMessage());

        // Log error ke file (tidak ditampilkan ke user)
        $logDir = ROOT_PATH . '/storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
        @error_log(
            sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), $context, $e->getMessage()),
            3,
            $logDir . '/db_error.log'
        );

        throw new AppException($message, $code, $e);
    }

    private function friendlyMessage(int $code, string $raw): string
    {
        return match ($code) {
            1048 => $this->extractNullColumn($raw),
            1062 => $this->extractDuplicateField($raw),
            1451 => "Data tidak dapat dihapus karena masih digunakan oleh data lain.",
            1452 => "Data referensi tidak valid. Pastikan data terkait sudah ada.",
            1054 => "Kolom tidak ditemukan di database. Hubungi administrator.",
            1146 => "Tabel belum dibuat. Pastikan migration sudah dijalankan.",
            2002 => "Tidak dapat terhubung ke database. Periksa konfigurasi.",
            default => "Terjadi kesalahan pada database (kode: {$code}). Silakan coba lagi.",
        };
    }

    private function extractNullColumn(string $raw): string
    {
        if (preg_match("/Column '(\w+)' cannot be null/i", $raw, $m)) {
            $labels = [
                'email'    => 'Email',
                'username' => 'Username',
                'name'     => 'Nama',
                'phone'    => 'Nomor telepon',
                'address'  => 'Alamat',
                'password' => 'Password',
                'store_id' => 'ID Toko',
                'slug'     => 'Slug toko',
                'niche'    => 'Jenis usaha',
            ];
            $field = $labels[$m[1]] ?? ucfirst(str_replace('_', ' ', $m[1]));
            return "Field \"{$field}\" wajib diisi.";
        }
        return "Ada field wajib yang belum diisi.";
    }

    private function extractDuplicateField(string $raw): string
    {
        if (preg_match("/Duplicate entry '(.+?)' for key '(.+?)'/i", $raw, $m)) {
            $key = preg_replace('/\w+\./', '', $m[2]); // strip table prefix
            $labels = [
                'username' => 'Username',
                'email'    => 'Email',
                'slug'     => 'Slug toko',
            ];
            $field = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
            return "{$field} \"{$m[1]}\" sudah digunakan. Silakan pilih yang lain.";
        }
        return "Data sudah ada. Gunakan nilai yang berbeda.";
    }
}
