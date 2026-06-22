<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Member;
use App\Models\MemberDocument;
use App\Models\MemberFamily;
use App\Models\MemberVehicle;
use App\Models\ParkingSlot;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ResidentProfile extends Component
{
    use BelongsToSociety, HandlesAdminForms, WithPanelLayout, WithFileUploads;

    public Member $member;

    public string $tab = 'profile';

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public ?int $flat_id = null;

    public string $member_type = 'owner';

    public string $id_proof_type = '';

    public string $id_proof_no = '';

    public ?string $move_in_date = null;

    public ?string $move_out_date = null;

    public string $family_name = '';

    public string $family_relation = '';

    public string $family_phone = '';

    public string $vehicle_no = '';

    public string $vehicle_type = 'car';

    public string $doc_title = '';

    public string $doc_type = 'aadhaar';

    public $doc_file;

    public function mount(int $id): void
    {
        $this->member = Member::where('society_id', $this->societyId())
            ->with(['flat', 'family', 'vehicles', 'documents'])
            ->findOrFail($id);
        $this->fillFromMember();
    }

    protected function fillFromMember(): void
    {
        $this->name = $this->member->name;
        $this->phone = $this->member->phone;
        $this->email = $this->member->email ?? '';
        $this->flat_id = $this->member->flat_id;
        $this->member_type = $this->member->member_type;
        $this->id_proof_type = $this->member->id_proof_type ?? '';
        $this->id_proof_no = $this->member->id_proof_no ?? '';
        $this->move_in_date = $this->member->move_in_date?->format('Y-m-d');
        $this->move_out_date = $this->member->move_out_date?->format('Y-m-d');
    }

    public function saveProfile(): void
    {
        $sid = $this->societyId();
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'flat_id' => ['nullable', 'integer', Rule::exists('flats', 'id')->where('society_id', $sid)],
            'member_type' => 'required|in:owner,tenant,family',
            'id_proof_type' => 'nullable|string|max:50',
            'id_proof_no' => 'nullable|string|max:50',
            'move_in_date' => 'nullable|date',
            'move_out_date' => 'nullable|date',
        ]);

        $this->member->update($data);
        session()->flash('success', 'Profile updated.');
    }

    public function addFamily(): void
    {
        $this->validate([
            'family_name' => 'required|string|max:255',
            'family_relation' => 'required|string|max:50',
            'family_phone' => 'nullable|string|max:20',
        ]);

        MemberFamily::create([
            'member_id' => $this->member->id,
            'name' => $this->family_name,
            'relation' => $this->family_relation,
            'phone' => $this->family_phone ?: null,
        ]);

        $this->reset(['family_name', 'family_relation', 'family_phone']);
        $this->member->load('family');
        session()->flash('success', 'Family member added.');
    }

    public function deleteFamily(int $id): void
    {
        MemberFamily::where('member_id', $this->member->id)->findOrFail($id)->delete();
        $this->member->load('family');
    }

    public function addVehicle(): void
    {
        $this->validate([
            'vehicle_no' => 'required|string|max:30',
            'vehicle_type' => 'required|in:car,bike,other',
        ]);

        MemberVehicle::create([
            'society_id' => $this->societyId(),
            'member_id' => $this->member->id,
            'vehicle_no' => $this->vehicle_no,
            'type' => $this->vehicle_type,
        ]);

        $this->reset(['vehicle_no']);
        $this->vehicle_type = 'car';
        $this->member->load('vehicles');
        session()->flash('success', 'Vehicle added.');
    }

    public function deleteVehicle(int $id): void
    {
        MemberVehicle::where('member_id', $this->member->id)->findOrFail($id)->delete();
        $this->member->load('vehicles');
    }

    public function addDocument(): void
    {
        $this->validate([
            'doc_title' => 'required|string|max:255',
            'doc_type' => 'required|in:aadhaar,pan,agreement,photo,other',
            'doc_file' => 'nullable|file|max:5120',
        ]);

        $path = $this->doc_file ? $this->doc_file->store('documents/'.$this->societyId(), 'public') : null;

        MemberDocument::create([
            'society_id' => $this->societyId(),
            'member_id' => $this->member->id,
            'title' => $this->doc_title,
            'doc_type' => $this->doc_type,
            'file_path' => $path,
        ]);

        $this->reset(['doc_title', 'doc_file']);
        $this->doc_type = 'aadhaar';
        $this->member->load('documents');
        session()->flash('success', 'Document saved.');
    }

    public function deleteDocument(int $id): void
    {
        MemberDocument::where('member_id', $this->member->id)->findOrFail($id)->delete();
        $this->member->load('documents');
    }

    public function render()
    {
        return $this->societyAdminLayout($this->member->name)
            ->with([
                'flats' => \App\Models\Flat::where('society_id', $this->societyId())->orderBy('flat_no')->get(),
                'parkingSlots' => ParkingSlot::where('society_id', $this->societyId())->get(),
            ]);
    }
}
