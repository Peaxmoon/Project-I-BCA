

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
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

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
    status ENUM('pending', 'completed', 'canceled') DEFAULT 'pending',
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
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,  -- Unique ID for each admin
    `email` VARCHAR(255) NOT NULL UNIQUE,     -- Email field (unique to each admin)
    `password` VARCHAR(255) NOT NULL,         -- Password field (hashed)
    `name` VARCHAR(100) NOT NULL,             -- Name field (admin's name)
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp when the admin is created
);
-- Create revenue_logs table
CREATE TABLE revenue_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
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

-- Create categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Appetizers', 'Start your meal with these delicious starters'),
('Main Course', 'Hearty and filling main dishes'),
('Desserts', 'Sweet treats to end your meal'),
('Beverages', 'Refreshing drinks and beverages'),
('Soups', 'Hot and comforting soups'),
('Salads', 'Fresh and healthy salads');

-- Add category_id to menu_items if not exists
ALTER TABLE menu_items
ADD COLUMN category_id INT,
ADD FOREIGN KEY (category_id) REFERENCES categories(id);

-- Update existing menu items with categories
UPDATE menu_items SET category_id = 1 WHERE name LIKE '%appetizer%' OR name LIKE '%starter%';
UPDATE menu_items SET category_id = 2 WHERE name LIKE '%main%' OR name LIKE '%curry%' OR name LIKE '%rice%';
UPDATE menu_items SET category_id = 3 WHERE name LIKE '%dessert%' OR name LIKE '%sweet%';
UPDATE menu_items SET category_id = 4 WHERE name LIKE '%drink%' OR name LIKE '%beverage%';
UPDATE menu_items SET category_id = 5 WHERE name LIKE '%soup%';
UPDATE menu_items SET category_id = 6 WHERE name LIKE '%salad%';

-- Set default category for any remaining items
UPDATE menu_items SET category_id = 2 WHERE category_id IS NULL;




-- Foreign key constaraints
ALTER TABLE orders
ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id),
ADD CONSTRAINT fk_table_id FOREIGN KEY (table_id) REFERENCES tables(id);

ALTER TABLE order_items
ADD CONSTRAINT fk_order_id FOREIGN KEY (order_id) REFERENCES orders(id),
ADD CONSTRAINT fk_menu_item_id FOREIGN KEY (menu_item_id) REFERENCES menu_items(id);

-- Rules on delete or update
ALTER TABLE orders
ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- create index to foreign key for faster queries
CREATE INDEX idx_user_id ON orders(user_id);
CREATE INDEX idx_table_id ON orders(table_id);
CREATE INDEX idx_order_id ON order_items(order_id);
CREATE INDEX idx_menu_item_id ON order_items(menu_item_id);
