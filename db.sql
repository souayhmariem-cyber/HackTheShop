-- ============================================================
--  HackTheShop — Full Database Setup
--  Educational Cybersecurity Project
--  Import via: phpMyAdmin > Import, or: mysql -u root -p < hacktheshop.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Create & select database
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS hacktheshop
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE hacktheshop;

-- ============================================================
--  TABLE: users
--  Vulnerabilities: plain-text passwords, secret_note field
--  (sensitive data exposure), role escalation demo
-- ============================================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nom         VARCHAR(100)  NOT NULL,
  email       VARCHAR(150)  NOT NULL UNIQUE,
  password    VARCHAR(255)  NOT NULL,          -- plain text — intentional for SQLi demo
  role        ENUM('user','admin') DEFAULT 'user',
  secret_note TEXT,                             -- sensitive data exposure demo
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed users (passwords stored in plain text — educational demo only)
INSERT INTO users (nom, email, password, role, secret_note) VALUES
('Alice Martin',  'alice@hacktheshop.com',  'password123',   'user',  'My pet name is Fluffy. Secret answer: blue.'),
('Bob Dupont',    'bob@hacktheshop.com',    'bob2024',        'user',  'Backup email: bob.backup@gmail.com'),
('Carol Smith',   'carol@hacktheshop.com',  'carol!pass',     'user',  'Card PIN reminder: 19XX — first car year'),
('David Lee',     'david@hacktheshop.com',  'david1234',      'user',  'SSH key passphrase: sunshine99'),
('Admin User',    'admin@hacktheshop.com',  'L4bS3cr3t!2025', 'admin', 'DB root pass: (empty). Admin token: HS_DEBUG_2025');

-- ============================================================
--  TABLE: products
-- ============================================================
DROP TABLE IF EXISTS products;
CREATE TABLE products (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nom         VARCHAR(200)   NOT NULL,
  prix        DECIMAL(10,2)  NOT NULL,
  description TEXT,
  categorie   VARCHAR(50),
  image       VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO products (nom, prix, description, categorie, image) VALUES
('Laptop Pro X',        799.00,  'High-performance laptop for professionals.',          'pc',        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80'),
('Gaming Laptop Z',    1299.00,  'RTX 4070, 165Hz display, RGB keyboard.',              'pc',        'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400&q=80'),
('MacBook Air M2',      999.00,  'Apple silicon, all-day battery.',                     'pc',        'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&q=80'),
('Smartphone Z12',      499.00,  '6.7" AMOLED, 200MP camera, 5G.',                     'phone',     'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&q=80'),
('iPhone 15 Pro',      1099.00,  'Titanium frame, A17 Pro chip.',                      'phone',     'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&q=80'),
('Samsung S24',         899.00,  'Galaxy AI, 50MP triple camera.',                     'phone',     'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&q=80'),
('AirPods Pro 2',       249.00,  'Active noise cancellation, spatial audio.',          'audio',     'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&q=80'),
('Sony WH-1000XM5',     349.00,  'Industry-leading noise cancellation headphones.',    'audio',     'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80'),
('JBL Charge 5',        179.00,  'Waterproof Bluetooth speaker, 20h battery.',         'audio',     'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&q=80'),
('Apple Watch S9',      399.00,  'Always-on display, crash detection.',                'wearable',  'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80'),
('Galaxy Watch 6',      299.00,  'Health monitoring, 3-day battery.',                  'wearable',  'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=400&q=80'),
('PlayStation 5',       499.00,  '4K gaming, DualSense haptics.',                      'gaming',    'https://images.unsplash.com/photo-1593118247619-e2d6f056869e?w=400&q=80'),
('Nintendo Switch',     299.00,  'Play at home or on the go.',                         'gaming',    'https://images.unsplash.com/photo-1617096200347-cb04ae810b1d?w=400&q=80'),
('Dell 27" 4K Monitor', 599.00,  'IPS panel, USB-C, 60Hz.',                           'screen',    'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80'),
('Gaming Monitor 144Hz',349.00,  '1ms response time, G-Sync compatible.',              'screen',    'https://images.unsplash.com/photo-1598986646512-9330bcc4c0dc?w=400&q=80'),
('Logitech MX Master',   99.00,  'Ergonomic wireless mouse, multi-device.',            'accessory', 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80'),
('USB-C Hub Pro',        49.00,  '7-in-1 hub: HDMI, USB-A, SD card.',                 'accessory', 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&q=80'),
('Sony Alpha A7',      2499.00,  'Full-frame mirrorless, 12fps burst.',                'camera',    'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80'),
('GoPro Hero 12',       399.00,  '5.3K video, HyperSmooth 6.0.',                      'camera',    'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=400&q=80'),
('Power Bank 20000',     39.00,  '20000mAh, 65W fast charge, 3 ports.',               'accessory', 'https://images.unsplash.com/photo-1591370874773-6702e8f12fd8?w=400&q=80');

-- ============================================================
--  TABLE: orders
--  Vulnerabilities: IDOR (no ownership check in order.php),
--  card_last4 exposed in API response
-- ============================================================
DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT           NOT NULL,
  product_id  INT,
  quantity    INT           DEFAULT 1,
  total       DECIMAL(10,2) NOT NULL,
  status      VARCHAR(50)   DEFAULT 'pending',
  card_last4  VARCHAR(4),                       -- sensitive data — IDOR demo exposes this
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders spread across multiple users (students enumerate id=1,2,3...)
INSERT INTO orders (user_id, product_id, quantity, total, status, card_last4) VALUES
(1, 1,  1, 799.00,  'delivered',  '4242'),  -- Alice's order  — id=1
(2, 4,  1, 499.00,  'shipping',   '5353'),  -- Bob's order    — id=2
(3, 7,  2, 498.00,  'delivered',  '1111'),  -- Carol's order  — id=3
(1, 12, 1, 499.00,  'pending',    '4242'),  -- Alice again    — id=4
(4, 14, 1, 599.00,  'cancelled',  '9876'),  -- David's order  — id=5
(2, 10, 1, 399.00,  'delivered',  '5353'),  -- Bob again      — id=6
(3, 16, 3, 297.00,  'shipping',   '1111'),  -- Carol again    — id=7
(5, 1,  1, 799.00,  'delivered',  '0000'),  -- Admin's order  — id=8
(4, 5,  1, 1099.00, 'pending',    '9876'),  -- David again    — id=9
(1, 8,  1, 349.00,  'shipping',   '4242');  -- Alice again    — id=10

-- ============================================================
--  TABLE: comments
--  Vulnerability: contenu stored raw (no sanitization)
--  Stored XSS demo — scripts saved here execute for all visitors
-- ============================================================
DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  product_id  INT           NOT NULL DEFAULT 1,
  user_id     INT,
  auteur      VARCHAR(100)  NOT NULL,
  contenu     TEXT          NOT NULL,           -- NOT sanitized — intentional
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Safe seed comments (students will add malicious ones via the form)
INSERT INTO comments (product_id, user_id, auteur, contenu) VALUES
(1, 1, 'Alice M.',   'Great laptop, super fast delivery!'),
(1, 2, 'Bob D.',     'Excellent build quality, worth every penny.'),
(4, 3, 'Carol S.',   'Best smartphone I have ever owned.'),
(7, 4, 'David L.',   'Amazing sound quality on these AirPods.'),
(12, 1, 'Alice M.',  'PS5 arrived perfectly packaged. Very happy!');

-- ============================================================
--  TABLE: lab_progress
--  Tracks which attacks each session has completed
-- ============================================================
DROP TABLE IF EXISTS lab_progress;
CREATE TABLE lab_progress (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  session_id   VARCHAR(255) NOT NULL,
  attack_name  VARCHAR(100) NOT NULL,
  completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_progress (session_id, attack_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  TABLE: debug_logs
--  Fake sensitive logs for discovery challenge
-- ============================================================
DROP TABLE IF EXISTS debug_logs;
CREATE TABLE debug_logs (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  log_text   TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO debug_logs (log_text) VALUES
('[DEBUG] DB connect: host=localhost db=hacktheshop user=root pass=(empty)'),
('[DEBUG] Admin login attempt from 192.168.1.42 — SUCCESS'),
('[DEBUG] JWT secret key: HS_JWT_SECRET_2025'),
('[DEBUG] Cron job ran: cleanup_sessions.php — 14 sessions purged'),
('[DEBUG] Failed upload blocked: shell.php.jpg from 10.0.0.7'),
('[INFO]  New user registered: eve@hacktheshop.com'),
('[WARN]  SQL error logged — suspicious input in search field');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  QUICK REFERENCE
-- ============================================================
-- Users you can log in as:
--   alice@hacktheshop.com  / password123
--   bob@hacktheshop.com    / bob2024
--   carol@hacktheshop.com  / carol!pass
--   admin@hacktheshop.com  / L4bS3cr3t!2025   (role: admin)
--
-- SQLi bypass payload (login form):
--   Email:    ' OR '1'='1'--
--   Password: anything
--
-- IDOR: visit order.php?id=1 through ?id=10 to see all users' orders
-- Stored XSS: post this in the comment box:
--   <img src=x onerror="alert('Stored XSS!')">
-- ============================================================