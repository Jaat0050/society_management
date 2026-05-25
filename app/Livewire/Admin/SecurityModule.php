<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\CctvCamera;
use App\Models\SecurityIncident;
use App\Models\SosAlert;
use Livewire\Component;

class SecurityModule extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $tab = 'incidents';

    public string $incident_title = '';

    public string $incident_description = '';

    public string $severity = 'medium';

    public string $location = '';

    public string $camera_name = '';

    public string $camera_location = '';

    public string $stream_url = '';

    public function reportIncident(): void
    {
        $data = $this->validate([
            'incident_title' => 'required|string|max:255',
            'incident_description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'location' => 'nullable|string|max:255',
        ]);

        SecurityIncident::create([
            'society_id' => $this->societyId(),
            'reported_by' => auth()->id(),
            'title' => $data['incident_title'],
            'description' => $data['incident_description'],
            'severity' => $data['severity'],
            'location' => $data['location'] ?: null,
            'occurred_at' => now(),
        ]);

        session()->flash('success', 'Incident reported.');
        $this->reset(['incident_title', 'incident_description', 'location']);
    }

    public function addCamera(): void
    {
        $data = $this->validate([
            'camera_name' => 'required|string|max:255',
            'camera_location' => 'required|string|max:255',
            'stream_url' => 'nullable|url|max:500',
        ]);

        CctvCamera::create([
            'society_id' => $this->societyId(),
            'name' => $data['camera_name'],
            'location' => $data['camera_location'],
            'stream_url' => $data['stream_url'] ?: null,
        ]);

        session()->flash('success', 'CCTV camera added (integration stub).');
        $this->reset(['camera_name', 'camera_location', 'stream_url']);
    }

    public function resolveSos(int $id): void
    {
        SosAlert::where('society_id', $this->societyId())->findOrFail($id)->update([
            'status' => 'resolved',
            'acknowledged_at' => now(),
        ]);
        session()->flash('success', 'SOS resolved.');
    }

    public function render()
    {
        $sid = $this->societyId();

        return $this->societyAdminLayout('Security')
            ->with([
                'incidents' => SecurityIncident::where('society_id', $sid)->latest()->paginate(10),
                'cameras' => CctvCamera::where('society_id', $sid)->get(),
                'sosAlerts' => SosAlert::where('society_id', $sid)->where('status', 'active')->latest()->get(),
            ]);
    }
}
