

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

-- for order admins
CREATE TABLE `admins` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,  -- Unique ID for each admin
    `email` VARCHAR(255) NOT NULL UNIQUE,     -- Email field (unique to each admin)
    `password` VARCHAR(255) NOT NULL,         -- Password field (hashed)
    `name` VARCHAR(100) NOT NULL,             -- Name field (admin's name)
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp when the admin is created
);


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

-- Test Foreign key behaviour
DELETE FROM users WHERE id = 1; -- Check if related rows in `orders` are also deleted if ON DELETE CASCADE is set.

-- To check foreign keys relation
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    TABLE_SCHEMA = 'tableserve'
    AND REFERENCED_TABLE_NAME IS NOT NULL;



