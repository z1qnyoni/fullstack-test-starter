<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

$data = json_decode(file_get_contents(__DIR__ . '/../data/data.json'), true);
if (!$data || !isset($data['products'])) {
    die("Ошибка чтения JSON или отсутствует ключ 'products'");
}


$pdo->exec("DROP TABLE IF EXISTS products");
$pdo->exec("
    CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sku VARCHAR(255),
        name VARCHAR(255),
        type VARCHAR(50),
        price DECIMAL(10,2),
        in_stock BOOLEAN
    )
");

foreach ($data['products'] as $item) {
    $stmt = $pdo->prepare("
        INSERT INTO products (sku, name, type, price, in_stock)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
    $item['id'] ?? '',
    $item['name'] ?? '',
    $item['type'] ?? '',
    $item['prices'][0]['amount'] ?? 0,
    isset($item['inStock']) ? (int) $item['inStock'] : 1,
]);
}

// Таблица заказов
$pdo->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        data JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

echo "✅ Импорт завершён!\n";
