<div class="grid lg:grid-cols-2 gap-6">
    <form wire:submit.prevent="send" class="bg-white rounded-xl border p-6">
        <h3 class="font-semibold mb-4">Send Global Notice</h3>
        <div class="space-y-4">
            <div><label class="text-sm">Title</label><input wire:model="title" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></div>
            <div><label class="text-sm">Message</label><textarea wire:model="body" rows="5" class="w-full rounded-lg border-slate-300 mt-1 text-sm"></textarea></div>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Send to All</button>
        </div>
    </form>
    <div class="bg-white rounded-xl border">
        <div class="px-5 py-4 border-b font-semibold">Recent</div>
        <ul class="divide-y">@foreach($notifications as $n)<li class="px-5 py-4"><p class="font-medium">{{ $n->title }}</p><p class="text-sm text-slate-500 mt-1">{{ Str::limit($n->body, 80) }}</p><p class="text-xs text-slate-400 mt-2">{{ $n->sent_at?->diffForHumans() }}</p></li>@endforeach</ul>
    </div>
</div>
