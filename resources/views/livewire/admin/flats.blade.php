<div>
    @include('livewire.partials.alerts')

    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <p class="text-sm text-slate-500">Add towers/blocks first, then create flats under each block.</p>
        <button type="button" wire:click="$set('showForm', true)" class="btn-primary">+ Add Flat</button>
    </div>

    @if ($showForm)
        <div class="form-card mb-6">
            <h3 class="form-card-title">{{ $editingId ? 'Edit Flat' : 'Add Flat' }}</h3>
            <p class="form-card-subtitle">Link flat to a tower/block for easier management.</p>

            <form wire:submit.prevent="save" class="form-grid">
                <div class="form-field">
                    <label class="form-label">Tower / Block</label>
                    <select wire:model="block_id" class="form-select">
                        <option value="">— No block —</option>
                        @foreach ($blocks as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                    @error('block_id') <span class="field-error">{{ $message }}</span> @enderror
                    @if ($blocks->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">No blocks found. <a href="{{ route('admin.blocks') }}" wire:navigate class="underline">Add a block first</a>.</p>
                    @endif
                </div>

                <div class="form-field">
                    <label class="form-label field-required">Flat Number</label>
                    <input type="text" wire:model="flat_no" class="form-control" placeholder="e.g. 301">
                    @error('flat_no') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label">Floor</label>
                    <input type="text" wire:model="floor" class="form-control" placeholder="e.g. 3">
                    @error('floor') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label">Area (sq.ft)</label>
                    <input type="number" wire:model="area_sqft" class="form-control" min="1" max="99999" step="0.01" placeholder="e.g. 950">
                    @error('area_sqft') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label">Type</label>
                    <select wire:model="type" class="form-select">
                        <option value="residential">Residential</option>
                        <option value="commercial">Commercial</option>
                        <option value="parking">Parking</option>
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label">Status</label>
                    <select wire:model="status" class="form-select">
                        <option value="vacant">Vacant</option>
                        <option value="occupied">Occupied</option>
                    </select>
                </div>

                <div class="form-actions span-full">
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Save Flat</span>
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
                    <th class="text-left px-5 py-3">Flat</th>
                    <th class="px-5 py-3">Block</th>
                    <th class="px-5 py-3">Floor</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($flats as $f)
                    <tr class="border-t border-slate-100 hover:bg-slate-50" wire:key="flat-{{ $f->id }}">
                        <td class="px-5 py-4 font-semibold">{{ $f->label }}</td>
                        <td class="px-5 py-4">{{ $f->block ?? $f->blockRelation?->name ?? '—' }}</td>
                        <td class="px-5 py-4">{{ $f->floor ?? '—' }}</td>
                        <td class="px-5 py-4 capitalize">{{ $f->type }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $f->status === 'occupied' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $f->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right space-x-3">
                            <button type="button" wire:click="edit({{ $f->id }})" class="text-indigo-600 text-sm font-medium">Edit</button>
                            <button type="button" wire:click="delete({{ $f->id }})" wire:confirm="Delete this flat?" class="text-red-600 text-sm font-medium">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No flats yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t px-5 py-4">{{ $flats->links() }}</div>
    </div>
</div>
