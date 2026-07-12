<div class="grid lg:grid-cols-2 gap-6">
    <form wire:submit.prevent="send" class="form-card">
        <h3 class="form-card-title">Send Global Notice</h3>
        <div class="space-y-4">
            <div class="form-field">
                <label class="form-label field-required">Title</label>
                <input type="text" wire:model="title" class="form-control" placeholder="Notification title">
                @error('title')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-field">
                <label class="form-label field-required">Message</label>
                <textarea wire:model="body" rows="5" class="form-control" placeholder="Write your message..."></textarea>
                @error('body')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn-primary">Send to All</button>
        </div>
    </form>
    <div class="bg-white rounded-xl border">
        <div class="px-5 py-4 border-b font-semibold">Recent</div>
        <ul class="divide-y">@foreach($notifications as $n)<li class="px-5 py-4"><p class="font-medium">{{ $n->title }}</p><p class="text-sm text-slate-500 mt-1">{{ Str::limit($n->body, 80) }}</p><p class="text-xs text-slate-400 mt-2">{{ $n->sent_at?->diffForHumans() }}</p></li>@endforeach</ul>
    </div>
</div>
