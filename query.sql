-- Dealers table
CREATE TABLE dealers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL
);

-- Inventory table
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    model VARCHAR(100) NOT NULL,
    make VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- Customers table
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);

-- Sales table
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dealer_id INT,
    customer_id INT,
    inventory_id INT,
    sale_date DATE,
    FOREIGN KEY (dealer_id) REFERENCES dealers(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (inventory_id) REFERENCES inventory(id)
);
