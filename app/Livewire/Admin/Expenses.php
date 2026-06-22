<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Expense;
use App\Models\LedgerEntry;
use App\Models\Vendor;
use Livewire\Component;

class Expenses extends Component
{
    use BelongsToSociety, HandlesAdminForms, WithPanelLayout;

    public bool $showForm = false;

    public string $category = '';

    public string $amount = '';

    public string $expense_date = '';

    public string $payment_mode = 'cash';

    public string $description = '';

    public ?int $vendor_id = null;

    public function save(): void
    {
        $this->ensureSocietyContext();
        $data = $this->validate([
            'category' => 'required|string|max:80',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_mode' => 'required|string|max:30',
            'description' => 'nullable|string',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $expense = Expense::create([...$data, 'society_id' => $this->societyId(), 'amount' => $data['amount']]);

        LedgerEntry::create([
            'society_id' => $this->societyId(),
            'type' => 'expense',
            'category' => $data['category'],
            'amount' => $data['amount'],
            'entry_date' => $data['expense_date'],
            'reference_type' => 'expense',
            'reference_id' => $expense->id,
            'description' => $data['description'],
        ]);

        session()->flash('success', 'Expense recorded.');
        $this->reset(['showForm', 'category', 'amount', 'description', 'vendor_id']);
        $this->expense_date = now()->format('Y-m-d');
    }

    public function mount(): void
    {
        $this->expense_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $sid = $this->societyId();

        return $this->societyAdminLayout('Expense Tracking')
            ->with([
                'expenses' => Expense::where('society_id', $sid)->with('vendor')->latest('expense_date')->paginate(15),
                'vendors' => Vendor::where('society_id', $sid)->where('is_active', true)->get(),
                'total' => Expense::where('society_id', $sid)->sum('amount'),
            ]);
    }
}
