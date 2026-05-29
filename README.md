# Online Food Ordering System (PHP + MySQL)

A beginner-friendly Online Food Ordering System built with:
- PHP (using **mysqli** functions only)
- MySQL
- HTML + CSS + Bootstrap
- JavaScript

---

## Features
- User registration/login/logout
- Session-based authentication
- Food menu page
- Add to cart
- Checkout system (orders + order items)
- Admin panel
- Add/Edit/Delete foods
- Responsive design
- Clean and simple folder structure

---

## Project Folder Structure
```text
online-food-ordering/
├── admin/
│   ├── dashboard.php
│   ├── foods.php
│   └── login.php
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── config/
│   └── db.php
├── includes/
│   ├── admin_auth.php
│   ├── auth.php
│   ├── footer.php
│   └── header.php
├── sql/
│   └── database.sql
├── index.php
├── register.php
├── login.php
├── logout.php
├── cart.php
└── checkout.php
```

---

## How to Run on Your System (Step by Step)

### 1) Install required software
You need one of these stacks:
- **XAMPP** (recommended for beginners), or
- WAMP / LAMP / MAMP

Minimum requirements:
- PHP 8+
- MySQL / MariaDB
- Apache (or any web server that can run PHP)

---

### 2) Copy project to web root
If using XAMPP:
- Windows: copy folder to `C:\xampp\htdocs\`
- Linux: copy folder to `/opt/lampp/htdocs/`

Example final path:
```text
C:\xampp\htdocs\online-food-ordering
```

---

### 3) Create database and tables
1. Start **Apache** and **MySQL** from XAMPP Control Panel.
2. Open `http://localhost/phpmyadmin`.
3. Create a new database named:
   - `food_ordering_system`
4. Import this file:
   - `sql/database.sql`

#### Where is `sql/database.sql` and how do I import it?
`sql/database.sql` is inside this project folder. For example, if your project is here:
```text
C:\xampp\htdocs\online-food-ordering
```
then the SQL file is here:
```text
C:\xampp\htdocs\online-food-ordering\sql\database.sql
```

To import it in phpMyAdmin:
1. Open `http://localhost/phpmyadmin`.
2. Click the database `food_ordering_system` from the left sidebar.
3. Click the **Import** tab at the top.
4. Click **Choose File** / **Browse**.
5. Select `database.sql` from the project's `sql` folder.
6. Click **Import** / **Go** at the bottom.

This SQL file automatically creates all required tables and inserts:
- default admin account
- sample food items

---

### 4) Configure database connection
Open:
- `config/db.php`

Set these values according to your MySQL setup:
```php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'food_ordering_system';
```

> For default XAMPP, these values usually work as-is.

---

### 5) Run the project
Open browser and go to:
```text
http://localhost/online-food-ordering/
```

You should now see the Food Menu page.

---

## Default Admin Login
Use this account for admin panel access:
- **Email:** `admin@foodorder.com`
- **Password:** `admin123`

Admin panel URL:
```text
http://localhost/online-food-ordering/login.php
```
(Login detects admin role and redirects to dashboard.)

---

## User Flow (Quick Test)
1. Register a new user
2. Login as user
3. Add foods to cart
4. Go to cart and checkout
5. Logout
6. Login as admin and manage foods

---

## Common Errors and Fixes

### Error: `Database connection failed`
- Check MySQL is running.
- Check credentials in `config/db.php`.
- Ensure database name is exactly `food_ordering_system`.

### Error: `Table doesn't exist`
- You likely did not import `sql/database.sql`.
- Re-import the SQL file in phpMyAdmin.

### Login not working
- Ensure passwords are not manually edited in DB.
- Use default admin credentials above.

---

## Notes
- This project intentionally uses simple beginner-friendly code.
- All database operations use **mysqli** only.
- Important code sections include comments to help learning.
