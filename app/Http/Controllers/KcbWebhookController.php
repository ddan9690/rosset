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

            /**
             * Get the raw request body first.
             *
             * This is important because KCB sends JSON and we want
             * to preserve the complete original callback.
             */
            $rawBody = $request->getContent();

            Log::info('KCB Webhook Raw Body Received', [
                'content_type' => $request->header('Content-Type'),
                'content_length' => strlen($rawBody),
                'raw_body' => $rawBody,
            ]);

            /**
             * Decode JSON manually.
             */
            $payload = json_decode($rawBody, true);

            /**
             * If the request wasn't JSON, fall back to Laravel input.
             */
            if (!is_array($payload)) {
                $payload = $request->all();
            }

            Log::info('KCB Webhook Payload Received', [
                'payload' => $payload,
            ]);

            if (empty($payload)) {

                Log::error('KCB Webhook Empty Payload');

                return response()->json([
                    'ResultCode' => 1,
                    'ResultDesc' => 'Empty payload',
                ], 400);
            }

            /**
             * Process the COMPLETE payload.
             */
            $result = $paymentService->processIpnNotification(
                $payload
            );

            Log::info('KCB IPN Processing Result', [
                'result' => $result,
            ]);

            /**
             * Return HTTP 200 to acknowledge receipt.
             *
             * The payment has already been processed by the service.
             */
            return response()->json([
                'ResultCode' => $result['success'] ? 0 : 1,
                'ResultDesc' =>
                    $result['message']
                    ?? 'KCB IPN received.',
            ], 200);

        } catch (\Throwable $e) {

            Log::error('KCB Webhook Controller Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Webhook processing error.',
            ], 500);
        }
    }
}