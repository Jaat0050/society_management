<div>
    @include('livewire.partials.alerts')

    <div class="flex justify-end mb-6">
        <button type="button" wire:click="$set('showForm', true)" class="btn-primary">+ Add Resident</button>
    </div>

    @if ($showForm)
        <div class="form-card mb-6">
            <h3 class="form-card-title">Add Resident</h3>
            <p class="form-card-subtitle">Register owner, tenant, or family member.</p>

            <form wire:submit.prevent="save" class="form-grid">
                <div class="form-field">
                    <label class="form-label field-required">Full Name</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="Resident name">
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label field-required">Phone</label>
                    <input type="tel" wire:model="phone" class="form-control" placeholder="10-digit mobile">
                    @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="email" class="form-control" placeholder="email@example.com">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label">Flat</label>
                    <select wire:model="flat_id" class="form-select">
                        <option value="">— No flat assigned —</option>
                        @foreach ($flats as $f)
                            <option value="{{ $f->id }}">{{ $f->label }} ({{ $f->status }})</option>
                        @endforeach
                    </select>
                    @error('flat_id') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label">Member Type</label>
                    <select wire:model="member_type" class="form-select">
                        <option value="owner">Owner</option>
                        <option value="tenant">Tenant</option>
                        <option value="family">Family</option>
                    </select>
                </div>

                <div class="form-actions span-full">
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Save Resident</span>
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
                    <th class="px-5 py-3">Phone</th>
                    <th class="px-5 py-3">Flat</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="text-right px-5 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($residents as $r)
                    <tr class="border-t border-slate-100 hover:bg-slate-50" wire:key="member-{{ $r->id }}">
                        <td class="px-5 py-4 font-semibold">{{ $r->name }}</td>
                        <td class="px-5 py-4">{{ $r->phone }}</td>
                        <td class="px-5 py-4">{{ $r->flat?->label ?? '—' }}</td>
                        <td class="px-5 py-4 capitalize">{{ $r->member_type }}</td>
                        <td class="px-5 py-4 text-right space-x-2">
                            <a href="{{ route('admin.residents.profile', $r->id) }}" wire:navigate class="text-indigo-600 text-sm font-medium">Manage</a>
                            <button type="button" wire:click="delete({{ $r->id }})" wire:confirm="Remove this resident?" class="text-red-600 text-sm font-medium">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No residents yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t px-5 py-4">{{ $residents->links() }}</div>
    </div>
</div>
