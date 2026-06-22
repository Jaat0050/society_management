<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\StaffAttendance;
use App\Models\StaffMember;
use App\Models\StaffPayment;
use Livewire\Component;

class StaffHub extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $tab = 'attendance';

    public ?int $staff_id = null;

    public string $attendance_date = '';

    public string $attendance_status = 'present';

    public string $payment_month = '';

    public string $payment_amount = '';

    public function mount(): void
    {
        $this->attendance_date = now()->format('Y-m-d');
        $this->payment_month = now()->format('Y-m');
    }

    public function markAttendance(): void
    {
        $this->validate([
            'staff_id' => 'required|exists:staff,id',
            'attendance_date' => 'required|date',
            'attendance_status' => 'required|in:present,absent,leave,half_day',
        ]);

        StaffAttendance::updateOrCreate(
            ['staff_id' => $this->staff_id, 'attendance_date' => $this->attendance_date],
            ['status' => $this->attendance_status]
        );

        session()->flash('success', 'Attendance saved.');
    }

    public function recordPayment(): void
    {
        $this->validate([
            'staff_id' => 'required|exists:staff,id',
            'payment_month' => 'required|string|size:7',
            'payment_amount' => 'required|numeric|min:0',
        ]);

        StaffPayment::create([
            'staff_id' => $this->staff_id,
            'payment_month' => $this->payment_month,
            'amount' => $this->payment_amount,
            'paid_at' => now(),
        ]);

        session()->flash('success', 'Salary payment recorded.');
    }

    public function render()
    {
        $sid = $this->societyId();

        return $this->societyAdminLayout('Staff & Attendance')
            ->with([
                'staffList' => StaffMember::where('society_id', $sid)->where('is_active', true)->orderBy('name')->get(),
                'attendance' => StaffAttendance::whereHas('staff', fn ($q) => $q->where('society_id', $sid))
                    ->with('staff')->latest('attendance_date')->take(20)->get(),
                'payments' => StaffPayment::whereHas('staff', fn ($q) => $q->where('society_id', $sid))
                    ->with('staff')->latest('paid_at')->take(20)->get(),
            ]);
    }
}
