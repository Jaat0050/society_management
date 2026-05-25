<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Block;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Blocks extends Component
{
    use BelongsToSociety, HandlesAdminForms, WithPanelLayout;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public int $total_floors = 1;

    public string $description = '';

    public function save(): void
    {
        $this->ensureSocietyContext();

        $societyId = $this->societyId();

        $data = $this->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('blocks', 'name')->where('society_id', $societyId)->ignore($this->editingId),
            ],
            'total_floors' => 'required|integer|min:1|max:200',
            'description' => 'nullable|string|max:500',
        ]);

        $data['society_id'] = $societyId;

        try {
            if ($this->editingId) {
                Block::where('society_id', $societyId)->findOrFail($this->editingId)->update($data);
                session()->flash('success', 'Tower / block updated successfully.');
            } else {
                Block::create($data);
                session()->flash('success', 'Tower / block added successfully.');
            }
        } catch (QueryException $e) {
            $this->handleSaveException($e);
        }

        $this->cancelForm();
    }

    public function edit(int $id): void
    {
        $b = Block::where('society_id', $this->societyId())->findOrFail($id);
        $this->editingId = $id;
        $this->name = $b->name;
        $this->total_floors = $b->total_floors;
        $this->description = $b->description ?? '';
        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        Block::where('society_id', $this->societyId())->findOrFail($id)->delete();
        session()->flash('success', 'Tower / block deleted.');
    }

    public function cancelForm(): void
    {
        $this->reset(['showForm', 'editingId', 'name', 'description']);
        $this->total_floors = 1;
        $this->resetValidation();
    }

    public function render()
    {
        return $this->societyAdminLayout('Towers / Blocks')
            ->with(['blocks' => Block::where('society_id', $this->societyId())->withCount('flats')->orderBy('name')->get()]);
    }
}
