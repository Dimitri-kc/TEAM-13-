
--ORDERS TABLE


CREATE TABLE orders (
    order_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price DECIMAL(10,2) DEFAULT 0.00,
    order_status VARCHAR(50) DEFAULT 'Pending',
    address VARCHAR(255),
    FOREIGN KEY (user_ID) REFERENCES users(id)
);


--ORDER ITEMS TABLE


CREATE TABLE order_items (
    order_item_ID INT AUTO_INCREMENT PRIMARY KEY,
    order_ID INT NOT NULL,
    product_ID INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2),

    FOREIGN KEY (order_ID) REFERENCES orders(order_ID) ON DELETE CASCADE,
    FOREIGN KEY (product_ID) REFERENCES products(product_ID)
);

--REVIEWS TABLE

CREATE TABLE reviews (
    review_ID INT AUTO_INCREMENT PRIMARY KEY,
    product_ID INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    review_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_ID) REFERENCES products(product_ID),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
