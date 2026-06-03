CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(40),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO contacts (name, email, phone) VALUES
('Ana Perez', 'ana@example.com', '+56 9 1111 1111'),
('Carlos Soto', 'carlos@example.com', '+56 9 2222 2222');
