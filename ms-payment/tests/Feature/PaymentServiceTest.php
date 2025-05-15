<?php

namespace Tests\Feature;

use App\Services\PaymentService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Charge;
use Stripe\Stripe;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = app(PaymentService::class);
        Stripe::setApiKey('fake-key');
    }

    #[Test]
    public function it_makes_a_payment_successfully()
    {
        $mock = Mockery::mock('alias:' . Charge::class);
        $mock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg['amount'] === 100 &&
                    $arg['currency'] === 'ron' &&
                    $arg['source'] === 'tok_test' &&
                    $arg['description'] === 'Payment for order';
            }))
            ->andReturn((object)['id' => 'ch_test_123']);

        $paymentService = app(PaymentService::class);

        $paymentId = $paymentService->makePayment([
            'price' => 1,
            'stripe_token' => 'tok_test',
        ]);

        $this->assertEquals('ch_test_123', $paymentId);
    }

    #[Test]
    public function it_throws_an_exception_on_failed_payment()
    {
        $mock = Mockery::mock('alias:' . Charge::class);
        $mock->shouldReceive('create')
            ->once()
            ->andThrow(new Exception('Stripe error'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Stripe error');

        $this->paymentService->makePayment([
            'price' => 1,
            'stripe_token' => 'invalid_token',
        ]);
    }
}
