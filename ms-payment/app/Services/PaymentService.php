<?php

namespace App\Services;

use Exception;
use Stripe\Charge;
use Stripe\Stripe;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('app.stripe_secret'));
    }

    /**
     * @throws Exception
     */
    public function makePayment(array $data): string
    {
        try {
            $charge = Charge::create([
                'amount' => $data['price'] * 100,
                'currency' => 'ron',
                'source' => $data['stripe_token'],
                'description' => 'Payment for order',
            ]);

            return $charge->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
