<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Block;
use App\Models\Flat;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Flats extends Component
{
    use BelongsToSociety, HandlesAdminForms, WithPanelLayout, WithPagination;

    public bool $showForm = false;

    public ?int $editingId = null;

    public ?int $block_id = null;

    public string $floor = '';

    public string $flat_no = '';

    public string $type = 'residential';

    public string $status = 'vacant';

    public ?string $area_sqft = null;

    public function save(): void
    {
        $this->ensureSocietyContext();
        $this->normalizeNullableIds('block_id');

        $societyId = $this->societyId();

        $blockName = null;
        if ($this->block_id) {
            $block = Block::where('society_id', $societyId)->find($this->block_id);
            if (! $block) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'block_id' => 'Selected tower/block is invalid.',
                ]);
            }
            $blockName = $block->name;
        }

        $data = $this->validate([
            'block_id' => [
                'nullable',
                'integer',
                Rule::exists('blocks', 'id')->where('society_id', $societyId),
            ],
            'floor' => 'nullable|string|max:10',
            'flat_no' => [
                'required',
                'string',
                'max:20',
                Rule::unique('flats', 'flat_no')
                    ->where('society_id', $societyId)
                    ->where(function ($q) use ($blockName) {
                        $blockName === null
                            ? $q->whereNull('block')
                            : $q->where('block', $blockName);
                    })
                    ->ignore($this->editingId),
            ],
            'type' => 'required|in:residential,commercial,parking',
            'status' => 'required|in:occupied,vacant',
            'area_sqft' => 'nullable|numeric|min:1|max:99999',
        ]);

        $payload = [
            'society_id' => $societyId,
            'block_id' => $this->block_id,
            'block' => $blockName,
            'floor' => $data['floor'] ?: null,
            'flat_no' => $data['flat_no'],
            'type' => $data['type'],
            'status' => $data['status'],
            'area_sqft' => $data['area_sqft'] ?: null,
        ];

        try {
            if ($this->editingId) {
                Flat::where('society_id', $societyId)->findOrFail($this->editingId)->update($payload);
                session()->flash('success', 'Flat updated successfully.');
            } else {
                Flat::create($payload);
                session()->flash('success', 'Flat added successfully.');
            }
        } catch (QueryException $e) {
            $this->handleSaveException($e);
        }

        $this->cancelForm();
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $flat = Flat::where('society_id', $this->societyId())->findOrFail($id);
        $this->editingId = $id;
        $this->block_id = $flat->block_id;
        $this->floor = $flat->floor ?? '';
        $this->flat_no = $flat->flat_no;
        $this->type = $flat->type;
        $this->status = $flat->status;
        $this->area_sqft = $flat->area_sqft ? (string) $flat->area_sqft : null;
        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        Flat::where('society_id', $this->societyId())->findOrFail($id)->delete();
        session()->flash('success', 'Flat deleted.');
    }

    public function cancelForm(): void
    {
        $this->reset(['showForm', 'editingId', 'block_id', 'floor', 'flat_no', 'area_sqft']);
        $this->type = 'residential';
        $this->status = 'vacant';
        $this->resetValidation();
    }

    public function render()
    {
        $flats = Flat::where('society_id', $this->societyId())
            ->with('blockRelation')
            ->latest()
            ->paginate(15);

        return $this->societyAdminLayout('Manage Flats')
            ->with([
                'flats' => $flats,
                'blocks' => Block::where('society_id', $this->societyId())->orderBy('name')->get(),
            ]);
    }
}
