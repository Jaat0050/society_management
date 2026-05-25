<div>
    @include('livewire.partials.alerts')
    <div class="form-card mb-6"><h2 class="text-xl font-bold">Welcome, {{ $member?->name ?? auth()->user()->name }}</h2><p class="text-slate-500">{{ $member?->flat?->label }} · {{ ucfirst($member?->member_type ?? 'resident') }}</p></div>
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Pending Bills</p><p class="text-2xl font-bold text-amber-600">{{ $pendingBills }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Open Complaints</p><p class="text-2xl font-bold">{{ $openComplaints }}</p></div>
    </div>
    @foreach($alerts as $a)<div class="form-card mb-2 border-red-300 bg-red-50"><strong class="text-red-700">{{ $a->title }}</strong><p class="text-sm">{{ $a->body }}</p></div>@endforeach
    <div class="form-card"><h3 class="font-semibold mb-2">Latest Notices</h3>@foreach($notices as $n)<p class="text-sm py-1 border-b">{{ $n->title }}</p>@endforeach</div>
</div>
