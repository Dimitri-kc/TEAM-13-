
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
    password VARCHAR (255) NOT NULL, -- increased value for hashed password storage
    phone VARCHAR (20), --increased length for scalability (international numbers)
    address VARCHAR (255), --inclreased value for addrresses for flexibility (match with payments table) 
    role ENUM('customer', 'admin') DEFAULT 'customer' --ENUM for fixed roles
);

--Basket Table--

--basket(basket_ID, user_ID)
CREATE TABLE basket (
    --primary key, unique identifier for basket
    basket_ID INT AUTO_INCREMENT PRIMARY KEY, --auto increment to create unique IDs means no manual input needed
    user_ID INT UNIQUE NOT NULL, --unique constraint to ensure one basket per user
    --fk linking basket to users table
    FOREIGN KEY (user_ID) REFERENCES users(user_ID) ON DELETE CASCADE -- if user is deleted, their basket is also deleted
);

--Basket Items Table--

--basket_items(basket_item_ID, basket_ID, product_ID, quantity)
CREATE TABLE basket_items (
    --primary key, unique identifier for basket items
    basket_item_ID INT AUTO_INCREMENT PRIMARY KEY, --no manual input needed
    basket_ID INT NOT NULL, --foreign key
    product_ID INT NOT NULL, --foreign key
    quantity INT NOT NULL CHECK (quantity > 0), --constraint 
    --fk linking basket_items to basket table
    FOREIGN KEY (basket_ID) REFERENCES basket(basket_ID) ON DELETE CASCADE, -- if basket is deleted, items also deleted
    FOREIGN KEY (product_ID) REFERENCES products(product_ID) --fk linking basket_items to products table
);

--Payments/Checkouts Table--

--payments(payment_ID, order_ID, ...)
CREATE TABLE payments (
    --primary key, unique identifier for payments
    payment_ID INT AUTO_INCREMENT PRIMARY KEY, --no manual input needed
    order_ID INT UNIQUE NOT NULL, --unique constraint to ensure one payment per order
    user_ID INT NOT NULL, --foreign key
    address VARCHAR(255) NOT NULL, --shipping address
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, --auto timestamp for payment date
    total_sum DECIMAL(10, 2) NOT NULL, --total amount paid
    FOREIGN KEY (order_ID) REFERENCES orders(order_ID), --fk linking payments to orders table
    FOREIGN KEY (user_ID) REFERENCES users(user_ID) ON DELETE CASCADE -- if user is deleted, their payments are also deleted
);

--NOTES:--
--Increased password field length for hashes - security.
--ENUM used for user roles to restrict values.
--ON DELETE CASCADE so no lone records.
--Unique constraints for 1-1 relationships.
--May need to include additional fields in payments table (e.g., payment method) but sticking to ERD for now.
--Also possibly increase phone field length for scalability .e.g., international numbers?
