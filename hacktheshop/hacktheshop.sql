-- HackTheShop Database Schema
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS hacktheshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hacktheshop;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) UNIQUE NOT NULL,
  icon VARCHAR(50),
  challenge_url VARCHAR(500),
  challenge_title VARCHAR(200),
  challenge_description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  img VARCHAR(500),
  description TEXT,
  category_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- User progress (completed challenges)
CREATE TABLE IF NOT EXISTS user_progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  category_id INT NOT NULL,
  completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_progress (user_id, category_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Rewards (products won)
CREATE TABLE IF NOT EXISTS rewards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  category_id INT NOT NULL,
  won_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author VARCHAR(100) NOT NULL,
  text TEXT NOT NULL,
  product VARCHAR(200) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Badges table
CREATE TABLE IF NOT EXISTS badges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  badge_name VARCHAR(100) NOT NULL,
  badge_icon VARCHAR(50),
  earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================
-- SEED DATA
-- =====================

INSERT INTO categories (name, slug, icon, challenge_url, challenge_title, challenge_description) VALUES
('PC & Laptops', 'pc', 'fa-laptop', 'https://www.hackerrank.com/challenges/sql-projects/problem', 'SQL Beginner Challenge', 'Solve a basic SQL query challenge to unlock our best laptops and PCs.'),
('Phones', 'phone', 'fa-mobile', 'https://www.hackerrank.com/challenges/xss-challenge/problem', 'XSS Detection Challenge', 'Identify XSS vulnerabilities to unlock smartphones and tablets.'),
('Audio', 'audio', 'fa-headphones', 'https://www.hackerrank.com/challenges/30-running-time-and-complexity/problem', 'Complexity Challenge', 'Prove your algorithm skills to unlock audio products.'),
('Wearables', 'wearable', 'fa-clock', 'https://www.hackerrank.com/challenges/ctci-fibonacci-numbers/problem', 'Fibonacci Challenge', 'Solve recursion challenge to unlock wearable tech.'),
('Cameras', 'camera', 'fa-camera', 'https://www.hackerrank.com/challenges/30-recursion/problem', 'Recursion Master', 'Master recursion to unlock cameras and drones.'),
('Gaming', 'gaming', 'fa-gamepad', 'https://www.hackerrank.com/challenges/ctci-array-left-rotation/problem', 'Array Rotation Challenge', 'Rotate your way to gaming rewards.'),
('Accessories', 'accessory', 'fa-plug', 'https://www.hackerrank.com/challenges/30-linked-list/problem', 'Linked List Challenge', 'Link your way to accessory rewards.'),
('Screens', 'screen', 'fa-tv', 'https://www.hackerrank.com/challenges/30-binary-search/problem', 'Binary Search Challenge', 'Search efficiently to unlock screens and monitors.');

INSERT INTO products (name, price, img, description, category_id) VALUES
('Laptop Pro X', 799.00, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80', 'Powerful laptop with Intel Core i7, 16GB RAM, 512GB SSD.', 1),
('Gaming Laptop Z', 1299.00, 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400&q=80', 'High performance gaming laptop with RTX 4060, 32GB RAM, 1TB SSD.', 1),
('MacBook Air M2', 999.00, 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&q=80', 'Ultra-thin Apple M2 chip, 8GB RAM, 256GB SSD.', 1),
('Smartphone Z12', 499.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&q=80', '6.5" AMOLED display, 108MP camera, 5000mAh battery.', 2),
('iPhone 15 Pro', 1099.00, 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&q=80', 'Apple iPhone 15 Pro. A17 Pro chip, 48MP camera, titanium design.', 2),
('Samsung S24', 899.00, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&q=80', 'Samsung Galaxy S24. Snapdragon 8 Gen 3, 50MP camera.', 2),
('AirPods Pro 2', 249.00, 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&q=80', 'Active Noise Cancellation, 30hr battery life.', 3),
('Sony WH-1000XM5', 349.00, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80', 'Industry-leading noise cancellation, 30hr battery.', 3),
('JBL Charge 5', 179.00, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&q=80', 'Portable Bluetooth speaker. IP67 waterproof, 20hr battery.', 3),
('Apple Watch S9', 399.00, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80', 'Health tracking, GPS, 18hr battery.', 4),
('Galaxy Watch 6', 299.00, 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=400&q=80', 'Samsung Galaxy Watch 6. Health monitoring, 40hr battery.', 4),
('Garmin Fenix 7', 599.00, 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?w=400&q=80', 'Premium multisport GPS watch. Solar charging, 18-day battery.', 4),
('Sony Alpha A7', 2499.00, 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80', 'Full-frame mirrorless. 33MP sensor, 4K video, 5-axis stabilization.', 5),
('Canon EOS R50', 799.00, 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=400&q=80', 'Entry-level mirrorless. 24MP sensor, 4K video.', 5),
('GoPro Hero 12', 399.00, 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=400&q=80', '5.3K video, HyperSmooth 6.0, waterproof to 10m.', 5),
('PlayStation 5', 499.00, 'https://images.unsplash.com/photo-1593118247619-e2d6f056869e?w=400&q=80', '4K gaming, SSD storage, DualSense controller.', 6),
('Nintendo Switch', 299.00, 'https://images.unsplash.com/photo-1617096200347-cb04ae810b1d?w=400&q=80', 'Hybrid gaming console. Play at home or on the go.', 6),
('Gaming Keyboard', 129.00, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400&q=80', 'Mechanical gaming keyboard. RGB backlight, tactile switches.', 6),
('Logitech MX Master', 99.00, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80', 'Advanced wireless mouse. MagSpeed scroll, ergonomic design.', 7),
('USB-C Hub Pro', 49.00, 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&q=80', '7-in-1 USB-C hub. 4K HDMI, 100W PD, SD card reader.', 7),
('Power Bank 20000', 39.00, 'https://images.unsplash.com/photo-1591370874773-6702e8f12fd8?w=400&q=80', '20000mAh power bank. 65W fast charging, 3 ports.', 7),
('Dell 27" 4K Monitor', 599.00, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80', '27" 4K IPS monitor. USB-C, 99% sRGB.', 8),
('Samsung Smart TV 55"', 799.00, 'https://images.unsplash.com/photo-1593359677879-a4bb92f4834c?w=400&q=80', '55" 4K QLED Smart TV. Quantum HDR, Tizen OS.', 8),
('LG Curved 34"', 699.00, 'https://images.unsplash.com/photo-1616763355548-1b606f439f86?w=400&q=80', '34" ultrawide curved monitor. 21:9, 144Hz.', 8);

-- Default admin user (password: admin123)
INSERT INTO users (nom, email, password, role) VALUES
('Admin', 'admin@hacktheshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');