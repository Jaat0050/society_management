<div>
    <div class="flex justify-end mb-6"><button wire:click="$set('showForm', true)" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">+ Add Plan</button></div>
    @if($showForm)
    <form wire:submit.prevent="save" class="bg-white rounded-xl border p-6 mb-6 grid md:grid-cols-3 gap-4">
        <div><label class="text-sm">Plan Name</label><input wire:model="name" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
        <div><label class="text-sm">Price (₹)</label><input wire:model="price" type="number" step="0.01" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
        <div><label class="text-sm">Duration (months)</label><input wire:model="duration_months" type="number" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
        <div><label class="text-sm">Max Flats</label><input wire:model="max_flats" type="number" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
        <div><label class="text-sm">Max Users</label><input wire:model="max_users" type="number" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
        <div class="flex items-end"><label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active"> Active</label></div>
        <div class="md:col-span-3 flex gap-2"><button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Save</button><button type="button" wire:click="resetForm" class="bg-slate-200 px-4 py-2 rounded-lg text-sm">Cancel</button></div>
    </form>
    @endif
    <div class="grid md:grid-cols-3 gap-4">
        @foreach($plans as $p)
        <div class="bg-white rounded-xl border p-5">
            <h3 class="font-bold text-lg">{{ $p->name }}</h3>
            <p class="text-2xl font-bold text-indigo-600 mt-2">₹{{ number_format($p->price, 0) }}</p>
            <p class="text-sm text-slate-500 mt-2">{{ $p->duration_months }} months · {{ $p->max_flats ?? '∞' }} flats</p>
            <button wire:click="edit({{ $p->id }})" class="mt-4 text-indigo-600 text-sm">Edit</button>
        </div>
        @endforeach
    </div>
</div>
