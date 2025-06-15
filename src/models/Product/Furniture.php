<?php
namespace Models\Product;

class Furniture extends Product {
    private float $height;
    private float $width;
    private float $length;

    public function __construct(string $sku, string $name, float $price, float $height, float $width, float $length) {
        parent::__construct($sku, $name, $price);
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
    }

    public function getType(): string {
        return 'Furniture';
    }

    public function getAttributes(): array {
        return [
            'dimensions' => "{$this->height}x{$this->width}x{$this->length}"
        ];
    }
}
