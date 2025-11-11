CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

--Products Table
CREATE TABLE products (
--PRIMARY KEY, a unique identifier for each product
    id INT AUTO_INCREMENT PRIMARY KEY,
--Required field, max 100 characters for the name of products
    name VARCHAR(100) NOT NULL,
--Products description, optional, has unlimited length
   description TEXT,
--Products price, 8 and 2 representing the amount of digits allowed before and after the decimal point
   price DECIMAL(8,2) NOT NULL,
--Stock quantity levels, set at 0 unless specified
   stock INT DEFAULT 0,
--Image file path
   image VARCHAR(255),
--Each product has an id that puts it with a category, required as all products will belong in a category
   category_id INT NOT NULL,
)