<?php

namespace Tests\Unit;

use App\Services\PaymentService;
use Exception;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Charge;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService();
    }

    #[Test]
    public function it_returns_stripe_charge_id_when_payment_succeeds()
    {
        $mock = Mockery::mock('alias:' . Charge::class);
        $mock->shouldReceive('create')
            ->once()
            ->with([
                'amount' => 5000,
                'currency' => 'ron',
                'source' => 'tok_test_visa',
                'description' => 'Payment for order',
            ])
            ->andReturn((object)['id' => 'ch_test_123']);

        $paymentId = $this->paymentService->makePayment([
            'price' => 50,
            'stripe_token' => 'tok_test_visa',
        ]);

        $this->assertEquals('ch_test_123', $paymentId);
    }

    #[Test]
    public function it_throws_an_exception_when_stripe_fails()
    {
        $mock = Mockery::mock('alias:' . Charge::class);
        $mock->shouldReceive('create')
            ->once()
            ->andThrow(new Exception('Stripe error'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Stripe error');

        $this->paymentService->makePayment([
            'price' => 10,
            'stripe_token' => 'invalid_token',
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
