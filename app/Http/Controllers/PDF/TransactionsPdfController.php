<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionsPdfController extends Controller
{
    public function download(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $search = $request->input('search');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $query = Transaction::query()->when($search, function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('reference_number', 'like', '%' . $search . '%')->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        })->when($startDate, function ($q) use ($startDate) {
            $q->whereDate('created_at', '>=', $startDate);
        })->when($endDate, function ($q) use ($endDate) {
            $q->whereDate('created_at', '<=', $endDate);
        });
        $transactions = $query->latest()->get();
        $totalAmount = (clone $query)->where('status', 'success')->sum('amount');
        $user = Auth::user();
        $generatedBy = trim(($user->salutation ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if (empty($generatedBy)) {
            $generatedBy = 'Administrator';
        }
        $data = ['transactions' => $transactions, 'totalAmount' => $totalAmount, 'search' => $search, 'startDate' => $startDate, 'endDate' => $endDate, 'generatedBy' => $generatedBy, 'generatedAt' => now()->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a'),];
        $pdf = Pdf::loadView('pdf.transactions-pdf', $data);
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $fontSize = 6;
            $pageText = "Page {$pageNumber} of {$pageCount}";
            $textWidth = $fontMetrics->getTextWidth($pageText, $font, $fontSize);
            $pageWidth = $canvas->get_width();
            $x = ($pageWidth - $textWidth) / 2;
            $y = $canvas->get_height() - 18;
            $canvas->text($x, $y, $pageText, $font, $fontSize);
        });
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'gateway-transactions-report-' . now()->format('d-m-Y') . '.pdf');
    }
}
