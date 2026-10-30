<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use App\Models\AgmPayment;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AgmPaymentsPdfController extends Controller
{
    public function download($year)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Fetch all successful AGM payments for the requested year, excluding developers
        $payments = AgmPayment::whereHas('user', function ($q) {
                $q->excludeDevelopers();
            })
            ->with(['user' => function ($q) {
                $q->excludeDevelopers();
            }])
            ->where('year', $year)
            ->where('status', 'success')
            ->latest('paid_at')
            ->get();

        $data = [
            'year' => $year,
            'payments' => $payments,
            'generatedAt' => now()->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a'),
        ];

        $pdf = Pdf::loadView('pdf.agm-payments-pdf', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'agm-payments-' . $year . '.pdf');
    }
}