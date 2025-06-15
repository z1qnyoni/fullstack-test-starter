<?php
namespace Models\Product;

class DVD extends Product {
    private float $size;

    public function __construct(string $sku, string $name, float $price, float $size) {
        parent::__construct($sku, $name, $price);
        $this->size = $size;
    }

    public function getType(): string {
        return 'DVD';
    }

    public function getAttributes(): array {
        return ['size' => $this->size];
    }
}
