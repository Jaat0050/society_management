<div>
    @include('livewire.partials.alerts')
    <div class="flex gap-2 mb-4"><button wire:click="$set('tab','notify')" class="px-3 py-1 rounded {{ $tab==='notify'?'bg-indigo-600 text-white':'bg-slate-100' }}">SMS/Email/Push</button><button wire:click="$set('tab','emergency')" class="px-3 py-1 rounded {{ $tab==='emergency'?'bg-red-600 text-white':'bg-slate-100' }}">Emergency</button></div>
    @if($tab==='notify')
    <form wire:submit.prevent="sendNotification" class="form-card mb-6 space-y-3 max-w-xl">
        <div class="grid grid-cols-2 gap-3">
            <div class="form-field"><label class="form-label">Channel</label><select wire:model="channel" class="form-select"><option value="sms">SMS</option><option value="email">Email</option><option value="push">Push</option></select></div>
            <div class="form-field"><label class="form-label">Recipients</label><select wire:model="recipient_group" class="form-select"><option value="all_residents">All Residents</option><option value="committee">Committee</option><option value="pending_bills">Pending Bills</option></select></div>
        </div>
        <div class="form-field"><label class="form-label">Title</label><input wire:model="title" class="form-control"></div>
        <div class="form-field"><label class="form-label">Message</label><textarea wire:model="body" class="form-control" rows="3"></textarea></div>
        <button type="submit" class="btn-primary">Send Notification</button>
    </form>
    @else
    <form wire:submit.prevent="sendEmergency" class="form-card mb-6 space-y-3 max-w-xl border-red-200">
        <div class="form-field"><label class="form-label">Severity</label><select wire:model="severity" class="form-select"><option value="info">Info</option><option value="warning">Warning</option><option value="critical">Critical</option></select></div>
        <div class="form-field"><label class="form-label">Alert Title</label><input wire:model="alert_title" class="form-control"></div>
        <div class="form-field"><label class="form-label">Alert Message</label><textarea wire:model="alert_body" class="form-control" rows="3"></textarea></div>
        <button type="submit" class="btn-primary !bg-red-600">Broadcast Emergency</button>
    </form>
    @endif
    <div class="grid md:grid-cols-2 gap-4">
        <div class="form-card"><h3 class="font-semibold mb-2">Sent Notifications</h3>@foreach($notifications as $n)<p class="text-sm py-1 border-b">{{ $n->channel }}: {{ $n->title }} <span class="text-xs text-slate-400">({{ $n->status }})</span></p>@endforeach</div>
        <div class="form-card"><h3 class="font-semibold mb-2">Emergency Alerts</h3>@foreach($alerts as $a)<p class="text-sm py-1 border-b text-red-700">{{ $a->title }} — {{ $a->severity }}</p>@endforeach</div>
    </div>
</div>
