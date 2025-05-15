<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Transformers\PaymentTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService){}


    /**
     * @throws Exception
     */
    public function makePayment(Request $request): JsonResponse {
        try {
            Log::info('here');
            $data = $request->all();
            $payment = PaymentTransformer::transform($data);
            $paymentId = $this->paymentService->makePayment($payment);
            Log::info('Payment created successfully with id '.$paymentId);
            return response()->json([
                'message' => 'Payment created successfully with id '.$paymentId,
            ],201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

}
