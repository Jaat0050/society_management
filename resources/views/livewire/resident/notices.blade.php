<div>
    <h3 class="font-semibold mb-3">Notices</h3>
    @foreach($notices as $n)<div class="form-card mb-3"><h4 class="font-medium">{{ $n->title }}</h4><p class="text-sm text-slate-600 mt-1">{{ Str::limit($n->body, 200) }}</p></div>@endforeach
    <h3 class="font-semibold mb-3 mt-6">Upcoming Events</h3>
    @foreach($events as $e)<div class="form-card mb-3"><h4 class="font-medium">{{ $e->title }}</h4><p class="text-xs text-slate-500">{{ $e->starts_at->format('d M Y H:i') }} @ {{ $e->venue }}</p></div>@endforeach
</div>
