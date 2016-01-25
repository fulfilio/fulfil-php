<?php
require('config.php');
/**
 * This is example for creating product with attributes
 */

// Create Product Attribute
$ProductAttribute = new Model('product.attribute');

$productAttr = $ProductAttribute->create([[
    "name" => "Test Attribute 1",
    "type_" => "selection",
    "display_name" => "Test Attr 1",
    "selection" => [
        ["create", [
            [
                "name" => "value1"
            ], [
                "name" => "value2"
            ]
        ]]
    ]
]])[0];
$productAttr = $ProductAttribute->get($productAttr['id']);
print_r($productAttr);

// Create Attribute Set
$AttributeSet = new Model('product.attribute.set');
$attributeSetData = [
    [
        "name" => "Test Attribute Set",
        "attributes" => [
            ['add', [$productAttr['id']]],  // Use id to map already linked attribute
            ['create', [[                   // or use create to create here itself.
                "name" => "Test Attribute 2",
                "type_" => "selection",
                "display_name" => "Test Attr 2",
                "selection" => [
                    ["create", [
                        [
                            "name" => "value1"
                        ],
                        [
                            "name" => "value2"
                        ]
                    ]]
                ]
            ]]]
        ]
    ]
];

$attributeSet = $AttributeSet->create($attributeSetData)[0];

// Create Product Template
$ProductTemplate = new Model('product.template');

$productTemplateData = [[
    "name" => "Test Product Template",
    "account_category" => true,
    "attribute_set" => $attributeSet['id']
]];
$template = $ProductTemplate->create($productTemplateData)[0];
print_r($template);


// Create Product
$Product = new Model('product.product');

$productData = [[
    "template" => $template['id'],
    "code" => "SKU-1",
    "list_price" => new DecimalType(2.0),
    "cost_price" => new DecimalType(1.0),
    "attributes" => [
        ['create', [[
            "attribute" => $productAttr['id'],
            "value_selection" => $productAttr['selection'][0]  // Adding 1st selection value
        ]]]
    ]
]];

print_r($productData);

$product = $Product->create($productData)[0];
print_r($product);
