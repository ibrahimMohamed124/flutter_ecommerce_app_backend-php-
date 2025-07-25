create database if not  exists smart_locker;
use smart_locker;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    f_name VARCHAR(50) NOT NULL,
    l_name VARCHAR(50) NOT NULL,
    user_name VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'courier', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE users ADD COLUMN image VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN gender varchar(10) not null;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  image VARCHAR(255) NOT NULL
);

CREATE TABLE tabs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);



CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    price DECIMAL(10, 2),
    discount INT DEFAULT 0,
    category_id INT,
    is_popular BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

ALTER TABLE products
ADD COLUMN discount_price DECIMAL(10,2) AFTER price,
ADD COLUMN in_stock BOOLEAN DEFAULT 1 AFTER discount_price,
ADD COLUMN rating DECIMAL(2,1) DEFAULT 0 AFTER in_stock,
ADD COLUMN reviews_count INT DEFAULT 0 AFTER rating,
ADD COLUMN colors TEXT AFTER reviews_count,
ADD COLUMN sizes TEXT AFTER colors,
ADD COLUMN images TEXT AFTER sizes;

ALTER TABLE products
ADD COLUMN icon VARCHAR(255) AFTER image;


-- CREATE TABLE brands (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     name VARCHAR(100) NOT NULL,
--     image TEXT NOT NULL,
--     products_count INT DEFAULT 0,
--     category_id INT NOT NULL,
--     FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
-- );
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image TEXT NOT NULL,
    products_count INT DEFAULT 0
);

ALTER TABLE brands ADD COLUMN tab_id INT;

SET SQL_SAFE_UPDATES = 0;

-- ربط البراندات بالتابات يدويًا (كمثال)
UPDATE brands SET tab_id = 1 WHERE name = 'Nike';        -- مرتبط بـ Sports
UPDATE brands SET tab_id = 4 WHERE name = 'Adidas';      -- مرتبط بـ Clothes
UPDATE brands SET tab_id = 3 WHERE name = 'Apple';       -- مرتبط بـ Electronics

CREATE TABLE drawers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drawer_number INT UNIQUE NOT NULL,
    status ENUM('available', 'reserved', 'open') DEFAULT 'available'
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'ready', 'collected', 'cancelled') DEFAULT 'pending',
    drawer_number INT,
    qr_token VARCHAR(100) UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (drawer_number) REFERENCES drawers(drawer_number)
);



INSERT INTO categories (name, image) VALUES
('Electronics', 'https://i.postimg.cc/sX6shY9g/icons8-electronics-50.png'),
('Smart Phones', 'https://i.postimg.cc/RC2BfQnN/icons8-smartphone-50.png'),
('Clothes', 'https://i.postimg.cc/KY8VCRg6/icons8-clothes-50.png'),
('Perfumes', 'https://i.postimg.cc/wj7jHTJm/icons8-perfume-50.png'),
('Jewel', 'https://i.postimg.cc/x8NDNW16/icons8-jewel-50.png'),
('Shoes', 'https://i.postimg.cc/y8qb6L3y/icons8-pair-of-sneakers-50.png'),
('Watches','https://i.postimg.cc/nrhtk0GD/icons8-watches-back-view-50.png');


INSERT INTO products (
  name, brand, price, discount_price, in_stock, rating, reviews_count,
  colors, sizes, images, image, discount, is_popular, icon
) VALUES
(
  'iphone 8 pro max', 'Apple', 15000.00, 13500.00, 1, 4.5, 120,
  'Black,Silver,Gold', '64GB,128GB', 'https://i.postimg.cc/PJYXJSXF/iphone8-mobile.png,https://i.postimg.cc/PJYXJSXF/iphone8-mobile.png,https://i.postimg.cc/PJYXJSXF/iphone8-mobile.png',
  'https://i.postimg.cc/PJYXJSXF/iphone8-mobile.png', 10, 1,
  'https://i.postimg.cc/bY0nqQR6/icons8-apple-50.png'
),
(
  'Nike air max', 'Nike', 149.99, 104.99, 1, 4.7, 230,
  'Red,Black,White', '40,41,42,43,44', 'https://i.postimg.cc/0yNTHtgR/Nike-Air-Max.png',
  'https://i.postimg.cc/0yNTHtgR/Nike-Air-Max.png', 30, 1,
  'https://i.postimg.cc/4xhQPpvd/icons8-nike-50.png'
),
(
  'acer gaming laptop', 'acer', 25999.00, 24699.00, 1, 4.2, 85,
  'Black', '16GB RAM,512GB SSD', 'https://i.postimg.cc/tJ5cB624/acer-laptop-2.png',
  'https://i.postimg.cc/tJ5cB624/acer-laptop-2.png', 5, 1,
  'https://i.postimg.cc/zBD70Qj5/icons8-acer-50.png'
);


-- INSERT INTO brands (name, image, products_count, category_id) VALUES
-- ('Nike', 'https://example.com/images/nike.png', 25, 6),     -- Shoes
-- ('Adidas', 'https://example.com/images/adidas.png', 30, 3), -- Clothes
-- ('Apple', 'https://example.com/images/apple.png', 15, 1);   -- Electronics

INSERT INTO brands (name, image, products_count, tab_id) VALUES
('Nike', 'https://i.postimg.cc/J08xck18/nike.png', 25, 1),
('Adidas', 'https://example.com/images/adidas.png', 30, 4),
('Apple', 'https://example.com/images/apple.png', 15, 3);




INSERT INTO tabs (name) VALUES 
('Sports'),
('Furniture'),
('Electronics'),
('Clothes'),
('Cosmetics');


