import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'api_screen.dart';
import '../../core/theme.dart';
import '../../providers/auth_provider.dart';
import '../../utils/data_parser.dart';
import '../../widgets/detail_widgets.dart';
import '../../widgets/form_ui.dart';
import '../../widgets/ui_kit.dart';
import 'gate_pass_screen.dart';

// --- Family ---

class FamilyMembersScreen extends StatefulWidget {
  const FamilyMembersScreen({super.key});

  @override
  State<FamilyMembersScreen> createState() => _FamilyMembersScreenState();
}

class _FamilyMembersScreenState extends State<FamilyMembersScreen> {
  final _name = TextEditingController();
  final _relation = TextEditingController();
  final _phone = TextEditingController();
  bool saving = false;

  @override
  void initState() {
    super.initState();
    context.read<AuthProvider>().refreshUser();
  }

  Future<void> _add() async {
    if (_name.text.trim().isEmpty || _relation.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Name and relation required')));
      return;
    }
    setState(() => saving = true);
    try {
      await context.read<AuthProvider>().authService.client.post('/profile/family', body: {
        'name': _name.text.trim(),
        'relation': _relation.text.trim(),
        if (_phone.text.isNotEmpty) 'phone': _phone.text.trim(),
      });
      await context.read<AuthProvider>().refreshUser();
      _name.clear();
      _relation.clear();
      _phone.clear();
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Family member added')));
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    }
    if (mounted) setState(() => saving = false);
  }

  @override
  Widget build(BuildContext context) {
    final family = context.watch<AuthProvider>().user?.family ?? [];

    return Scaffold(
      body: Column(
        children: [
          PageHeader(title: 'Family members', subtitle: '${family.length} registered'),
          Expanded(
            child: RefreshIndicator(
              onRefresh: () => context.read<AuthProvider>().refreshUser(),
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  if (family.isEmpty)
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 24),
                      child: Center(child: Text('No family members yet', style: TextStyle(color: Colors.grey.shade600))),
                    ),
                  ...family.map((m) => DataListTile(
                        title: m['name']?.toString() ?? '',
                        subtitle: '${m['relation'] ?? ''}${m['phone'] != null ? ' • ${m['phone']}' : ''}',
                        leading: Icons.family_restroom_rounded,
                      )),
                  FormSection(
                    title: 'Add member',
                    subtitle: 'Register a family member to your flat',
                    icon: Icons.person_add_alt_1_rounded,
                    children: [
                      AppTextField(controller: _name, label: 'Name *', prefixIcon: Icons.person_outline),
                      AppTextField(controller: _relation, label: 'Relation *', prefixIcon: Icons.people_outline, hint: 'e.g. Spouse, Child'),
                      AppTextField(controller: _phone, label: 'Phone', prefixIcon: Icons.phone_outlined, keyboardType: TextInputType.phone),
                      AppPrimaryButton(label: 'Add family member', loading: saving, onPressed: _add, icon: Icons.check_rounded),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// --- Vehicles ---

class VehiclesScreen extends StatefulWidget {
  const VehiclesScreen({super.key});

  @override
  State<VehiclesScreen> createState() => _VehiclesScreenState();
}

class _VehiclesScreenState extends State<VehiclesScreen> {
  final _no = TextEditingController();
  String _type = 'car';
  bool saving = false;

  @override
  void initState() {
    super.initState();
    context.read<AuthProvider>().refreshUser();
  }

  Future<void> _add() async {
    if (_no.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Vehicle number required')));
      return;
    }
    setState(() => saving = true);
    try {
      await context.read<AuthProvider>().authService.client.post('/profile/vehicle', body: {
        'vehicle_no': _no.text.trim().toUpperCase(),
        'type': _type,
      });
      await context.read<AuthProvider>().refreshUser();
      _no.clear();
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Vehicle registered')));
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    }
    if (mounted) setState(() => saving = false);
  }

  @override
  Widget build(BuildContext context) {
    final vehicles = context.watch<AuthProvider>().user?.vehicles ?? [];

    return Scaffold(
      body: Column(
        children: [
          PageHeader(title: 'Vehicles', subtitle: '${vehicles.length} registered'),
          Expanded(
            child: RefreshIndicator(
              onRefresh: () => context.read<AuthProvider>().refreshUser(),
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  if (vehicles.isEmpty)
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 24),
                      child: Center(child: Text('No vehicles registered', style: TextStyle(color: Colors.grey.shade600))),
                    ),
                  ...vehicles.map((v) => DataListTile(
                        title: v['vehicle_no']?.toString() ?? '',
                        subtitle: statusLabel(v['type']?.toString()),
                        leading: Icons.directions_car_rounded,
                      )),
                  FormSection(
                    title: 'Register vehicle',
                    subtitle: 'Add car, bike or other vehicle',
                    icon: Icons.directions_car_outlined,
                    children: [
                      AppTextField(
                        controller: _no,
                        label: 'Vehicle number *',
                        prefixIcon: Icons.pin_outlined,
                        textCapitalization: TextCapitalization.characters,
                        hint: 'e.g. DL01AB1234',
                      ),
                      AppDropdownField<String>(
                        value: _type,
                        label: 'Vehicle type',
                        prefixIcon: Icons.category_outlined,
                        items: const [
                          DropdownMenuItem(value: 'car', child: Text('Car')),
                          DropdownMenuItem(value: 'bike', child: Text('Bike')),
                          DropdownMenuItem(value: 'other', child: Text('Other')),
                        ],
                        onChanged: (v) => setState(() => _type = v ?? 'car'),
                      ),
                      AppPrimaryButton(label: 'Add vehicle', loading: saving, onPressed: _add, icon: Icons.add_rounded),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// --- Documents ---

class DocumentsScreen extends StatelessWidget {
  const DocumentsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Documents', subtitle: 'ID proof & society records'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                DetailCard(
                  title: 'ID proof',
                  children: [
                    InfoRow(label: 'Type', value: user?.idProofType ?? 'Not set', icon: Icons.badge_outlined),
                    InfoRow(label: 'Number', value: user?.idProofNo ?? 'Not set', icon: Icons.numbers_outlined),
                    const SizedBox(height: 8),
                    Text('Update ID proof from Profile → member settings in admin panel or contact society office.', style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
                  ],
                ),
                DataListTile(title: 'Maintenance receipts', subtitle: 'View under Payment History', leading: Icons.receipt_long_rounded, onTap: () {}),
                DataListTile(title: 'Visitor gate passes', subtitle: 'QR passes from Visitors module', leading: Icons.qr_code_rounded, onTap: () {}),
                DataListTile(title: 'Complaint records', subtitle: 'Track in Complaints module', leading: Icons.report_problem_outlined, onTap: () {}),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// --- Bills ---

class BillDetailScreen extends StatelessWidget {
  const BillDetailScreen({super.key, required this.bill});

  final Map<String, dynamic> bill;

  @override
  Widget build(BuildContext context) {
    final canPay = context.watch<AuthProvider>().paymentGatewayEnabled;

    return Scaffold(
      appBar: AppBar(title: const Text('Bill details')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text(bill['title']?.toString() ?? bill['bill_month']?.toString() ?? 'Bill', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          StatusChip(status: statusLabel(bill['status']?.toString())),
          const SizedBox(height: 20),
          DetailCard(
            title: 'Amount breakdown',
            children: [
              InfoRow(label: 'Base amount', value: formatMoney(bill['amount']), icon: Icons.payments_outlined),
              InfoRow(label: 'Penalty', value: formatMoney(bill['penalty_amount'] ?? 0), icon: Icons.warning_amber_outlined),
              InfoRow(label: 'Total due', value: formatMoney(bill['total_due'] ?? bill['amount']), icon: Icons.account_balance_wallet_outlined),
              InfoRow(label: 'Due date', value: formatDate(bill['due_date']), icon: Icons.event_outlined),
              if (bill['paid_at'] != null) InfoRow(label: 'Paid on', value: formatDate(bill['paid_at'], withTime: true), icon: Icons.check_circle_outline),
            ],
          ),
          if (!canPay && bill['status'] != 'paid')
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(color: AppTheme.accent.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
              child: const Row(
                children: [
                  Icon(Icons.info_outline, color: AppTheme.accent),
                  SizedBox(width: 10),
                  Expanded(child: Text('Pay at society office — online payment is disabled', style: TextStyle(fontSize: 13))),
                ],
              ),
            ),
        ],
      ),
    );
  }
}

class BillsListScreen extends StatelessWidget {
  const BillsListScreen({super.key, required this.path});

  final String path;

  @override
  Widget build(BuildContext context) {
    final canPay = context.watch<AuthProvider>().paymentGatewayEnabled;

    return ApiScreen(
      title: 'Maintenance Bills',
      path: path,
      header: canPay
          ? null
          : Container(
              margin: const EdgeInsets.fromLTRB(16, 0, 16, 0),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(color: AppTheme.accent.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(12)),
              child: const Row(
                children: [
                  Icon(Icons.info_outline, color: AppTheme.accent),
                  SizedBox(width: 8),
                  Expanded(child: Text('Online payment disabled — pay at society office', style: TextStyle(fontSize: 13))),
                ],
              ),
            ),
      itemBuilder: (c, item) => DataListTile(
        title: '${item['bill_month'] ?? item['title']} — ${formatMoney(item['total_due'] ?? item['amount'])}',
        subtitle: 'Due: ${formatDate(item['due_date'])}',
        status: item['status']?.toString(),
        leading: Icons.receipt_long_rounded,
        trailing: const Icon(Icons.chevron_right),
        onTap: () => Navigator.push(c, MaterialPageRoute(builder: (_) => BillDetailScreen(bill: item))),
      ),
    );
  }
}

// --- Visitors ---

class VisitorDetailScreen extends StatelessWidget {
  const VisitorDetailScreen({super.key, required this.visitor, required this.basePath});

  final Map<String, dynamic> visitor;
  final String basePath;

  Future<void> _openPass(BuildContext context) async {
    try {
      final j = await context.read<AuthProvider>().authService.client.get('$basePath/${visitor['id']}/pass');
      if (!context.mounted) return;
      Navigator.push(context, MaterialPageRoute(builder: (_) => GatePassScreen.fromApi(parseMap(j['data']))));
    } catch (e) {
      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Visitor details')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text(visitor['visitor_name']?.toString() ?? '', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          StatusChip(status: statusLabel(visitor['status']?.toString())),
          const SizedBox(height: 16),
          DetailCard(
            title: 'Visit info',
            children: [
              InfoRow(label: 'Type', value: categoryLabel(visitor['visit_type']?.toString()), icon: Icons.person_outline),
              InfoRow(label: 'Phone', value: visitor['phone']?.toString() ?? '—', icon: Icons.phone_outlined),
              InfoRow(label: 'Purpose', value: visitor['purpose']?.toString() ?? '—', icon: Icons.notes_outlined),
              InfoRow(label: 'Pass code', value: visitor['pass_code']?.toString() ?? '—', icon: Icons.qr_code_outlined),
              InfoRow(label: 'Created', value: formatDate(visitor['created_at'], withTime: true), icon: Icons.calendar_today_outlined),
            ],
          ),
          FilledButton.icon(
            onPressed: () => _openPass(context),
            icon: const Icon(Icons.qr_code_2),
            label: const Text('View gate pass / QR'),
          ),
        ],
      ),
    );
  }
}

class PreRegisterVisitorScreen extends StatefulWidget {
  const PreRegisterVisitorScreen({super.key});

  @override
  State<PreRegisterVisitorScreen> createState() => _PreRegisterVisitorScreenState();
}

class _PreRegisterVisitorScreenState extends State<PreRegisterVisitorScreen> {
  final _name = TextEditingController();
  final _phone = TextEditingController();
  final _purpose = TextEditingController();
  String _type = 'guest';
  bool saving = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pre-register visitor')),
      body: FormPageBody(
        children: [
          FormSection(
            title: 'Visitor details',
            subtitle: 'A gate pass & QR will be generated',
            icon: Icons.qr_code_rounded,
            children: [
              AppTextField(controller: _name, label: 'Visitor name *', prefixIcon: Icons.person_outline),
              AppTextField(controller: _phone, label: 'Phone', prefixIcon: Icons.phone_outlined, keyboardType: TextInputType.phone),
              AppTextField(controller: _purpose, label: 'Purpose', prefixIcon: Icons.notes_outlined, hint: 'Meeting, delivery, etc.'),
              AppDropdownField<String>(
                value: _type,
                label: 'Visit type',
                prefixIcon: Icons.category_outlined,
                items: ['guest', 'delivery', 'cab', 'domestic_help', 'vendor', 'other']
                    .map((e) => DropdownMenuItem(value: e, child: Text(categoryLabel(e))))
                    .toList(),
                onChanged: (v) => setState(() => _type = v ?? 'guest'),
              ),
              AppPrimaryButton(
                label: 'Create gate pass',
                loading: saving,
                icon: Icons.qr_code_2_rounded,
                onPressed: saving
                    ? null
                    : () async {
                        if (_name.text.trim().isEmpty) return;
                        setState(() => saving = true);
                        try {
                          final j = await context.read<AuthProvider>().authService.client.post('/resident/visitors', body: {
                            'visitor_name': _name.text.trim(),
                            if (_phone.text.isNotEmpty) 'phone': _phone.text.trim(),
                            'visit_type': _type,
                            if (_purpose.text.isNotEmpty) 'purpose': _purpose.text.trim(),
                          });
                          if (context.mounted) {
                            Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => GatePassScreen.fromApi(parseMap(j['data']))));
                          }
                        } catch (e) {
                          if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                        }
                        if (mounted) setState(() => saving = false);
                      },
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class VisitorsListScreen extends StatelessWidget {
  const VisitorsListScreen({super.key, required this.path});

  final String path;

  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'Visitors',
      path: path,
      itemBuilder: (c, item) => DataListTile(
        title: item['visitor_name']?.toString() ?? 'Visitor',
        subtitle: '${categoryLabel(item['visit_type']?.toString())} • ${formatDate(item['created_at'])}',
        status: item['status']?.toString(),
        leading: Icons.qr_code_rounded,
        trailing: const Icon(Icons.chevron_right),
        onTap: () => Navigator.push(c, MaterialPageRoute(builder: (_) => VisitorDetailScreen(visitor: item, basePath: path))),
      ),
      floatingAction: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const PreRegisterVisitorScreen())),
        icon: const Icon(Icons.person_add),
        label: const Text('Create gate pass'),
      ),
    );
  }
}

// --- Amenities & Bookings ---

class BookAmenityScreen extends StatefulWidget {
  const BookAmenityScreen({super.key, required this.amenityId, required this.name, this.fee});

  final int amenityId;
  final String name;
  final dynamic fee;

  @override
  State<BookAmenityScreen> createState() => _BookAmenityScreenState();
}

class _BookAmenityScreenState extends State<BookAmenityScreen> {
  DateTime from = DateTime.now().add(const Duration(hours: 2));
  DateTime to = DateTime.now().add(const Duration(hours: 4));
  final _notes = TextEditingController();
  bool saving = false;

  Future<void> _pickDateTime(bool isFrom) async {
    final date = await showDatePicker(context: context, initialDate: isFrom ? from : to, firstDate: DateTime.now(), lastDate: DateTime.now().add(const Duration(days: 60)));
    if (date == null || !mounted) return;
    final time = await showTimePicker(context: context, initialTime: TimeOfDay.fromDateTime(isFrom ? from : to));
    if (time == null) return;
    final dt = DateTime(date.year, date.month, date.day, time.hour, time.minute);
    setState(() {
      if (isFrom) {
        from = dt;
        if (!to.isAfter(from)) to = from.add(const Duration(hours: 2));
      } else {
        to = dt;
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Book ${widget.name}')),
      body: FormPageBody(
        children: [
          FormSection(
            title: 'Booking slot',
            subtitle: widget.fee != null ? 'Fee: ${formatMoney(widget.fee)}' : 'Select date and time',
            icon: Icons.event_available_outlined,
            children: [
              Card(
                margin: EdgeInsets.zero,
                color: AppTheme.surfaceLight.withValues(alpha: 0.5),
                child: Column(
                  children: [
                    ListTile(
                      leading: const Icon(Icons.play_arrow_rounded, color: AppTheme.primary),
                      title: const Text('From'),
                      subtitle: Text(formatDate(from.toIso8601String(), withTime: true)),
                      trailing: const Icon(Icons.edit_calendar_outlined),
                      onTap: () => _pickDateTime(true),
                    ),
                    const Divider(height: 1),
                    ListTile(
                      leading: const Icon(Icons.stop_rounded, color: AppTheme.primary),
                      title: const Text('To'),
                      subtitle: Text(formatDate(to.toIso8601String(), withTime: true)),
                      trailing: const Icon(Icons.edit_calendar_outlined),
                      onTap: () => _pickDateTime(false),
                    ),
                  ],
                ),
              ),
              AppTextField(controller: _notes, label: 'Notes (optional)', prefixIcon: Icons.notes_outlined, maxLines: 2),
              AppPrimaryButton(
                label: 'Request booking',
                loading: saving,
                icon: Icons.check_rounded,
                onPressed: saving
                    ? null
                    : () async {
                        setState(() => saving = true);
                        try {
                          await context.read<AuthProvider>().authService.client.post('/resident/bookings', body: {
                            'amenity_id': widget.amenityId,
                            'booked_from': from.toIso8601String(),
                            'booked_to': to.toIso8601String(),
                            if (_notes.text.isNotEmpty) 'notes': _notes.text,
                          });
                          if (context.mounted) {
                            ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Booking requested — pending approval')));
                            Navigator.pop(context);
                          }
                        } catch (e) {
                          if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                        }
                        if (mounted) setState(() => saving = false);
                      },
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class AmenitiesListScreen extends StatelessWidget {
  const AmenitiesListScreen({super.key, required this.path});

  final String path;

  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'Amenities',
      path: path,
      itemBuilder: (c, item) => DataListTile(
        title: item['name']?.toString() ?? 'Amenity',
        subtitle: '${item['booking_fee'] != null ? formatMoney(item['booking_fee']) : 'Free'} • ${item['capacity'] ?? ''} capacity',
        leading: Icons.pool_rounded,
        trailing: const Icon(Icons.chevron_right),
        onTap: () => Navigator.push(
          c,
          MaterialPageRoute(
            builder: (_) => BookAmenityScreen(amenityId: item['id'] as int, name: item['name']?.toString() ?? '', fee: item['booking_fee']),
          ),
        ),
      ),
    );
  }
}

class BookingsListScreen extends StatefulWidget {
  const BookingsListScreen({super.key, required this.path});

  final String path;

  @override
  State<BookingsListScreen> createState() => _BookingsListScreenState();
}

class _BookingsListScreenState extends State<BookingsListScreen> {
  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'My Bookings',
      path: widget.path,
      itemBuilder: (c, item) => DataListTile(
        title: item['amenity']?['name']?.toString() ?? 'Booking',
        subtitle: '${formatDate(item['booked_from'], withTime: true)} — ${formatDate(item['booked_to'], withTime: true)}',
        status: item['status']?.toString(),
        leading: Icons.pool_rounded,
        onTap: () {
          if (['pending', 'approved'].contains(item['status'])) {
            showModalBottomSheet(
              context: c,
              builder: (ctx) => SafeArea(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    ListTile(
                      leading: const Icon(Icons.cancel_outlined, color: AppTheme.danger),
                      title: const Text('Cancel booking'),
                      onTap: () async {
                        Navigator.pop(ctx);
                        try {
                          await c.read<AuthProvider>().authService.client.delete('${widget.path}/${item['id']}');
                          if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(const SnackBar(content: Text('Booking cancelled')));
                        } catch (e) {
                          if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(SnackBar(content: Text('$e')));
                        }
                      },
                    ),
                  ],
                ),
              ),
            );
          }
        },
      ),
    );
  }
}

// --- Notices / Events detail ---

class NoticeDetailScreen extends StatelessWidget {
  const NoticeDetailScreen({super.key, required this.notice});

  final Map<String, dynamic> notice;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Notice')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text(notice['title']?.toString() ?? '', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          Text(formatDate(notice['published_at'] ?? notice['created_at'], withTime: true), style: TextStyle(color: Colors.grey.shade600, fontSize: 13)),
          const SizedBox(height: 16),
          Text(notice['body']?.toString() ?? '', style: const TextStyle(height: 1.5, fontSize: 15)),
        ],
      ),
    );
  }
}

class EventDetailScreen extends StatelessWidget {
  const EventDetailScreen({super.key, required this.event});

  final Map<String, dynamic> event;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Event')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text(event['title']?.toString() ?? '', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          DetailCard(
            title: 'Event details',
            children: [
              InfoRow(label: 'Starts', value: formatDate(event['starts_at'], withTime: true), icon: Icons.schedule_outlined),
              InfoRow(label: 'Ends', value: formatDate(event['ends_at'], withTime: true), icon: Icons.schedule_outlined),
              InfoRow(label: 'Location', value: event['location']?.toString() ?? '—', icon: Icons.location_on_outlined),
            ],
          ),
          if (event['description'] != null) Text(event['description'].toString(), style: const TextStyle(height: 1.5)),
        ],
      ),
    );
  }
}

class NoticesListScreen extends StatelessWidget {
  const NoticesListScreen({super.key, required this.path});

  final String path;

  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'Notices',
      path: path,
      itemBuilder: (c, item) => DataListTile(
        title: item['title']?.toString() ?? 'Notice',
        subtitle: formatDate(item['published_at'] ?? item['created_at']),
        leading: Icons.campaign_rounded,
        trailing: const Icon(Icons.chevron_right),
        onTap: () => Navigator.push(c, MaterialPageRoute(builder: (_) => NoticeDetailScreen(notice: item))),
      ),
    );
  }
}

class EventsListScreen extends StatelessWidget {
  const EventsListScreen({super.key, required this.path});

  final String path;

  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'Events',
      path: path,
      itemBuilder: (c, item) => DataListTile(
        title: item['title']?.toString() ?? 'Event',
        subtitle: '${formatDate(item['starts_at'], withTime: true)} • ${item['location'] ?? ''}',
        leading: Icons.event_rounded,
        trailing: const Icon(Icons.chevron_right),
        onTap: () => Navigator.push(c, MaterialPageRoute(builder: (_) => EventDetailScreen(event: item))),
      ),
    );
  }
}
