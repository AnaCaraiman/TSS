<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private string $paymentServiceUrl;
    public function __construct(protected AuthController $authController,protected CartController $cartController){
        $this->paymentServiceUrl = config('services.ms_payment.url');
    }


    public function makePayment(array $data): JsonResponse
    {
        try {
            Log::info($data);
            $response = Http::post($this->paymentServiceUrl . '/api/ms-payment', $data);

            return response()->json(json_decode($response->getBody()->getContents(), true), $response->status());
        }
        catch (Exception $e){
            return response()->json([
                'message'=> $e->getMessage(),
            ], 400);
        }
    }

}
