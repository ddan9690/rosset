<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Setting;
use App\Models\BenevolenceCase;
use Illuminate\Console\Command;
use Carbon\Carbon;

class EvaluateMemberDefaults extends Command
{
    protected $signature = 'members:evaluate-defaults';
    protected $description = 'Evaluate members and update their status to defaulted if they miss benevolence cases past their deadlines.';

    public function handle()
    {
        $this->info('Evaluating member contribution defaults based on past deadlines...');

        $settings = Setting::current();
        $maxAllowedMissed = $settings->max_missed_benevolence_cases ?? 1;
        $now = Carbon::now();

        // Fetch all active or currently defaulted members to re-evaluate
        $users = User::whereIn('status', ['active', 'defaulted'])->get();
        $updatedCount = 0;

        foreach ($users as $user) {
            // Skip members without a join date
            if (!$user->joined_at) {
                continue;
            }

            // Get IDs of cases the user has already contributed to
            $contributedCaseIds = $user->benevolenceContributions()
                ->pluck('benevolence_case_id')
                ->toArray();

            // Count cases where:
            // - Created on or after user's join date
            // - Not the user's own case
            // - Deadline has officially passed (< now)
            // - User has NOT contributed
            $missedCasesCount = BenevolenceCase::query()
                ->where('user_id', '!=', $user->id)
                ->where('created_at', '>=', $user->joined_at)
                ->where('deadline', '<', $now)
                ->whereIn('status', ['active', 'closed'])
                ->whereNotIn('id', $contributedCaseIds)
                ->count();

            if ($missedCasesCount > $maxAllowedMissed) {
                if ($user->status !== 'defaulted') {
                    $user->update(['status' => 'defaulted']);
                    $this->line("Member [ID: {$user->id}] {$user->first_name} {$user->last_name} marked as DEFAULTED (Missed past deadlines: {$missedCasesCount})");
                    $updatedCount++;
                }
            } else {
                // If they cleared arrears or missed cases are within the allowed threshold, restore to active
                if ($user->status === 'defaulted') {
                    $user->update(['status' => 'active']);
                    $this->line("Member [ID: {$user->id}] {$user->first_name} {$user->last_name} restored to ACTIVE.");
                    $updatedCount++;
                }
            }
        }

        $this->info("Evaluation complete. Total status updates: {$updatedCount}");
    }
}