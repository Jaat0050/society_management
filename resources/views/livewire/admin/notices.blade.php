<div class="grid lg:grid-cols-2 gap-6">
    <form wire:submit.prevent="publish" class="bg-white border rounded-xl p-5 space-y-3">
        <h3 class="font-semibold">Publish Notice</h3>
        <input wire:model="title" placeholder="Title" class="w-full rounded-lg border-slate-300 text-sm">
        <textarea wire:model="body" rows="5" placeholder="Notice body" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_pinned"> Pin notice</label>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Publish</button>
    </form>
    <div class="space-y-3">@foreach($notices as $n)<div class="bg-white border rounded-xl p-4"><div class="flex justify-between"><h4 class="font-medium">{{ $n->title }}@if($n->is_pinned) <span class="text-xs text-amber-600">Pinned</span>@endif</h4><button wire:click="delete({{ $n->id }})" class="text-red-500 text-xs">Delete</button></div><p class="text-sm text-slate-600 mt-2">{{ Str::limit($n->body, 120) }}</p><p class="text-xs text-slate-400 mt-2">{{ $n->published_at?->format('d M Y') }}</p></div>@endforeach</div>
</div>
