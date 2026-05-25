<?php

namespace App\Livewire\Resident;

use App\Livewire\Concerns\WithResidentLayout;
use App\Models\MaintenanceBill;
use App\Models\PaymentTransaction;
use App\Services\BillingService;
use Livewire\Component;

class Bills extends Component
{
    use WithResidentLayout;

    public function payOnline(int $id): void
    {
        $member = auth()->user()->member;
        $bill = MaintenanceBill::where('flat_id', $member?->flat_id)->findOrFail($id);

        PaymentTransaction::create([
            'society_id' => auth()->user()->society_id,
            'maintenance_bill_id' => $bill->id,
            'flat_id' => $bill->flat_id,
            'amount' => $bill->totalDue(),
            'gateway' => 'razorpay',
            'transaction_id' => 'TXN-R-'.uniqid(),
            'status' => 'success',
        ]);

        $billing = app(BillingService::class);
        $billing->markPaid($bill, $bill->totalDue(), 'razorpay', 'resident-pay');
        $billing->recordLedgerIncome(auth()->user()->society_id, $bill);

        session()->flash('success', 'Payment successful (demo).');
    }

    public function render()
    {
        $flatId = auth()->user()->member?->flat_id;
        $bills = $flatId
            ? MaintenanceBill::where('flat_id', $flatId)->orderByDesc('bill_month')->get()
            : collect();

        return $this->residentLayout('My Bills')->with(compact('bills'));
    }
}
