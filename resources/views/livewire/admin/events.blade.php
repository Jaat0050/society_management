<div class="grid lg:grid-cols-2 gap-6">
    <form wire:submit.prevent="save" class="bg-white border rounded-xl p-5 space-y-3">
        <input wire:model="title" placeholder="Event title" class="w-full rounded-lg border-slate-300 text-sm">
        <input wire:model="starts_at" type="datetime-local" class="w-full rounded-lg border-slate-300 text-sm">
        <input wire:model="venue" placeholder="Venue" class="w-full rounded-lg border-slate-300 text-sm">
        <textarea wire:model="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Create Event</button>
    </form>
    <div class="space-y-3">@foreach($events as $e)<div class="bg-white border rounded-xl p-4 flex justify-between"><div><h4 class="font-medium">{{ $e->title }}</h4><p class="text-sm text-slate-500">{{ $e->starts_at->format('d M Y, h:i A') }} · {{ $e->venue }}</p></div><button wire:click="delete({{ $e->id }})" class="text-red-500 text-sm">Delete</button></div>@endforeach</div>
</div>
