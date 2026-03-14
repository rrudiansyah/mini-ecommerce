-- Migration: Add PWA Push Notifications Table
-- Run this SQL query to add push notification subscription support

CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT,
    endpoint VARCHAR(512) NOT NULL UNIQUE,
    p256dh TEXT NOT NULL,
    auth_key TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    INDEX idx_store_id (store_id),
    INDEX idx_endpoint (endpoint)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
