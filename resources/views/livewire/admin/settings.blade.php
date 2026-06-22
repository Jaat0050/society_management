<form wire:submit.prevent="save" class="bg-white border rounded-xl p-6 max-w-lg space-y-5">
    <h3 class="font-semibold text-slate-800">Visitor & Society Settings</h3>
    <label class="flex items-center gap-3 text-sm"><input type="checkbox" wire:model="visitor_approval_required" class="rounded text-indigo-600"> Require resident approval for visitors</label>
    <label class="flex items-center gap-3 text-sm"><input type="checkbox" wire:model="auto_approve_domestic_help" class="rounded text-indigo-600"> Auto-approve domestic help entry</label>
    <label class="flex items-center gap-3 text-sm"><input type="checkbox" wire:model="auto_approve_delivery" class="rounded text-indigo-600"> Auto-approve registered delivery staff</label>
    <div><label class="text-sm text-slate-600">Maintenance due day of month</label><input wire:model="maintenance_due_day" type="number" min="1" max="28" class="form-control mt-1"></div>
    <div><label class="text-sm text-slate-600">Late fee % per month</label><input wire:model="late_fee_percent" type="number" step="0.1" min="0" class="form-control mt-1"></div>
    <div><label class="text-sm text-slate-600">Payment gateway</label><select wire:model="payment_gateway" class="form-select mt-1"><option value="razorpay">Razorpay</option><option value="payu">PayU</option><option value="manual">Manual only</option></select></div>
    <button class="btn-primary">Save Settings</button>
</form>
