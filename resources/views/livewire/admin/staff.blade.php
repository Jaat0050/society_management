<div>
    <div class="flex justify-end mb-4"><button wire:click="$set('showForm', true)" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">+ Add Staff</button></div>
    @if($showForm)<form wire:submit.prevent="save" class="bg-white border rounded-xl p-5 mb-4 flex flex-wrap gap-3">
        <input wire:model="name" placeholder="Name" class="rounded-lg border-slate-300 text-sm flex-1 min-w-[140px]">
        <input wire:model="phone" placeholder="Phone" class="rounded-lg border-slate-300 text-sm flex-1 min-w-[140px]">
        <select wire:model="role" class="rounded-lg border-slate-300 text-sm"><option value="guard">Guard</option><option value="cleaner">Cleaner</option><option value="manager">Manager</option><option value="other">Other</option></select>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Save</button>
    </form>@endif
    <div class="bg-white border rounded-xl"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Role</th><th class="px-4 py-3 text-right">Action</th></tr></thead>
    <tbody>@foreach($staffList as $s)<tr class="border-t"><td class="px-4 py-3">{{ $s->name }}</td><td class="px-4 py-3">{{ $s->phone }}</td><td class="px-4 py-3 capitalize">{{ $s->role }}</td>
    <td class="px-4 py-3 text-right"><button wire:click="delete({{ $s->id }})" class="text-red-600 text-sm">Remove</button></td></tr>@endforeach</tbody></table></div>
</div>
