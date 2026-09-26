<?php

namespace App\Livewire\Admin;

use App\Imports\MembersImport;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.dashboard')]
#[Title('Onboard Members | ROSSET-SWA')]
class MemberOnboard extends Component
{
    use WithFileUploads, WithPagination;

    public $excelFile;

    public function uploadMembers()
    {
        set_time_limit(300);

        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new MembersImport, $this->excelFile->getRealPath());

            session()->flash('message', 'Members successfully imported and updated!');
            $this->reset('excelFile');
            $this->resetPage(); 
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.member-onboard', [
            'users' => User::orderBy('membership_number', 'asc')->paginate(15),
            'totalImported' => User::count(),
        ]);
    }
}