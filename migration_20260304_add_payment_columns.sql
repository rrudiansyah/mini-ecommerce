-- Migration: Add Payment columns to orders table
-- Run this SQL query to update your database

ALTER TABLE orders
ADD COLUMN payment_status ENUM('unpaid','paid','failed') DEFAULT 'unpaid',
ADD COLUMN payment_method VARCHAR(50) NULL,
ADD COLUMN payment_date DATETIME NULL;
