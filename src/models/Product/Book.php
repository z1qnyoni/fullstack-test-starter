<?php
namespace Models\Product;

class Book extends Product {
    private float $weight;

    public function __construct(string $sku, string $name, float $price, float $weight) {
        parent::__construct($sku, $name, $price);
        $this->weight = $weight;
    }

    public function getType(): string {
        return 'Book';
    }

    public function getAttributes(): array {
        return ['weight' => $this->weight];
    }
}
