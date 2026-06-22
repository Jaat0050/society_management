<div>
    @include('livewire.partials.alerts')
    <div class="flex gap-2 mb-4 flex-wrap">
        <button wire:click="$set('tab','incidents')" class="px-3 py-1 rounded text-sm {{ $tab==='incidents'?'bg-indigo-600 text-white':'bg-slate-100' }}">Incidents</button>
        <button wire:click="$set('tab','cctv')" class="px-3 py-1 rounded text-sm {{ $tab==='cctv'?'bg-indigo-600 text-white':'bg-slate-100' }}">CCTV</button>
        <button wire:click="$set('tab','sos')" class="px-3 py-1 rounded text-sm {{ $tab==='sos'?'bg-red-600 text-white':'bg-slate-100' }}">SOS ({{ $sosAlerts->count() }})</button>
    </div>
    @if($tab==='incidents')
    <form wire:submit.prevent="reportIncident" class="form-card mb-4 space-y-3 max-w-xl">
        <input wire:model="incident_title" class="form-control" placeholder="Incident title">
        <textarea wire:model="incident_description" class="form-control" rows="2" placeholder="Description"></textarea>
        <div class="grid grid-cols-2 gap-3"><select wire:model="severity" class="form-select"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option><option value="critical">Critical</option></select><input wire:model="location" class="form-control" placeholder="Location"></div>
        <button type="submit" class="btn-primary">Report Incident</button>
    </form>
    @foreach($incidents as $i)<div class="form-card mb-2 py-2"><strong>{{ $i->title }}</strong> — {{ $i->severity }} · {{ $i->status }}</div>@endforeach
    {{ $incidents->links() }}
    @elseif($tab==='cctv')
    <form wire:submit.prevent="addCamera" class="form-card mb-4 grid md:grid-cols-3 gap-3 items-end">
        <input wire:model="camera_name" class="form-control" placeholder="Camera name">
        <input wire:model="camera_location" class="form-control" placeholder="Location">
        <input wire:model="stream_url" class="form-control" placeholder="RTSP/HTTP stream URL (stub)">
        <button type="submit" class="btn-primary md:col-span-3">Add Camera</button>
    </form>
    @foreach($cameras as $c)<div class="form-card mb-2 py-2 flex justify-between"><span>{{ $c->name }} @ {{ $c->location }}</span>@if($c->stream_url)<a href="{{ $c->stream_url }}" target="_blank" class="text-xs text-indigo-600">Stream</a>@endif</div>@endforeach
    @else
    @forelse($sosAlerts as $s)<div class="form-card mb-2 border-red-300 flex justify-between items-center"><div><strong>SOS</strong> Flat {{ $s->flat?->label }} — {{ $s->message ?? 'Emergency' }}</div><button wire:click="resolveSos({{ $s->id }})" class="btn-secondary text-xs">Resolve</button></div>@empty<p class="text-slate-500">No active SOS alerts.</p>@endforelse
    @endif
</div>
