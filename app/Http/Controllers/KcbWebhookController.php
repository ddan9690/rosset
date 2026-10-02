<?php

namespace App\Http\Controllers;

use App\Services\KcbPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KcbWebhookController extends Controller
{
    public function handle(
        Request $request,
        KcbPaymentService $paymentService
    ) {
        try {
            $rawBody = $request->getContent();
            $payload = json_decode($rawBody, true) ?: $request->all();

            if (empty($payload)) {
                Log::error('KCB Webhook Empty Payload');

                return response()->json([
                    'ResultCode' => 1,
                    'ResultDesc' => 'Empty payload',
                ], 400);
            }

            $result = $paymentService->processIpnNotification($payload);

            return response()->json([
                'ResultCode' => $result['success'] ? 0 : 1,
                'ResultDesc' => $result['message'] ?? 'KCB IPN received.',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('KCB Webhook Controller Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Webhook processing error.',
            ], 500);
        }
    }
}