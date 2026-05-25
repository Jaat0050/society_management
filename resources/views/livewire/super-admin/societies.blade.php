<div>
    <div class="flex flex-wrap items-end gap-4 justify-between mb-6">
        <div class="form-field">
            <label class="form-label">Search Societies</label>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Type society name or city..." class="search-box form-control">
        </div>
        <button wire:click="create" type="button" class="btn-primary">+ Add Society</button>
    </div>

    @if ($showForm)
        <div class="form-card mb-6">
            <h3 class="form-card-title">{{ $editingId ? 'Edit Society' : 'New Society' }}</h3>
            <p class="form-card-subtitle">Fill in the society details below. Fields marked with * are required.</p>

            <form wire:submit.prevent="save" class="form-grid">
                <div class="form-field">
                    <label class="form-label field-required">Society Name</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="e.g. Green Valley Residency">
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">Registration Number</label>
                    <input type="text" wire:model="registration_no" class="form-control" placeholder="e.g. SOC-2024-001">
                </div>

                <div class="form-field span-full">
                    <label class="form-label field-required">Full Address</label>
                    <textarea wire:model="address" rows="3" class="form-control" placeholder="Street, area, landmark..."></textarea>
                    @error('address')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label field-required">City</label>
                    <input type="text" wire:model="city" class="form-control" placeholder="e.g. Mumbai">
                    @error('city')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">State</label>
                    <input type="text" wire:model="state" class="form-control" placeholder="e.g. Maharashtra">
                </div>

                <div class="form-field">
                    <label class="form-label">Pincode</label>
                    <input type="text" wire:model="pincode" class="form-control" placeholder="e.g. 400001">
                </div>

                <div class="form-field">
                    <label class="form-label">Phone</label>
                    <input type="tel" wire:model="phone" class="form-control" placeholder="e.g. 02212345678">
                </div>

                <div class="form-field">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="email" class="form-control" placeholder="e.g. info@society.com">
                </div>

                <div class="form-field">
                    <label class="form-label">Subscription Plan</label>
                    <select wire:model="subscription_plan_id" class="form-select">
                        <option value="">— Select Plan —</option>
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (₹{{ number_format($p->price, 0) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label">Subscription Status</label>
                    <select wire:model="subscription_status" class="form-select">
                        <option value="trial">Trial</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="form-actions span-full">
                    <button type="submit" class="btn-primary">Save Society</button>
                    <button type="button" wire:click="resetForm" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="text-left px-5 py-3">Society</th>
                    <th class="text-left px-5 py-3">City</th>
                    <th class="text-left px-5 py-3">Plan</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($societies as $s)
                    <tr class="border-t border-slate-100 hover:bg-slate-50/80" wire:key="s-{{ $s->id }}">
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $s->name }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $s->city }}</td>
                        <td class="px-5 py-4">{{ $s->subscriptionPlan?->name ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if($s->blocked_at)
                                <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">Blocked</span>
                            @else
                                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 capitalize">{{ $s->subscription_status }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right space-x-3">
                            <a href="{{ route('super-admin.preview.society', $s->id) }}" class="text-cyan-600 hover:text-cyan-800 font-medium text-sm">Preview</a>
                            <button type="button" wire:click="edit({{ $s->id }})" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Edit</button>
                            <button type="button" wire:click="toggleBlock({{ $s->id }})" class="text-amber-600 hover:text-amber-800 font-medium text-sm">{{ $s->blocked_at ? 'Unblock' : 'Block' }}</button>
                            <button type="button" wire:click="delete({{ $s->id }})" wire:confirm="Delete this society?" class="text-red-600 hover:text-red-800 font-medium text-sm">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-slate-100 px-5 py-4">{{ $societies->links() }}</div>
    </div>
</div>
