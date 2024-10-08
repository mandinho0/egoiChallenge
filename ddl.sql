CREATE DATABASE IF NOT EXISTS laminas;
USE laminas;

CREATE TABLE IF NOT EXISTS recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(15)
);

INSERT INTO recipients (name, email, phone_number)
VALUES ('Teste 123', 'teste123@example.com', '+351910000000');
