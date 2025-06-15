<?php

namespace App\Models;

use PDO;
use PDOException;

class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        try {
            $this->db = new PDO(
                'mysql:host=localhost;dbname=scandiweb_test;charset=utf8',
                'root',
                '1234'
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \RuntimeException("DB connection failed: " . $e->getMessage());
        }
    }

    public function save(string $orderId, array $items, float $total): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO orders (data, items, total) 
            VALUES (:data, :items, :total)
        ");

        $data = json_encode([
            'orderId' => $orderId,
            'items' => $items,
            'total' => $total
        ]);

        return $stmt->execute([
            ':data' => $data,
            ':items' => json_encode($items),
            ':total' => $total
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT id, total, items, created_at 
            FROM orders 
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
