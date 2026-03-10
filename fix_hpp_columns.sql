ALTER TABLE `products`
  ADD COLUMN `hpp`      decimal(10,2) NOT NULL DEFAULT '0.00',
  ADD COLUMN `hpp_type` enum('manual','auto') NOT NULL DEFAULT 'manual';