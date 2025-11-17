
--
CREATE DATABASE loft_and_living_db;
USE loft_and_living_db;

--Users Table--

--user(user_ID, name, email, password, phone, address, role)--
CREATE TABLE users (
    --primary key - unique identifier for users
    user_ID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR (50) NOT NULL,
    email VARCHAR (100) UNIQUE NOT NULL,
    password NOT NULL, --needs parameter hashed...
    phone VARCHAR (11),
    address VARCHAR (200),
    role ENUM('customer', 'admin') DEFAULT 'customer' --ENUM for fixed
)

--Basket Table--

--basket(basket_ID, user_ID)
CREATE TABLE basket (
    --primary key, unique identifier for basket
    basket_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT UNIQUE, 
    --foreign key, linking basket table to users table
    FOREIGN KEY user_ID REFERENCES users(identifier)
)

--Basket Items Table--

--basket_items(basket_item_ID, basket_ID, product_ID, quantity)
CREATE TABLE basket_items (
    --primary key, unique identifier for basket items
    basket_item_ID INT AUTO_INCREMENT PRIMARY KEY,
    basket_ID INT,
    product_ID INT,
    quantity INT NOT NULL CHECK (quantity > 0), --constraint 
    --foreign key, linking basket_items table to basket table
    FOREIGN KEY basket_ID REFERENCES basket(identifier),
    --foreign key, linking basket_items table to products table
    FOREIGN KEY product_ID REFERENCES products(identifier)
)

--Payments/Checkouts Table--

--payments(payment_ID, order_ID, ...)
CREATE TABLE payments (
    --primary key, unique identifier for payments
    payment_ID INT AUTO_INCREMENT PRIMARY KEY,    
    --foreign key, linking payments table to orders table
    FOREIGN KEY order_ID REFERENCES orders(identifier)
)