-- Create database
CREATE DATABASE IF NOT EXISTS food_ordering_system;
USE food_ordering_system;

-- Users table stores both normal users and admins.
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Foods table for menu items managed by admin panel.
CREATE TABLE foods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table keeps checkout summary for each order.
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  address TEXT NOT NULL,
  phone VARCHAR(30) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table keeps products included in an order.
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  food_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE
);

-- Default admin account password hash for: admin123
INSERT INTO users(name, email, password, role) VALUES
('Administrator', 'admin@foodorder.com', '$2y$10$xhB7vkhakIkhWGvYbFlJxeGE9BvCp3J8.gveJf4hPk.N1x7y2hB1G', 'admin');

-- Sample foods for quick testing.
INSERT INTO foods(name, description, price, image) VALUES
('Margherita Pizza', 'Classic pizza with mozzarella and tomato sauce', 8.99, 'https://images.unsplash.com/photo-1604382355076-af4b0eb60143?w=800'),
('Veg Burger', 'Crispy veggie patty with cheese and lettuce', 5.49, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800'),
('Pasta Alfredo', 'Creamy alfredo pasta with herbs', 7.25, 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800');
