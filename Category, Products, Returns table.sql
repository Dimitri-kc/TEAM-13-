--Categories Table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

--Products Table
CREATE TABLE products (

--PRIMARY KEY, a unique identifier for each product
    product_id INT AUTO_INCREMENT PRIMARY KEY,
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
--Creates a link to the category
   FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

--Returns Table 
CREATE TABLE returns(
--Primary KEY, a unique identifier for each return request
   return_id INT AUTO_INCREMENT PRIMARY KEY,
--The specific item id number from the order being returned, also links to the order_items table
   order_ID INT NOT NULL,
--Shows user id that created return, links to user table
   user_id INT NOT NULL,
--Optional text to explain reason  for return 
   reason TEXT,
--Using ENUM for status changes instead of VARCHAR
  status ENUM('Requested', 'Authorised', 'Received', 'Refunded', 'Denied')
--Creates a time stamp for when the return request was put through 
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--Creates link to orders table and defines relationship
  FOREIGN KEY (order_ID) REFERENCES orders(order_ID) ON DELETE CASCADE,
--Creates link to users table and defines relationship
  FOREIGN KEY (user_id) REFERENCES users(users_ID) ON DELETE CASCADE
);

--Return_items Table: Tracks the specific products included in the return requests
CREATE TABLE return_items(
--Primary KEY, a unique identifier for each return item
   return_item_ID INT AUTO_INCREMENT PRIMARY KEY,
   return_id INT NOT NULL,
   order_item_id INT NOT NULL
--The specific quantity of this specific item being returned
   quantity INT NOT NULL CHECK(quantity > 0),
--Optional text to explain why this specific item is being returned
   reason VARCHAR(255),
   FOREIGN KEY (return_id) REFERENCES returns(return_id) ON DELETE CASCADE,
   FOREIGN KEY (order_item_id) REFERENCES order_items(order_item_id)
);