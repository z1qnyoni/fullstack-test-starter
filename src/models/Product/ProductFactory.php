<?php
namespace Models\Product;

class ProductFactory {
    public static function create(array $data): Product {
        $sku = $data['sku'];
        $name = $data['name'];
        $price = $data['price'];
        $type = strtolower($data['type']);

        return match ($type) {
            'book' => new Book($sku, $name, $price, $data['weight']),
            'dvd' => new DVD($sku, $name, $price, $data['size']),
            'furniture' => new Furniture($sku, $name, $price, $data['height'], $data['width'], $data['length']),
            default => throw new \Exception("Unknown product type: $type"),
        };
    }
}
