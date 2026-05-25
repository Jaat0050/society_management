<form wire:submit.prevent="save" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
    <div><label class="text-sm">App Name</label><input wire:model="app_name" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
    <div><label class="text-sm">Support Email</label><input wire:model="support_email" type="email" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
    <div><label class="text-sm">Support Phone</label><input wire:model="support_phone" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
    <div><label class="text-sm">Currency</label><input wire:model="currency" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
    <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Save Settings</button>
</form>
