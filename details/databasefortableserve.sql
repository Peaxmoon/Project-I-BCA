-- to create users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Hashed password
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- for menu 
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    -- image and category_id is also in database 
    -- image	varchar(255)	utf8mb4_general_ci	
    -- category_id Index	int(11)
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
-- Update menu_items table to reference menu_categories
ALTER TABLE menu_items
DROP FOREIGN KEY menu_items_ibfk_1,
ADD FOREIGN KEY (category_id) REFERENCES menu_categories(id);


-- Create menu_categories table
CREATE TABLE menu_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert sample categories
INSERT INTO menu_categories (name, description) VALUES
('Appetizers', 'Start your meal with these delicious starters'),
('Main Course', 'Hearty and filling main dishes'),
('Desserts', 'Sweet treats to end your meal'),
('Beverages', 'Refreshing drinks and beverages');




-- for table
CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL UNIQUE,
    location VARCHAR(100), -- Optional description (e.g., 'Main Room', 'Patio')
    status ENUM('occupied', 'available') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB;


-- for order
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    table_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
ALTER TABLE `orders`
ADD COLUMN `payment_status` ENUM('not_paid', 'paid') DEFAULT 'not_paid';


-- for order items
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
ALTER TABLE `order_items`
ADD COLUMN `payment_status` ENUM('not_paid', 'paid') DEFAULT 'not_paid';


-- for order admins
CREATE TABLE `admins` (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,  -- Unique ID for each admin
    email VARCHAR(255) NOT NULL UNIQUE,     -- Email field (unique to each admin)
    password VARCHAR(255) NOT NULL,         -- Password field (hashed)
    name VARCHAR(100) NOT NULL,             -- Name field (admin's name)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp when the admin is created
);
-- Create revenue_logs table
CREATE TABLE revenue_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    payment_ref	varchar(255),
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
CREATE TABLE food_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
);


