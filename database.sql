-- Mini E-Commerce Builder — Database Schema

CREATE DATABASE IF NOT EXISTS ecommerce_builder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_builder;

CREATE TABLE stores (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    name         VARCHAR(100) NOT NULL,
    niche        ENUM('coffee','barbershop','restaurant','fashion','bakery','laundry') NOT NULL,
    logo         VARCHAR(255),
    theme_color  VARCHAR(20) DEFAULT '#3b82f6',
    address      TEXT,
    phone        VARCHAR(20),
    is_active    TINYINT DEFAULT 1,
    created_at   DATETIME DEFAULT NOW()
);

CREATE TABLE admins (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    store_id     INT NOT NULL,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(100) UNIQUE NOT NULL,
    password     VARCHAR(255) NOT NULL,
    created_at   DATETIME DEFAULT NOW(),
    FOREIGN KEY (store_id) REFERENCES stores(id)
);

CREATE TABLE categories (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    store_id     INT NOT NULL,
    name         VARCHAR(100) NOT NULL,
    icon         VARCHAR(50),
    FOREIGN KEY (store_id) REFERENCES stores(id)
);

CREATE TABLE products (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    store_id     INT NOT NULL,
    category_id  INT,
    name         VARCHAR(150) NOT NULL,
    description  TEXT,
    price        DECIMAL(10,2) NOT NULL DEFAULT 0,
    image        VARCHAR(255),
    is_available TINYINT DEFAULT 1,
    created_at   DATETIME DEFAULT NOW(),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE orders (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    store_id        INT NOT NULL,
    customer_name   VARCHAR(100) NOT NULL,
    customer_phone  VARCHAR(20),
    total           DECIMAL(10,2) NOT NULL DEFAULT 0,
    status          ENUM('pending','proses','selesai','batal') DEFAULT 'pending',
    note            TEXT,
    payment_status  ENUM('unpaid','paid','failed') DEFAULT 'unpaid',
    payment_method  VARCHAR(50),
    payment_date    DATETIME,
    created_at      DATETIME DEFAULT NOW(),
    FOREIGN KEY (store_id) REFERENCES stores(id)
);

CREATE TABLE order_items (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    order_id    INT NOT NULL,
    product_id  INT NOT NULL,
    qty         INT NOT NULL DEFAULT 1,
    price       DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Data Contoh (Coffee Shop)
INSERT INTO stores (name, niche, theme_color, address, phone) VALUES
('Kopi Nusantara', 'coffee', '#b45309', 'Jl. Merdeka No. 10, Banda Aceh', '0812-0000-0001');

-- Password: admin123
INSERT INTO admins (store_id, name, email, password) VALUES
(1, 'Admin Kopi', 'admin@kopinusantara.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO categories (store_id, name, icon) VALUES
(1, 'Kopi Panas', '☕'), (1, 'Kopi Dingin', '🧊'), (1, 'Non-Kopi', '🍵'), (1, 'Makanan', '🍰');

INSERT INTO products (store_id, category_id, name, description, price) VALUES
(1, 1, 'Americano',    'Espresso + air panas',           18000),
(1, 1, 'Cappuccino',   'Espresso + steamed milk + foam', 25000),
(1, 2, 'Es Kopi Susu', 'Kopi + susu + es batu',          22000),
(1, 2, 'Cold Brew',    'Kopi cold brew 12 jam',          28000),
(1, 3, 'Matcha Latte', 'Matcha premium + susu',          27000),
(1, 4, 'Croissant',    'Croissant butter fresh',         20000);
