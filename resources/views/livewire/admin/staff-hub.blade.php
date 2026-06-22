<div>
    @include('livewire.partials.alerts')
    <div class="flex gap-2 mb-4"><button wire:click="$set('tab','attendance')" class="px-3 py-1 rounded {{ $tab==='attendance'?'bg-indigo-600 text-white':'bg-slate-100' }}">Attendance</button><button wire:click="$set('tab','salary')" class="px-3 py-1 rounded {{ $tab==='salary'?'bg-indigo-600 text-white':'bg-slate-100' }}">Salary</button></div>
    <div class="form-card mb-4 grid md:grid-cols-4 gap-3 items-end">
        <div class="form-field"><label class="form-label">Staff</label><select wire:model="staff_id" class="form-select"><option value="">—</option>@foreach($staffList as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
        @if($tab==='attendance')
        <div class="form-field"><label class="form-label">Date</label><input type="date" wire:model="attendance_date" class="form-control"></div>
        <div class="form-field"><label class="form-label">Status</label><select wire:model="attendance_status" class="form-select"><option value="present">Present</option><option value="absent">Absent</option><option value="leave">Leave</option></select></div>
        <button wire:click="markAttendance" class="btn-primary">Mark</button>
        @else
        <div class="form-field"><label class="form-label">Month</label><input type="month" wire:model="payment_month" class="form-control"></div>
        <div class="form-field"><label class="form-label">Amount</label><input type="number" wire:model="payment_amount" class="form-control"></div>
        <button wire:click="recordPayment" class="btn-primary">Record Pay</button>
        @endif
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="form-card"><h3 class="font-semibold mb-2">Recent Attendance</h3>@foreach($attendance as $a)<p class="text-sm py-1 border-b">{{ $a->attendance_date->format('d M') }} — {{ $a->staff->name }}: {{ $a->status }}</p>@endforeach</div>
        <div class="form-card"><h3 class="font-semibold mb-2">Salary Payments</h3>@foreach($payments as $p)<p class="text-sm py-1 border-b">{{ $p->payment_month }} — {{ $p->staff->name }}: ₹{{ number_format($p->amount,0) }}</p>@endforeach</div>
    </div>
</div>
