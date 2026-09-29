<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberContributionsPdfController extends Controller
{
    public function download()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Construct full member name
        $memberName = trim(
            ($user->salutation ?? '') . ' ' .
            ($user->first_name ?? '') . ' ' .
            ($user->last_name ?? '')
        );

        if (empty($memberName)) {
            $memberName = 'Valued Member';
        }

        // Separate Membership Number and TSC Number with '-' fallback if empty
        $membershipNumber = !empty($user->membership_number) ? $user->membership_number : '-';
        $tscNumber = !empty($user->tsc_number) ? $user->tsc_number : '-';

        $contributionHistory = Transaction::query()
            ->with(['benevolenceCase.member'])
            ->where('user_id', $user->id)
            ->where('type', 'benevolence_contribution')
            ->where('status', 'success')
            ->whereNotNull('paid_at')
            ->latest('paid_at')
            ->get();

        $data = [
            'memberName' => $memberName,
            'membershipNumber' => $membershipNumber,
            'tscNumber' => $tscNumber,
            'contributionHistory' => $contributionHistory,
            'generatedAt' => now()->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a'),
        ];

        // Safe filename replacement for dash/fallback values
        $fileIdentifier = $membershipNumber !== '-' ? str_replace('/', '-', $membershipNumber) : ($tscNumber !== '-' ? $tscNumber : 'member');

        $pdf = Pdf::loadView('pdf.member-contributions-pdf', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'member-contributions-' . $fileIdentifier . '.pdf');
    }
}