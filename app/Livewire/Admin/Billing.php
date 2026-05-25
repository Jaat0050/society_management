<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Flat;
use App\Models\MaintenanceBill;
use Livewire\Component;
use Livewire\WithPagination;

class Billing extends Component
{
    use BelongsToSociety, WithPanelLayout, WithPagination;

    public bool $showForm = false;

    public ?int $flat_id = null;

    public string $title = 'Monthly Maintenance';

    public string $bill_month = '';

    public string $amount = '';

    public string $due_date = '';

    public function mount(): void
    {
        $this->bill_month = now()->format('Y-m');
        $this->due_date = now()->addDays(15)->format('Y-m-d');
    }

    public function save(): void
    {
        $data = $this->validate([
            'flat_id' => 'required|exists:flats,id',
            'title' => 'required|string|max:255',
            'bill_month' => 'required|string|size:7',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        MaintenanceBill::create([...$data, 'society_id' => $this->societyId(), 'status' => 'pending']);
        session()->flash('success', 'Bill generated.');
        $this->showForm = false;
    }

    public function generateBulk(): void
    {
        $amount = $this->validate(['amount' => 'required|numeric|min:0'])['amount'];
        $month = $this->bill_month ?: now()->format('Y-m');

        foreach (Flat::where('society_id', $this->societyId())->get() as $flat) {
            MaintenanceBill::firstOrCreate(
                ['flat_id' => $flat->id, 'bill_month' => $month],
                [
                    'society_id' => $this->societyId(),
                    'title' => 'Maintenance - '.$month,
                    'amount' => $amount,
                    'due_date' => $this->due_date,
                    'status' => 'pending',
                ]
            );
        }

        session()->flash('success', 'Bulk bills generated for all flats.');
    }

    public function render()
    {
        return $this->societyAdminLayout('Maintenance Billing')
            ->with([
                'bills' => MaintenanceBill::where('society_id', $this->societyId())->with('flat')->latest()->paginate(15),
                'flats' => Flat::where('society_id', $this->societyId())->get(),
            ]);
    }
}
