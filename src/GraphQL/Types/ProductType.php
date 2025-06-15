<?php
namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType {
    public function __construct() {
        parent::__construct([
            'name' => 'Product',
            'fields' => [
                'sku' => Type::string(),
                'name' => Type::string(),
                'price' => Type::float(),
                'type' => Type::string(),
                'attributes' => [
                    'type' => Type::string(), 
                    'resolve' => fn($root) => json_encode($root['attributes'], JSON_UNESCAPED_UNICODE)
                ]
            ]
        ]);
    }
}