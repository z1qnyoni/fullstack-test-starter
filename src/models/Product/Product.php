<?php
namespace Models\Product;

abstract class Product {
    protected string $sku;
    protected string $name;
    protected float $price;

    public function __construct(string $sku, string $name, float $price) {
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
    }

    abstract public function getType(): string;
    abstract public function getAttributes(): array;

    public function getBaseData(): array {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'type' => $this->getType(),
            'attributes' => $this->getAttributes(),
        ];
    }
}
