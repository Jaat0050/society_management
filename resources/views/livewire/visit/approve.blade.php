<div class="max-w-md mx-auto py-10 px-4">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Visitor Approval</h1>
        <p class="text-slate-500 text-sm mt-1">QR link or OTP approval</p>
    </div>
    @if(!$entry)
        <div class="rounded-xl bg-red-50 border border-red-200 p-6 text-center text-red-700">{{ $message }}</div>
    @else
        <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6 space-y-4">
            <div class="text-center">
                <p class="text-xs text-slate-500 uppercase">{{ $entry->society->name }}</p>
                <h2 class="text-2xl font-bold">{{ $entry->visitor_name }}</h2>
                <p class="font-mono text-indigo-600 font-bold">{{ $entry->pass_code }}</p>
            </div>
            <dl class="grid grid-cols-2 gap-2 text-sm">
                <div><dt class="text-slate-500">Flat</dt><dd class="font-medium">{{ $entry->flat?->label ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Type</dt><dd class="font-medium capitalize">{{ $entry->visit_type }}</dd></div>
            </dl>
            @if($entry->status === 'pending' && $entry->approval_otp)
            <div class="bg-amber-50 rounded-lg p-3 text-center">
                <p class="text-xs text-amber-700">Approval OTP (share with resident)</p>
                <p class="text-2xl font-mono font-bold tracking-widest">{{ $entry->approval_otp }}</p>
            </div>
            <div class="form-field"><label class="form-label">Enter OTP to approve</label><input wire:model="otp" class="form-control text-center font-mono" maxlength="6" placeholder="6-digit OTP"></div>
            <button type="button" wire:click="approveByOtp" class="btn-primary w-full !w-full">Approve with OTP</button>
            @endif
            <p class="text-center text-sm font-semibold px-3 py-2 rounded-lg bg-slate-100">{{ $entry->statusLabel() }}</p>
            @if($message)<p class="text-center text-sm text-indigo-600">{{ $message }}</p>@endif
            @if($entry->status === 'pending')
            <div class="flex gap-3">
                <button type="button" wire:click="approve" class="flex-1 btn-primary">Approve</button>
                <button type="button" wire:click="reject" class="flex-1 btn-secondary !text-red-600">Reject</button>
            </div>
            @endif
        </div>
    @endif
</div>
