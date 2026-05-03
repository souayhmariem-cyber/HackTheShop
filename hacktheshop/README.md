# HackTheShop — Gamified Tech Ecommerce Platform

A full-stack gamified ecommerce platform where users complete SQL coding challenges to unlock product categories and win rewards.

---

## Stack
- **Frontend**: HTML, Tailwind CSS (CDN), Vanilla JS
- **Backend**: PHP 8+, PDO
- **Database**: MySQL / MariaDB

---

## Setup Instructions

### 1. Requirements
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10+
- A local server: XAMPP, WAMP, Laragon, or MAMP

### 2. Import the Database
```bash
mysql -u root -p < sql/hacktheshop.sql
```
Or open **phpMyAdmin** → Import → select `sql/hacktheshop.sql`

### 3. Configure the Database
Edit `php/db.php` and update your credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password
define('DB_NAME', 'hacktheshop');
```

### 4. Place files in your web root
- XAMPP: `C:/xampp/htdocs/hacktheshop/`
- Laragon: `C:/laragon/www/hacktheshop/`

### 5. Open in browser
```
http://localhost/hacktheshop/
```

---

## Default Admin Account
| Field    | Value                    |
|----------|--------------------------|
| Username | `admin`                  |
| Password | `admin123`               |
| URL      | `/hacktheshop/admin/`    |

> ⚠️ Change the admin password after first login via phpMyAdmin.

---

## Project Structure
```
hacktheshop/
├── index.php                        ← Main homepage
├── css/
│   └── style.css                    ← All styles
├── php/
│   ├── db.php                       ← Database connection
│   └── controllers/
│       ├── AuthController.php       ← Login / Register / Logout
│       ├── ProductController.php    ← Products, Cart, Wishlist, Orders, Reviews
│       └── RewardController.php     ← Progress tracking & reward draws
├── chatbot/
│   └── reply.php                    ← Echo AI shopping assistant
├── admin/
│   └── dashboard.php                ← Full admin panel
└── sql/
    └── hacktheshop.sql              ← Database schema + seed data
```

---

## Key Features

### Gamification Flow
1. User selects a category (Laptops, Gaming, etc.)
2. Clicks **"Unlock Challenge"** → redirected to HackerRank
3. After solving, returns and clicks **"I solved it"**
4. Progress is saved to the database
5. User clicks **"Claim Loot Spin"** → animated wheel spins
6. A random product from that category is won and saved to their account

### Ecommerce
- Product grid with 8 categories
- Product modal with images, descriptions, reviews
- Persistent cart (DB-backed when logged in)
- Wishlist system
- Full checkout → order saved to DB

### Auth System
- Register / Login / Logout with PHP sessions
- Passwords hashed with bcrypt
- Role-based access: `user` and `admin`

### Admin Panel (`/admin/dashboard.php`)
- User management + role control
- Add / delete products
- Edit HackerRank challenge URLs per category
- View all orders and rewards log

### Chatbot (Echo)
- Shopping assistant with rule-based replies
- Answers questions about products, challenges, cart, wishlist, rewards
- Located bottom-right of every page

---

## Challenge Categories Linked
| Category    | HackerRank Challenge                    |
|-------------|------------------------------------------|
| Laptops     | Basic Select All                         |
| Phones      | Revising the Select Query                |
| Audio       | Weather Observation Station 1            |
| Wearables   | Draw the Triangle 1                      |
| Cameras     | Japanese Cities Attributes               |
| Gaming      | Salary of Employees                      |
| Accessories | Revising the Select Query 2              |
| Screens     | Weather Observation Station 2            |

All challenge URLs can be updated from the Admin Panel.
