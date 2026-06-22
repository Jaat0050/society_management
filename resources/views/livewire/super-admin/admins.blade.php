<div>
    <div class="flex justify-end mb-6">
        <button wire:click="$set('showForm', true)" type="button" class="btn-primary">+ Assign Admin</button>
    </div>

    @if ($showForm)
        <div class="form-card mb-6">
            <h3 class="form-card-title">Assign Society Admin</h3>
            <p class="form-card-subtitle">Create a new admin user for a society.</p>
            <form wire:submit.prevent="save" class="form-grid">
                <div class="form-field">
                    <label class="form-label field-required">Full Name</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="Admin name">
                </div>
                <div class="form-field">
                    <label class="form-label field-required">Email</label>
                    <input type="email" wire:model="email" class="form-control" placeholder="admin@society.com">
                </div>
                <div class="form-field">
                    <label class="form-label">Phone</label>
                    <input type="tel" wire:model="phone" class="form-control" placeholder="Mobile number">
                </div>
                <div class="form-field">
                    <label class="form-label field-required">Password</label>
                    <input type="password" wire:model="password" class="form-control" placeholder="Min 6 characters">
                </div>
                <div class="form-field span-full">
                    <label class="form-label field-required">Society</label>
                    <select wire:model="society_id" class="form-select">
                        <option value="">— Select Society —</option>
                        @foreach($societies as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-actions span-full">
                    <button type="submit" class="btn-primary">Create Admin</button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="text-left px-5 py-3">Name</th>
                    <th class="text-left px-5 py-3">Email</th>
                    <th class="text-left px-5 py-3">Society</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $a)
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-5 py-4 font-medium">{{ $a->name }}</td>
                    <td class="px-5 py-4">{{ $a->email }}</td>
                    <td class="px-5 py-4 text-indigo-600">{{ $a->society?->name }}</td>
                    <td class="px-5 py-4">{{ $a->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="px-5 py-4 text-right">
                        <button type="button" wire:click="toggleActive({{ $a->id }})" class="text-indigo-600 text-sm font-medium">{{ $a->is_active ? 'Deactivate' : 'Activate' }}</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t px-5 py-4">{{ $admins->links() }}</div>
    </div>
</div>
