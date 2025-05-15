<?php

namespace Tests\Unit;

use App\Http\Controllers\PaymentController;
use App\Services\PaymentService;
use App\Transformers\PaymentTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    private PaymentService|MockObject $paymentService;
    private PaymentController $controller;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = $this->createMock(PaymentService::class);
        $this->controller = new PaymentController($this->paymentService);
    }

    /**
     * @throws Exception
     */
    #[Test]

    public function it_calls_transform_and_makePayment_on_success()
    {
        $requestData = [
            'user_id' => 1,
            'stripe_token' => 'tok_test_visa',
            'price' => 10,
        ];

        $request = new Request($requestData);

        $transformerMock = Mockery::mock('alias:' . PaymentTransformer::class);
        $transformerMock->shouldReceive('transform')
            ->once()
            ->with($requestData)
            ->andReturn($requestData);

        $this->paymentService
            ->expects($this->once())
            ->method('makePayment')
            ->with($requestData)
            ->willReturn('ch_test_123');

        $response = $this->controller->makePayment($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }


    /**
     * @throws Exception
     */
    #[Test]

    public function test_it_returns_400_when_exception_is_thrown()
    {
        $requestData = [
            'stripe_token' => 'tok_invalid',
            'price' => 0,
        ];

        $request = new Request($requestData);

        $mock = Mockery::mock('alias:' . PaymentTransformer::class);
        $mock->shouldReceive('transform')
            ->once()
            ->with($requestData)
            ->andThrow(new Exception('Validation failed'));

        $response = $this->controller->makePayment($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
