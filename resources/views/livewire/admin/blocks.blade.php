<div>
    @include('livewire.partials.alerts')

    <div class="flex justify-end mb-6">
        <button type="button" wire:click="$set('showForm', true)" class="btn-primary">+ Add Tower / Block</button>
    </div>

    @if ($showForm)
        <div class="form-card mb-6">
            <h3 class="form-card-title">{{ $editingId ? 'Edit Tower / Block' : 'Add Tower / Block' }}</h3>
            <p class="form-card-subtitle">Create building blocks or towers for your society.</p>

            <form wire:submit.prevent="save" class="form-grid">
                <div class="form-field">
                    <label class="form-label field-required">Block / Tower Name</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="e.g. Tower A">
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label field-required">Total Floors</label>
                    <input type="number" wire:model="total_floors" class="form-control" min="1" max="200" step="1" placeholder="1">
                    @error('total_floors') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field span-full">
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" rows="2" class="form-control" placeholder="Optional notes"></textarea>
                    @error('description') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-actions span-full">
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Save Block</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                    <button type="button" wire:click="cancelForm" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="text-left px-5 py-3">Name</th>
                    <th class="px-5 py-3 text-center">Floors</th>
                    <th class="px-5 py-3 text-center">Flats</th>
                    <th class="text-right px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($blocks as $b)
                    <tr class="border-t border-slate-100 hover:bg-slate-50" wire:key="block-{{ $b->id }}">
                        <td class="px-5 py-4 font-semibold">{{ $b->name }}</td>
                        <td class="px-5 py-4 text-center">{{ $b->total_floors }}</td>
                        <td class="px-5 py-4 text-center">{{ $b->flats_count }}</td>
                        <td class="px-5 py-4 text-right space-x-3">
                            <button type="button" wire:click="edit({{ $b->id }})" class="text-indigo-600 text-sm font-medium">Edit</button>
                            <button type="button" wire:click="delete({{ $b->id }})" wire:confirm="Delete this block?" class="text-red-600 text-sm font-medium">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">No towers/blocks yet. Add your first block above.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
