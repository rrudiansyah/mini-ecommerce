<?php

/**
 * RateLimiter — file-based, tanpa dependency eksternal
 * Menyimpan attempt count di storage/rate_limit/
 */
class RateLimiter
{
    private string $storageDir;
    private int    $maxAttempts;
    private int    $decaySeconds;

    public function __construct(int $maxAttempts = 5, int $decaySeconds = 300)
    {
        $this->storageDir   = ROOT_PATH . '/storage/rate_limit';
        $this->maxAttempts  = $maxAttempts;
        $this->decaySeconds = $decaySeconds;

        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
            file_put_contents($this->storageDir . '/.htaccess', "Deny from all\n");
        }
    }

    /**
     * Cek apakah key sudah melampaui batas
     */
    public function tooManyAttempts(string $key): bool
    {
        $data = $this->getData($key);
        if (!$data) return false;

        // Reset jika sudah expired
        if (time() > $data['reset_at']) {
            $this->clear($key);
            return false;
        }

        return $data['attempts'] >= $this->maxAttempts;
    }

    /**
     * Tambah 1 attempt
     */
    public function hit(string $key): void
    {
        $data = $this->getData($key);

        if (!$data || time() > $data['reset_at']) {
            $data = ['attempts' => 0, 'reset_at' => time() + $this->decaySeconds];
        }

        $data['attempts']++;
        $this->saveData($key, $data);
    }

    /**
     * Sisa detik sampai unlock
     */
    public function availableIn(string $key): int
    {
        $data = $this->getData($key);
        if (!$data) return 0;
        return max(0, $data['reset_at'] - time());
    }

    /**
     * Sisa attempt yang boleh
     */
    public function remainingAttempts(string $key): int
    {
        $data = $this->getData($key);
        if (!$data || time() > $data['reset_at']) return $this->maxAttempts;
        return max(0, $this->maxAttempts - $data['attempts']);
    }

    /**
     * Reset attempts (dipanggil saat login berhasil)
     */
    public function clear(string $key): void
    {
        $file = $this->filePath($key);
        if (file_exists($file)) unlink($file);
    }

    private function getData(string $key): array|false
    {
        $file = $this->filePath($key);
        if (!file_exists($file)) return false;
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : false;
    }

    private function saveData(string $key, array $data): void
    {
        file_put_contents($this->filePath($key), json_encode($data), LOCK_EX);
    }

    private function filePath(string $key): string
    {
        return $this->storageDir . '/' . md5($key) . '.json';
    }

    /**
     * Generate key dari IP + identifier
     */
    public static function key(string $prefix, string $identifier = ''): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'unknown';
        return $prefix . '|' . $ip . '|' . $identifier;
    }
}
