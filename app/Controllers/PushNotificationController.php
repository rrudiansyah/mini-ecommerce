<?php

use Firebase\JWT\JWT;

class PushNotificationController extends Controller
{
    private object $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Subscribe user untuk push notifications
     */
    public function subscribe(): void
    {
        // Tidak perlu auth untuk subscribe (client-side)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['endpoint'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid subscription']);
                return;
            }

            $storeId = $_SESSION['store_id'] ?? null;

            // Jika tidak ada store_id, simpan dengan session
            if (!$storeId) {
                $_SESSION['push_subscription'] = $input;
                echo json_encode(['success' => true]);
                return;
            }

            // Simpan subscription ke database
            $endpoint = $input['endpoint'] ?? '';
            $p256dh = $input['keys']['p256dh'] ?? '';
            $auth = $input['keys']['auth'] ?? '';

            $query = "INSERT INTO push_subscriptions (store_id, endpoint, p256dh, auth_key, created_at)
                      VALUES (:store_id, :endpoint, :p256dh, :auth, NOW())
                      ON DUPLICATE KEY UPDATE updated_at = NOW()";

            $this->db->query($query);
            $this->db->bind(':store_id', $storeId);
            $this->db->bind(':endpoint', $endpoint);
            $this->db->bind(':p256dh', $p256dh);
            $this->db->bind(':auth', $auth);
            $this->db->execute();

            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Subscription saved']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Unsubscribe user dari push notifications
     */
    public function unsubscribe(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['endpoint'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid endpoint']);
                return;
            }

            $query = "DELETE FROM push_subscriptions WHERE endpoint = :endpoint";
            $this->db->query($query);
            $this->db->bind(':endpoint', $input['endpoint']);
            $this->db->execute();

            http_response_code(200);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Send push notification ke semua subscribers
     * (Digunakan oleh admin untuk notifikasi pesanan baru)
     */
    public function sendNotification(): void
    {
        $this->requireAuth();
        $this->requirePermission('orders.read');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $storeId = $_SESSION['store_id'];

            $title = $input['title'] ?? 'Notifikasi Toko';
            $body = $input['body'] ?? '';
            $url = $input['url'] ?? '/orders';

            // Ambil semua subscribers untuk store ini
            $query = "SELECT * FROM push_subscriptions WHERE store_id = :store_id";
            $this->db->query($query);
            $this->db->bind(':store_id', $storeId);
            $subscriptions = $this->db->resultSet();

            $vapidPublicKey = $_ENV['VAPID_PUBLIC_KEY'] ?? '';
            $vapidPrivateKey = $_ENV['VAPID_PRIVATE_KEY'] ?? '';

            if (!$vapidPublicKey || !$vapidPrivateKey) {
                http_response_code(500);
                echo json_encode(['error' => 'VAPID keys not configured']);
                return;
            }

            $sent = 0;
            $failed = 0;

            foreach ($subscriptions as $sub) {
                try {
                    $this->sendPushToSubscription(
                        $sub,
                        $title,
                        $body,
                        $url,
                        $vapidPublicKey,
                        $vapidPrivateKey
                    );
                    $sent++;
                } catch (Exception $e) {
                    $failed++;
                    error_log('Push send failed: ' . $e->getMessage());
                }
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'sent' => $sent,
                'failed' => $failed
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Helper untuk send push ke subscription
     */
    private function sendPushToSubscription(
        array $subscription,
        string $title,
        string $body,
        string $url,
        string $vapidPublicKey,
        string $vapidPrivateKey
    ): void {
        // Data payload
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => '/pwa/icon-192.png',
            'badge' => '/pwa/icon-72.png'
        ]);

        // VAPID headers
        $vapidHeaders = $this->getVAPIDHeaders(
            $subscription['endpoint'],
            $vapidPublicKey,
            $vapidPrivateKey
        );

        // Send via curl
        $ch = curl_init($subscription['endpoint']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'Authorization: vapid t=' . $vapidHeaders['Authorization'],
            'Crypto-Key: p256ecdsa=' . $vapidHeaders['Crypto-Key']
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode >= 400) {
            throw new Exception("Push service returned $statusCode");
        }
    }

    /**
     * Generate VAPID headers
     */
    private function getVAPIDHeaders(
        string $endpoint,
        string $vapidPublicKey,
        string $vapidPrivateKey
    ): array {
        $now = time();
        $exp = $now + 3600;
        $endpointUrl = parse_url($endpoint)['host'];

        $payload = [
            'aud' => 'https://' . $endpointUrl,
            'exp' => $exp,
            'sub' => 'mailto:admin@example.com'
        ];

        try {
            $jwt = JWT::encode($payload, $vapidPrivateKey, 'ES256');
        } catch (Exception $e) {
            $jwt = '';
        }

        return [
            'Authorization' => $jwt,
            'Crypto-Key' => 'p256ecdsa=' . $vapidPublicKey
        ];
    }
}
