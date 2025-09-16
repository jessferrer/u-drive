-- Create database and tables
CREATE DATABASE IF NOT EXISTS udrivedb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE udrivedb;

CREATE TABLE `affiliates` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `number` varchar(160) NOT NULL,
  `address` varchar(160) NOT NULL
);

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `client_name` varchar(160) NOT NULL,
  `client_number` varchar(160) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `client_id` int(11) DEFAULT NULL
);

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `booking_count` int(11) DEFAULT 0
);

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `make_model` varchar(160) NOT NULL,
  `transmission` enum('AT','MT') DEFAULT 'AT',
  `seats` int(11) DEFAULT 5,
  `rate_per_day` decimal(10,2) DEFAULT 0.00,
  `status` enum('available','unavailable','maintenance','booked') DEFAULT 'available',
  `booked_until` datetime DEFAULT NULL,
  `affiliate_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
);

INSERT INTO `units` (`id`, `make_model`, `transmission`, `seats`, `rate_per_day`, `status`, `booked_until`, `affiliate_id`, `image_url`) VALUES
(1, 'Mirage G4', 'AT', 5, '1500.00', 'available', NULL, 0, 'assets/images/mirage-g4.webp'),
(2, 'Vios', 'AT', 5, '1500.00', 'available', NULL, 0, 'assets/images/vios.webp'),
(3, 'Livina', 'AT', 7, '2500.00', 'available', NULL, 0, 'assets/images/livina.webp'),
(4, 'Innova', 'MT', 7, '2500.00', 'available', NULL, 0, 'assets/images/innova.webp'),
(5, 'HiAce', 'MT', 16, '3000.00', 'available', NULL, 0, 'assets/images/hiace.webp'),
(6, 'Avanza', 'AT', 7, '2500.00', 'available', NULL, 0, 'assets/images/avanza.webp'),
(7, 'Wigo', 'AT', 5, '1500.00', 'available', NULL, 0, 'assets/images/wigo.webp'),
(8, 'Ertiga', 'AT', 7, '2500.00', 'maintenance', NULL, 0, 'assets/images/ertiga.webp'),
(9, 'Navara', 'AT', 5, '3000.00', 'available', NULL, 0, 'assets/images/navara.webp'),
(11, 'HiAce Commuter', 'MT', 16, '3000.00', 'available', NULL, 0, 'assets/images/hiace-commuter.webp'),
(12, 'Almera', 'AT', 5, '1500.00', 'available', NULL, 0, 'assets/images/almera.webp');

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `phone` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','employee','client') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT current_timestamp()
);

ALTER TABLE `affiliates`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `client_id` (`client_id`);

ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `affiliates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

-- Seed data
INSERT INTO users(name,email,password_hash,role) VALUES
('Administrator','admin@udrive.com','$2y$10$BxZaBn2Ig.I6r.CY2kNHCuE8lGhaT/KRgQ25.t5.jbAccBwCFfDHe','admin');

-- Password for all above is: Passw0rd!  (bcrypt hash placeholder)
