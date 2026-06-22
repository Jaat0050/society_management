<?php

namespace App\Services;

use App\Models\MaintenanceBill;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function penaltyRate(int $societyId): float
    {
        $value = DB::table('society_settings')
            ->where('society_id', $societyId)
            ->where('key', 'late_fee_percent')
            ->value('value');

        return (float) ($value ?? 2);
    }

    public function applyOverduePenalties(int $societyId): int
    {
        $rate = $this->penaltyRate($societyId);
        $count = 0;

        MaintenanceBill::where('society_id', $societyId)
            ->where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->each(function (MaintenanceBill $bill) use ($rate, &$count) {
                $daysLate = now()->diffInDays($bill->due_date);
                $penalty = round($bill->amount * ($rate / 100) * max(1, ceil($daysLate / 30)), 2);
                $bill->update([
                    'status' => 'overdue',
                    'penalty_amount' => $penalty,
                ]);
                $count++;
            });

        return $count;
    }

    public function markPaid(MaintenanceBill $bill, float $amount, string $mode = 'manual', ?string $ref = null): MaintenanceBill
    {
        $totalDue = $bill->amount + $bill->penalty_amount;
        $paid = min($amount, $totalDue);

        $bill->update([
            'status' => $paid >= $totalDue ? 'paid' : 'pending',
            'paid_amount' => $paid,
            'paid_at' => now(),
            'payment_mode' => $mode,
            'transaction_ref' => $ref,
        ]);

        return $bill->fresh();
    }

    public function recordLedgerIncome(int $societyId, MaintenanceBill $bill): void
    {
        DB::table('ledger_entries')->insert([
            'society_id' => $societyId,
            'type' => 'income',
            'category' => 'maintenance',
            'amount' => $bill->paid_amount ?: $bill->amount,
            'entry_date' => now()->toDateString(),
            'reference_type' => 'maintenance_bill',
            'reference_id' => $bill->id,
            'description' => $bill->title,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
