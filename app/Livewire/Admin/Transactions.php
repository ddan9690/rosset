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

    protected $queryString = ['search', 'startDate', 'endDate'];

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
        $this->reset(['search', 'startDate', 'endDate']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Transaction::query()
            ->with('user')
            ->when($this->search, function ($q) {
                $q->where('reference_number', 'like', '%' . $this->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            });

        // Calculate total amount based on the current search/date filters
        $totalTransactedAmount = (clone $query)->where('status', 'success')->sum('amount');

        $transactions = $query->latest()->paginate(15);

        return view('livewire.admin.transactions', [
            'transactions' => $transactions,
            'totalTransactedAmount' => $totalTransactedAmount,
        ]);
    }
}