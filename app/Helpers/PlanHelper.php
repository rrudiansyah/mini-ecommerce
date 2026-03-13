<?php

/**
 * PlanHelper — Pembatas fitur berdasarkan paket toko (basic/pro/bisnis)
 *
 * Cara pakai:
 *   PlanHelper::canFeature('inventory')      → bool
 *   PlanHelper::limit('products')            → int (-1 = unlimited)
 *   PlanHelper::isOverLimit('products', 20)  → bool
 *   PlanHelper::planName()                   → 'Basic' / 'Pro' / 'Bisnis'
 *   PlanHelper::isExpired()                  → bool
 */
class PlanHelper
{
    // ── Definisi paket ─────────────────────────────────────────────
    private static array $plans = [
        'basic' => [
            'label'    => 'Basic',
            'color'    => '#6b7280',
            'emoji'    => '🥉',
            'limits'   => [
                'products' => 20,
                'admins'   => 1,
            ],
            'features' => [
                'inventory'        => false,
                'export'           => false,
                'variants'         => false,
                'hpp_manual'       => true,   // HPP input manual ✅
                'hpp_auto'         => false,  // HPP dari resep bahan ❌
                'reports_advanced' => false,
            ],
        ],
        'pro' => [
            'label'    => 'Pro',
            'color'    => '#2563a8',
            'emoji'    => '🥈',
            'limits'   => [
                'products' => 100,
                'admins'   => 5,
            ],
            'features' => [
                'inventory'        => true,
                'export'           => true,
                'variants'         => false,  // Varian hanya Bisnis
                'hpp_manual'       => true,   // HPP input manual ✅
                'hpp_auto'         => true,   // HPP dari resep bahan ✅
                'reports_advanced' => true,
            ],
        ],
        'bisnis' => [
            'label'    => 'Bisnis',
            'color'    => '#d97706',
            'emoji'    => '🥇',
            'limits'   => [
                'products' => -1,   // unlimited
                'admins'   => -1,   // unlimited
            ],
            'features' => [
                'inventory'        => true,
                'export'           => true,
                'variants'         => true,   // Varian produk ✅
                'hpp_manual'       => true,   // HPP input manual ✅
                'hpp_auto'         => true,   // HPP dari resep bahan ✅
                'reports_advanced' => true,
            ],
        ],
    ];

    // ── Ambil plan toko saat ini ───────────────────────────────────
    public static function current(): string
    {
        return $_SESSION['store_plan'] ?? 'basic';
    }

    public static function planName(): string
    {
        $plan = self::current();
        return self::$plans[$plan]['label'] ?? 'Basic';
    }

    public static function planEmoji(): string
    {
        $plan = self::current();
        return self::$plans[$plan]['emoji'] ?? '🥉';
    }

    public static function planColor(): string
    {
        $plan = self::current();
        return self::$plans[$plan]['color'] ?? '#6b7280';
    }

    public static function isExpired(): bool
    {
        $exp = $_SESSION['store_plan_expires'] ?? null;
        if (!$exp) return false;
        return strtotime($exp) < time();
    }

    // ── Cek apakah fitur tersedia di paket ini ─────────────────────
    public static function canFeature(string $feature): bool
    {
        if (self::isExpired()) return false;
        $plan = self::current();
        return self::$plans[$plan]['features'][$feature] ?? false;
    }

    // ── Ambil limit angka (-1 = unlimited) ────────────────────────
    public static function limit(string $resource): int
    {
        $plan = self::current();
        return self::$plans[$plan]['limits'][$resource] ?? 0;
    }

    // ── Cek apakah sudah melebihi limit ───────────────────────────
    public static function isOverLimit(string $resource, int $currentCount): bool
    {
        $limit = self::limit($resource);
        if ($limit === -1) return false;        // unlimited
        return $currentCount >= $limit;
    }

    // ── Sisa kuota ────────────────────────────────────────────────
    public static function remaining(string $resource, int $currentCount): int|string
    {
        $limit = self::limit($resource);
        if ($limit === -1) return '∞';
        return max(0, $limit - $currentCount);
    }

    // ── Render badge paket untuk ditampilkan di UI ────────────────
    public static function badge(): string
    {
        $emoji = self::planEmoji();
        $label = self::planName();
        $color = self::planColor();
        $expired = self::isExpired() ? ' (Kedaluwarsa)' : '';
        return "<span style=\"background:{$color};color:#fff;font-size:11px;font-weight:700;"
             . "padding:3px 10px;border-radius:100px;white-space:nowrap\">"
             . "{$emoji} {$label}{$expired}</span>";
    }

    // ── Pesan upgrade ─────────────────────────────────────────────
    public static function upgradeMessage(string $resource): string
    {
        $messages = [
            'products'  => 'Batas produk paket ' . self::planName() . ' tercapai.',
            'admins'    => 'Batas pengguna paket ' . self::planName() . ' tercapai.',
            'inventory' => 'Fitur Stok & HPP tersedia mulai paket Pro.',
            'export'    => 'Fitur Export tersedia mulai paket Pro.',
            'variants'  => 'Fitur Varian Produk tersedia mulai paket Bisnis.',
            'hpp_manual'=> 'Fitur HPP Manual tersedia di semua paket.',
            'hpp_auto'  => 'Fitur HPP dari Resep Bahan tersedia mulai paket Pro.',
        ];
        return ($messages[$resource] ?? 'Fitur ini tidak tersedia di paket Anda.')
             . ' Hubungi Super Admin untuk upgrade.';
    }

    // ── Load plan dari store_id (dipanggil saat login) ─────────────
    public static function loadToSession(int $storeId): void
    {
        require_once ROOT_PATH . '/app/Models/StoreModel.php';
        $storeModel = new StoreModel();
        $store = $storeModel->find($storeId);
        $_SESSION['store_plan']         = $store['plan']            ?? 'basic';
        $_SESSION['store_plan_expires'] = $store['plan_expires_at'] ?? null;
    }

    // ── Daftar semua paket (untuk UI) ─────────────────────────────
    public static function allPlans(): array
    {
        return self::$plans;
    }
}
