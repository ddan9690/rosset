<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BenevolenceCasePdfController extends Controller
{
    public function download($id, $slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $case = BenevolenceCase::with(['member', 'category', 'creator'])
            ->where('id', $id)
            ->where('slug', $slug)
            ->firstOrFail();

        $contributions = BenevolenceContribution::where('benevolence_case_id', $case->id)
            ->with('user')
            ->latest('created_at')
            ->get();

        $contributingUserIds = $contributions->pluck('user_id')->unique();
        $totalContributorsCount = $contributingUserIds->count();
        $totalSystemMembers = User::count();
        $contributionPercentage = $totalSystemMembers > 0 
            ? round(($totalContributorsCount / $totalSystemMembers) * 100, 2) 
            : 0;
        $totalAmountCollected = $contributions->sum('amount');

        $contributorsByGender = User::whereIn('id', $contributingUserIds)
            ->selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        $contributorsBySchoolLevel = User::whereIn('id', $contributingUserIds)
            ->selectRaw('school_level, count(*) as count')
            ->groupBy('school_level')
            ->pluck('count', 'school_level')
            ->toArray();

        // Safe filename replacement for case numbers containing slashes or special chars
        $fileIdentifier = !empty($case->case_number) ? str_replace('/', '-', $case->case_number) : $case->id;

        $data = [
            'case' => $case,
            'contributions' => $contributions,
            'totalContributorsCount' => $totalContributorsCount,
            'totalSystemMembers' => $totalSystemMembers,
            'contributionPercentage' => $contributionPercentage,
            'totalAmountCollected' => $totalAmountCollected,
            'contributorsByGender' => $contributorsByGender,
            'contributorsBySchoolLevel' => $contributorsBySchoolLevel,
            'generatedAt' => now()->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a'),
        ];

        $pdf = Pdf::loadView('pdf.benevolence-case', $data)->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Benevolence-Case-' . $fileIdentifier . '.pdf');
    }
}