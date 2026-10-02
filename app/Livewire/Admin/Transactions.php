<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Transactions | ROSSET-SWA')]
class Transactions extends Component
{
    use WithPagination;

    public $search = '';
    public $startDate = '';
    public $endDate = '';

    protected $queryString = [
        'search',
        'startDate',
        'endDate',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'startDate',
            'endDate',
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $query = Transaction::query()
            ->with('user')
            ->when($this->search, function ($q) {
                $search = trim($this->search);

                $q->where(function ($query) use ($search) {
                    $query->where('reference_number', 'like', '%' . $search . '%')
                        ->orWhere('phone_number', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('membership_number', 'like', '%' . $search . '%')
                                ->orWhere('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            });

        // Total successful transaction amount
        // Uses the same search/date filters currently applied.
        $totalTransactedAmount = (clone $query)
            ->where('status', 'success')
            ->sum('amount');

        // All transactions, paginated
        $transactions = $query
            ->latest('created_at')
            ->paginate(15)
            ->through(function ($transaction) {
                if ($transaction->created_at) {
                    $transaction->created_at = $transaction->created_at->setTimezone('Africa/Nairobi');
                }
                return $transaction;
            });

        return view('livewire.admin.transactions', [
            'transactions' => $transactions,
            'totalTransactedAmount' => $totalTransactedAmount,
        ]);
    }
}
