@if ($tab === 'members')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Flat</th><th class="px-4 py-3 text-left">Phone</th><th class="px-4 py-3 text-left">Type</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t hover:bg-slate-50"><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3">{{ $row->flat?->label ?? '—' }}</td><td class="px-4 py-3">{{ $row->phone }}</td><td class="px-4 py-3 capitalize">{{ $row->member_type }}</td></tr>@empty<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'flats')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Flat</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Block</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Area</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t hover:bg-slate-50"><td class="px-4 py-3 font-medium">{{ $row->label }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3">{{ $row->block ?? '—' }}</td><td class="px-4 py-3 capitalize">{{ $row->status }}</td><td class="px-4 py-3">{{ $row->area_sqft ?? '—' }}</td></tr>@empty<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'blocks')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Block</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Floors</th><th class="px-4 py-3 text-left">Flats</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3">{{ $row->total_floors }}</td><td class="px-4 py-3">{{ $row->flats_count }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'vehicles')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Vehicle</th><th class="px-4 py-3 text-left">Member</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Sticker</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->vehicle_no }}</td><td class="px-4 py-3">{{ $row->member?->name }}</td><td class="px-4 py-3">{{ $row->type }}</td><td class="px-4 py-3">{{ $row->sticker_no ?? '—' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'staff')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Role</th><th class="px-4 py-3 text-left">Phone</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3 capitalize">{{ $row->role }}</td><td class="px-4 py-3">{{ $row->phone }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'attendance')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Staff</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3">{{ $row->attendance_date->format('d M Y') }}</td><td class="px-4 py-3">{{ $row->staff?->name }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->staff?->society?->name }}</td><td class="px-4 py-3">{{ $row->status }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'salaries')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Month</th><th class="px-4 py-3 text-left">Staff</th><th class="px-4 py-3 text-left">Amount</th><th class="px-4 py-3 text-left">Paid</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3">{{ $row->payment_month }}</td><td class="px-4 py-3">{{ $row->staff?->name }}</td><td class="px-4 py-3">₹{{ number_format($row->amount,0) }}</td><td class="px-4 py-3">{{ $row->paid_at->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'vendors')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Vendor</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Service</th><th class="px-4 py-3 text-left">Contract End</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3">{{ $row->service_type }}</td><td class="px-4 py-3">{{ $row->contract_end?->format('d M Y') ?? '—' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif (in_array($tab, ['admins', 'guards', 'residents_users']))
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Email</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Phone</th><th class="px-4 py-3 text-left">Active</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3">{{ $row->email }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name ?? '—' }}</td><td class="px-4 py-3">{{ $row->phone ?? '—' }}</td><td class="px-4 py-3">{{ $row->is_active ? 'Yes' : 'No' }}</td></tr>@empty<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'visitors')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Visitor</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Flat</th><th class="px-4 py-3 text-left">Pass</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Entry/Exit</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->visitor_name }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3">{{ $row->flat?->label ?? '—' }}</td><td class="px-4 py-3 font-mono text-xs">{{ $row->pass_code }}</td><td class="px-4 py-3">{{ $row->status }}</td><td class="px-4 py-3 text-xs">{{ $row->entry_at?->format('d M H:i') ?? '—' }} / {{ $row->exit_at?->format('H:i') ?? '—' }}</td></tr>@empty<tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'delivery')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Company</th><th class="px-4 py-3 text-left">Verified</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3">{{ $row->company }}</td><td class="px-4 py-3">{{ $row->is_verified ? 'Yes' : 'No' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'complaints')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Subject</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Assigned</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->subject }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3 text-xs">{{ $row->assignedStaff?->name ?? $row->assignedVendor?->name ?? '—' }}</td><td class="px-4 py-3">{{ $row->status }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'notices')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Title</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">Published</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->title }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3">{{ $row->published_at?->format('d M Y') ?? '—' }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'events')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Event</th><th class="px-4 py-3 text-left">Society</th><th class="px-4 py-3 text-left">When</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->title }}</td><td class="px-4 py-3 text-indigo-600">{{ $row->society?->name }}</td><td class="px-4 py-3">{{ $row->starts_at->format('d M Y H:i') }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'notifications')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Title</th><th class="px-4 py-3 text-left">Channel</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Sent</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3">{{ $row->title }}</td><td class="px-4 py-3">{{ $row->channel }}</td><td class="px-4 py-3">{{ $row->status }}</td><td class="px-4 py-3">{{ $row->sent_at?->format('d M Y') ?? '—' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'emergency')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Alert</th><th class="px-4 py-3 text-left">Severity</th><th class="px-4 py-3 text-left">Active</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium text-red-700">{{ $row->title }}</td><td class="px-4 py-3">{{ $row->severity }}</td><td class="px-4 py-3">{{ $row->is_active ? 'Yes' : 'No' }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'bills')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Bill</th><th class="px-4 py-3 text-left">Flat</th><th class="px-4 py-3 text-left">Amount</th><th class="px-4 py-3 text-left">Penalty</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3">{{ $row->title }}</td><td class="px-4 py-3">{{ $row->flat?->label }}</td><td class="px-4 py-3">₹{{ number_format($row->amount,0) }}</td><td class="px-4 py-3">₹{{ number_format($row->penalty_amount ?? 0,0) }}</td><td class="px-4 py-3">{{ $row->status }}</td></tr>@empty<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'payments')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Txn</th><th class="px-4 py-3 text-left">Gateway</th><th class="px-4 py-3 text-left">Amount</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-mono text-xs">{{ $row->transaction_id }}</td><td class="px-4 py-3">{{ $row->gateway }}</td><td class="px-4 py-3">₹{{ number_format($row->amount,0) }}</td><td class="px-4 py-3">{{ $row->status }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'expenses')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Category</th><th class="px-4 py-3 text-left">Amount</th><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Vendor</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3">{{ $row->category }}</td><td class="px-4 py-3">₹{{ number_format($row->amount,0) }}</td><td class="px-4 py-3">{{ $row->expense_date->format('d M Y') }}</td><td class="px-4 py-3">{{ $row->vendor?->name ?? '—' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'ledger')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Category</th><th class="px-4 py-3 text-left">Amount</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3">{{ $row->entry_date->format('d M Y') }}</td><td class="px-4 py-3 capitalize">{{ $row->type }}</td><td class="px-4 py-3">{{ $row->category }}</td><td class="px-4 py-3 {{ $row->type==='income'?'text-emerald-600':'text-red-600' }}">₹{{ number_format($row->amount,0) }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'parking')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Slot</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Flat</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->slot_no }}</td><td class="px-4 py-3">{{ $row->type }}</td><td class="px-4 py-3">{{ $row->flat?->label ?? '—' }}</td><td class="px-4 py-3">{{ $row->status }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'parking_passes')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Vehicle</th><th class="px-4 py-3 text-left">Sticker</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Valid Till</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->vehicle_no }}</td><td class="px-4 py-3">{{ $row->sticker_no ?? '—' }}</td><td class="px-4 py-3">{{ $row->pass_type }}</td><td class="px-4 py-3">{{ $row->valid_until?->format('d M Y') ?? '—' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'amenities')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Amenity</th><th class="px-4 py-3 text-left">Rate</th><th class="px-4 py-3 text-left">Bookings</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3">₹{{ number_format($row->hourly_rate,0) }}/hr</td><td class="px-4 py-3">{{ $row->bookings_count }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'bookings')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Amenity</th><th class="px-4 py-3 text-left">Flat</th><th class="px-4 py-3 text-left">From</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3">{{ $row->amenity?->name }}</td><td class="px-4 py-3">{{ $row->flat?->label ?? '—' }}</td><td class="px-4 py-3">{{ $row->booked_from->format('d M H:i') }}</td><td class="px-4 py-3">{{ $row->status }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'security')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Incident</th><th class="px-4 py-3 text-left">Severity</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Date</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->title }}</td><td class="px-4 py-3">{{ $row->severity }}</td><td class="px-4 py-3">{{ $row->status }}</td><td class="px-4 py-3">{{ $row->occurred_at?->format('d M Y') ?? '—' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'cctv')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Camera</th><th class="px-4 py-3 text-left">Location</th><th class="px-4 py-3 text-left">Active</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $row->name }}</td><td class="px-4 py-3">{{ $row->location }}</td><td class="px-4 py-3">{{ $row->is_active ? 'Yes' : 'No' }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>

@elseif ($tab === 'sos')
<table class="w-full text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3 text-left">Flat</th><th class="px-4 py-3 text-left">Message</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Time</th></tr></thead>
<tbody>@forelse ($data as $row)<tr class="border-t"><td class="px-4 py-3">{{ $row->flat?->label ?? '—' }}</td><td class="px-4 py-3 text-red-700">{{ \Illuminate\Support\Str::limit($row->message, 40) }}</td><td class="px-4 py-3 font-semibold {{ $row->status==='active'?'text-red-600':'' }}">{{ $row->status }}</td><td class="px-4 py-3">{{ $row->created_at->format('d M H:i') }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No records</td></tr>@endforelse</tbody></table>
@endif
