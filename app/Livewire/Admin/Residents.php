<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Flat;
use App\Models\Member;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Residents extends Component
{
    use BelongsToSociety, HandlesAdminForms, WithPanelLayout, WithPagination;

    public bool $showForm = false;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public ?int $flat_id = null;

    public string $member_type = 'owner';

    public function save(): void
    {
        $this->ensureSocietyContext();
        $this->normalizeNullableIds('flat_id');

        $societyId = $this->societyId();

        $data = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'flat_id' => [
                'nullable',
                'integer',
                Rule::exists('flats', 'id')->where('society_id', $societyId),
            ],
            'member_type' => 'required|in:owner,tenant,family',
        ]);

        $data['society_id'] = $societyId;

        try {
            Member::create($data);

            if ($data['flat_id']) {
                Flat::where('society_id', $societyId)->where('id', $data['flat_id'])->update(['status' => 'occupied']);
            }

            session()->flash('success', 'Resident added successfully.');
        } catch (QueryException $e) {
            $this->handleSaveException($e);
        }

        $this->cancelForm();
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Member::where('society_id', $this->societyId())->findOrFail($id)->delete();
        session()->flash('success', 'Resident removed.');
    }

    public function cancelForm(): void
    {
        $this->reset(['showForm', 'name', 'phone', 'email', 'flat_id']);
        $this->member_type = 'owner';
        $this->resetValidation();
    }

    public function render()
    {
        return $this->societyAdminLayout('Residents')
            ->with([
                'residents' => Member::where('society_id', $this->societyId())->with('flat')->latest()->paginate(15),
                'flats' => Flat::where('society_id', $this->societyId())->orderBy('flat_no')->get(),
            ]);
    }
}
