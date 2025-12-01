 --REVIEWS TABLE

CREATE TABLE reviews (
    review_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT NOT NULL,-- User who wrote the review
    product_ID INT NOT NULL, --Product being reviewed
    rating INT, -- Removed the inline CHECK constraint
    comment TEXT,-- Customer’s review text
    review_date DATETIME DEFAULT CURRENT_TIMESTAMP,  -- When review was created
    FOREIGN KEY (product_ID) REFERENCES products(product_ID), -- Link to products
    FOREIGN KEY (user_ID) REFERENCES users(user_ID), -- Link to users
    CONSTRAINT CHK_Rating CHECK (rating BETWEEN 1 AND 5) -- Defined the CHECK constraint separately
);

--ORDERS TABLE

CREATE TABLE orders (
    order_ID INT AUTO_INCREMENT PRIMARY KEY,  -- Unique order identifier
    user_ID INT NOT NULL,                     -- User who placed order
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP, -- Time placed
    total_price DECIMAL(10,2) DEFAULT 0.00,   -- Order total
    order_status VARCHAR(50) DEFAULT 'Pending', -- Order status
    address VARCHAR(255),                     -- Delivery address
    FOREIGN KEY (user_ID) REFERENCES users(user_ID) -- Link to users
);

 --ORDER ITEMS TABLE

CREATE TABLE order_items (
    order_item_ID INT AUTO_INCREMENT PRIMARY KEY, -- Unique order item ID
    product_ID INT NOT NULL,                     -- FK -> products
    unit_price DECIMAL(10,2) NOT NULL,           -- Price when purchased
    order_ID INT NOT NULL,                       -- FK -> orders
    FOREIGN KEY (order_ID) REFERENCES orders(order_ID),     -- Link to orders
    FOREIGN KEY (product_ID) REFERENCES products(product_ID)  -- Link to products
);