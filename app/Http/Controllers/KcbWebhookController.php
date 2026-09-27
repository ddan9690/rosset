<?php

namespace App\Http\Controllers;

use App\Services\KcbPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KcbWebhookController extends Controller
{
    public function handle(Request $request, KcbPaymentService $paymentService)
    {
        Log::info('KCB Webhook Payload Received:', $request->all());

        $result = $paymentService->processIpnNotification($request->all());

        return response()->json($result);
    }
}