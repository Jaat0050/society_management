<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\MaintenanceBill;
use App\Models\PaymentTransaction;
use App\Services\BillingService;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\WithPagination;

class Payments extends Component
{
    use BelongsToSociety, WithPanelLayout, WithPagination;

    public string $filter = 'pending';

    public string $gateway = 'razorpay';

    public function applyPenalties(): void
    {
        $count = app(BillingService::class)->applyOverduePenalties($this->societyId());
        session()->flash('success', "Late fees applied to {$count} overdue bill(s).");
    }

    public function sendReminders(): void
    {
        $pending = MaintenanceBill::where('society_id', $this->societyId())->whereIn('status', ['pending', 'overdue'])->count();
        app(NotificationService::class)->sendBillReminder(
            $this->societyId(),
            'Maintenance Due Reminder',
            "You have {$pending} pending maintenance bill(s). Please pay before due date to avoid late fees."
        );
        session()->flash('success', 'Due reminders sent (SMS logged).');
    }

    public function markPaid(int $id): void
    {
        $bill = MaintenanceBill::where('society_id', $this->societyId())->findOrFail($id);
        $billing = app(BillingService::class);
        $billing->markPaid($bill, $bill->totalDue(), 'manual');
        $billing->recordLedgerIncome($this->societyId(), $bill);
        session()->flash('success', 'Marked as paid.');
    }

    public function initiateOnline(int $id): void
    {
        $bill = MaintenanceBill::where('society_id', $this->societyId())->findOrFail($id);

        PaymentTransaction::create([
            'society_id' => $this->societyId(),
            'maintenance_bill_id' => $bill->id,
            'flat_id' => $bill->flat_id,
            'amount' => $bill->totalDue(),
            'gateway' => $this->gateway,
            'transaction_id' => 'TXN-'.strtoupper(uniqid()),
            'status' => 'success',
            'meta' => ['note' => 'Simulated online payment — connect Razorpay/PayU in production'],
        ]);

        $billing = app(BillingService::class);
        $billing->markPaid($bill, $bill->totalDue(), $this->gateway, 'online-'.time());
        $billing->recordLedgerIncome($this->societyId(), $bill);

        session()->flash('success', 'Online payment recorded (demo mode).');
    }

    public function render()
    {
        $sid = $this->societyId();
        $query = MaintenanceBill::where('society_id', $sid)->with('flat');

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return $this->societyAdminLayout('Payment Tracking')
            ->with([
                'bills' => $query->latest()->paginate(15),
                'totals' => [
                    'paid' => MaintenanceBill::where('society_id', $sid)->where('status', 'paid')->sum('paid_amount'),
                    'pending' => MaintenanceBill::where('society_id', $sid)->whereIn('status', ['pending', 'overdue'])->sum('amount'),
                ],
            ]);
    }
}
