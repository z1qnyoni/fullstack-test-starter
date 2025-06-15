<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$pdo = new PDO(
    "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$pdo->exec("DROP TABLE IF EXISTS products");
$pdo->exec("
    CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sku VARCHAR(255),
        name VARCHAR(255),
        type VARCHAR(50),
        price DECIMAL(10,2),
        in_stock BOOLEAN,
        weight DECIMAL(10,2) DEFAULT NULL,
        size DECIMAL(10,2) DEFAULT NULL,
        height DECIMAL(10,2) DEFAULT NULL,
        width DECIMAL(10,2) DEFAULT NULL,
        length DECIMAL(10,2) DEFAULT NULL
    )
");

// Таблица orders
$pdo->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        data JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

echo "✅ migration done!\n";
