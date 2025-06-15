<?php

namespace App\Controller;

use App\Models\OrderRepository;
use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL
{
    public static function handle(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type');
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            exit(0);
        }

        try {
            $dataRaw = json_decode(file_get_contents(__DIR__ . '/../../data/data.json'), true);
            $data = $dataRaw['data'] ?? [];

            $products = $data['products'] ?? [];
            $categories = $data['categories'] ?? [];

            $attributeItemType = new ObjectType([
                'name' => 'AttributeItem',
                'fields' => [
                    'id' => Type::string(),
                    'displayValue' => Type::string(),
                    'value' => Type::string(),
                ],
            ]);

            $attributeType = new ObjectType([
                'name' => 'Attribute',
                'fields' => [
                    'id' => Type::string(),
                    'name' => Type::string(),
                    'type' => Type::string(),
                    'items' => Type::listOf($attributeItemType),
                ],
            ]);

            $priceType = new ObjectType([
                'name' => 'Price',
                'fields' => [
                    'amount' => Type::float(),
                    'currency' => new ObjectType([
                        'name' => 'Currency',
                        'fields' => [
                            'label' => Type::string(),
                            'symbol' => Type::string(),
                        ]
                    ])
                ]
            ]);

            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => Type::string(),
                    'name' => Type::string(),
                    'inStock' => Type::boolean(),
                    'gallery' => Type::listOf(Type::string()),
                    'description' => Type::string(),
                    'category' => Type::string(),
                    'attributes' => Type::listOf($attributeType),
                    'prices' => Type::listOf($priceType),
                    'brand' => Type::string(),
                ],
            ]);

            $categoryType = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'name' => Type::string(),
                ],
            ]);

            $orderType = new ObjectType([
                'name' => 'Order',
                'fields' => [
                    'id' => Type::int(),
                    'items' => Type::string(),
                    'total' => Type::float(),
                    'created_at' => Type::string(),
                ],
            ]);

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => fn () => $categories,
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'args' => [
                            'category' => ['type' => Type::string()],
                        ],
                        'resolve' => fn ($root, $args) => isset($args['category']) && $args['category'] !== 'all'
                            ? array_values(array_filter($products, fn ($p) => $p['category'] === $args['category']))
                            : $products,
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => fn ($root, $args) => array_values(array_filter($products, fn ($p) => $p['id'] === $args['id']))[0] ?? null,
                    ],
                    'orders' => [
                        'type' => Type::listOf($orderType),
                        'resolve' => fn () => (new OrderRepository())->getAll(),
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'createOrder' => [
                        'type' => Type::string(),
                        'args' => [
                            'orderId' => Type::nonNull(Type::string()),
                            'items' => Type::nonNull(Type::string()),
                            'total' => Type::nonNull(Type::float()),
                        ],
                        'resolve' => function ($root, $args) {
                            $repo = new OrderRepository();
                            $items = json_decode($args['items'], true);
                            if (!is_array($items)) {
                                throw new \Exception("Invalid items format");
                            }
                            $success = $repo->save($args['orderId'], $items, $args['total']);
                            if (!$success) {
                                throw new \Exception("Order could not be saved to database.");
                            }
                            return "Order " . $args['orderId'] . " saved to DB";
                        }
                    ]
                ]
            ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'] ?? null;
            $variableValues = $input['variables'] ?? null;

            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
        } catch (Throwable $e) {
            http_response_code(500);
            $output = [
                'errors' => [[
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]]
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: POST, OPTIONS');

        return json_encode($output, JSON_UNESCAPED_UNICODE);
    }
}
