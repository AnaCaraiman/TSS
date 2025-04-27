<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class OrderController
{
    private string $orderServiceUrl;
    public function __construct(protected AuthController $authController,protected PaymentController $paymentController,protected CartController $cartController) {
        $this->orderServiceUrl = config("services.ms_order.url");
    }


    public function addOrder(Request $request): JsonResponse
    {
        try
        {
            $data = $request->all();
            $data['user_id'] = $this->authController->getUserId($request);
            $data['price'] = $this->cartController->getCartTotal($request);
            $data['products'] = $this->cartController->getCartItems($request);
            if ($data['payment_type'] == "credit_card") {
                $paymentResponse = $this->paymentController->makePayment($data);
                if ($paymentResponse->status() != 201) {
                    throw new Exception($paymentResponse->status());
                }
            }

            $response = Http::post($this->orderServiceUrl . '/api/ms-order', $data);
            return response()->json(json_decode($response->getBody()->getContents(), true));
        }
        catch (Exception $e) {
            return response()->json(json_decode($e->getMessage(), true));
        }
    }


    /**
     * @throws ConnectionException
     */
    public function getOrders(Request $request): JsonResponse {
        $userId = $this->authController->getUserId($request);
        $page = $request->query('page', 1);

        $response = Http::get($this->orderServiceUrl . '/api/ms-order',['user_id' => $userId,'page' => $page]);
        return response()->json(json_decode($response->getBody()->getContents(),true));
    }

}
