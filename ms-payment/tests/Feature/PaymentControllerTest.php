<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Mockery;
use Stripe\Charge;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/api/ms-payment', [\App\Http\Controllers\PaymentController::class, 'makePayment']);
    }

    #[Test]
    public function it_processes_payment_and_returns_success_response()
    {
        // Mock Stripe charge
        Mockery::mock('alias:' . Charge::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)['id' => 'ch_test_abc123']);

        $payload = [
            'user_id' => 1,
            'stripe_token' => 'tok_test_visa',
            'price' => 100,
        ];

        $response = $this->postJson('/api/ms-payment', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => 'Payment created successfully with id ch_test_abc123',
        ]);
    }

    #[Test]
    public function it_returns_400_on_validation_failure()
    {
        $payload = [
            // missing user_id
            'stripe_token' => null,
            'price' => 0,
        ];

        $response = $this->postJson('/api/ms-payment', $payload);

        $response->assertStatus(400);
        $this->assertStringContainsString('user_id is required', $response->json('message'));
        $this->assertStringContainsString('stripe_token is required', $response->json('message'));
        $this->assertStringContainsString('amount must be at least 1', $response->json('message'));
    }

    #[Test]
    public function it_returns_400_if_stripe_fails()
    {
        Mockery::mock('alias:' . Charge::class)
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Stripe error: token expired'));

        $payload = [
            'user_id' => 1,
            'stripe_token' => 'tok_expired',
            'price' => 100,
        ];

        $response = $this->postJson('/api/ms-payment', $payload);

        $response->assertStatus(400);
        $this->assertStringContainsString('Stripe error: token expired', $response->json('message'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
