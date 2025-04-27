<?php

namespace Tests\Feature;

use App\Transformers\OrderTransformer;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderTransformerTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function it_transforms_valid_order_data_correctly()
    {
        $input = [
            'user_id' => 1,
            'name' => 'Test User',
            'street' => 'Test Street',
            'number' => 10,
            'additional_info' => 'Ring the bell',
            'city' => 'Test City',
            'county' => 'Test County',
            'postcode' => 12345,
            'payment_type' => 'credit_card',
            'price' => 199.99,
            'products' => [
                [
                    'id' => 101,
                    'name' => 'Product 1',
                    'price' => 99.99,
                    'quantity' => 2,
                    'image_url' => 'https://example.com/img1.jpg',
                ],
            ],
        ];

        $result = OrderTransformer::transform($input);

        $this->assertEquals(1, $result['user_id']);
        $this->assertEquals('Test User', $result['name']);
        $this->assertEquals('Test Street', $result['street']);
        $this->assertEquals(10, $result['number']);
        $this->assertEquals('Ring the bell', $result['additional_info']);
        $this->assertEquals('Test City', $result['city']);
        $this->assertEquals('Test County', $result['county']);
        $this->assertEquals(12345, $result['postcode']);
        $this->assertEquals('pending', $result['status']->value);
        $this->assertEquals('credit_card', $result['payment_type']);
        $this->assertEquals(199.99, $result['price']);

        $this->assertArrayHasKey('products', $result);
        $this->assertCount(1, $result['products']);
        $this->assertEquals(101, $result['products'][0]['id']);
        $this->assertEquals('Product 1', $result['products'][0]['name']);
        $this->assertEquals(99.99, $result['products'][0]['price']);
        $this->assertEquals(2, $result['products'][0]['quantity']);
        $this->assertEquals('https://example.com/img1.jpg', $result['products'][0]['image_url']);
    }

    #[Test]
    public function it_throws_exception_for_invalid_fields()
    {
        $invalidData = [
            'user_id' => 'sting',
            'name' => 1,
            'street' => 1,
            'number' => 'string',
            'additional_info' => 1,
            'city' => 1,
            'county' => 1,
            'postcode' => 'string',
            'payment_type' => 'invalid_type',
            'price' => 'string',
            'products' => [
                [
                    'id' => 'string',
                    'name' => 1,
                    'price' => 'string',
                    'quantity' => 'string',
                    'image_url' => 1,
                ],
            ],
        ];

        try {
            OrderTransformer::transform($invalidData);
            $this->fail('Expected Exception was not thrown');
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();

            $this->assertStringContainsString('The user ID must be a valid integer.', $errorMessage);
            $this->assertStringContainsString('The name must be a valid string.', $errorMessage);
            $this->assertStringContainsString('The street must be a valid string.', $errorMessage);
            $this->assertStringContainsString('The number must be a valid integer.', $errorMessage);
            $this->assertStringContainsString('Additional info must be a valid string.', $errorMessage);
            $this->assertStringContainsString('The city must be a valid string.', $errorMessage);
            $this->assertStringContainsString('The county must be a valid string.', $errorMessage);
            $this->assertStringContainsString('The postcode field must be an integer.', $errorMessage);
            $this->assertStringContainsString('The selected payment type is invalid.', $errorMessage);
            $this->assertStringContainsString('The price must be a valid numeric.', $errorMessage);
            $this->assertStringContainsString('The product ID must be a valid integer.', $errorMessage);
            $this->assertStringContainsString('The products.0.name field must be a string.', $errorMessage);
            $this->assertStringContainsString('The products.0.price field must be a number.', $errorMessage);
            $this->assertStringContainsString('The products.0.image_url field must be a string.', $errorMessage);
            $this->assertStringContainsString('Product quantity must be an integer.',$errorMessage);

        }

    }

    #[Test]
    public function it_throws_exception_for_missing_fields()
    {
        $missingData = [];

        try{
            OrderTransformer::transform($missingData);
            $this->fail('Expected Exception was not thrown');
        }
        catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->assertStringContainsString('The user ID is required.', $errorMessage);
            $this->assertStringContainsString('The name is required.', $errorMessage);
            $this->assertStringContainsString('The street is required.', $errorMessage);
            $this->assertStringContainsString('The number is required.', $errorMessage);
            $this->assertStringContainsString('The city is required.', $errorMessage);
            $this->assertStringContainsString('The county is required.', $errorMessage);
            $this->assertStringContainsString('The postcode field is required.', $errorMessage);
            $this->assertStringContainsString('The price is required.', $errorMessage);
            $this->assertStringContainsString('The payment type is required.', $errorMessage);
            $this->assertStringContainsString('At least one product is required.', $errorMessage);
        }

    }


}
