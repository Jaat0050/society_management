import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/theme.dart';
import '../../providers/auth_provider.dart';
import '../../utils/data_parser.dart';
import '../../widgets/ui_kit.dart';
import 'api_screen.dart';
import 'chat_screen.dart';
import 'gate_pass_screen.dart';

class ModuleScreens {
  static String titleFor(String route) => _titles[route] ?? 'Module';

  static final _titles = {
    '/home': 'Home',
    '/profile': 'Profile',
    '/family': 'Family',
    '/vehicles': 'Vehicles',
    '/documents': 'Documents',
    '/bills': 'Maintenance Bills',
    '/payments': 'Payment History',
    '/complaints': 'Complaints',
    '/visitors': 'Visitors',
    '/notices': 'Notices',
    '/events': 'Events',
    '/amenities': 'Amenities',
    '/bookings': 'My Bookings',
    '/sos': 'Emergency SOS',
    '/emergency': 'Emergency Contacts',
    '/notifications': 'Alerts',
    '/chat': 'Community Chat',
    '/gate-pass': 'Gate Pass',
    '/scan': 'Scan QR / OTP',
    '/new-entry': 'New Entry',
    '/inside': 'Visitors Inside',
    '/checkin': 'Check-in',
    '/checkout': 'Check-out',
    '/log': 'Visitor Log',
    '/delivery': 'Delivery Staff',
    '/vehicle': 'Vehicle Verify',
    '/attendance': 'Attendance',
    '/staff-list': 'Staff List',
    '/sos-alerts': 'SOS Alerts',
    '/incidents': 'Report Incident',
    '/tasks': 'Tasks',
    '/schedule': 'Schedule',
    '/inventory': 'Inventory Request',
    '/financial': 'Financial Summary',
    '/reports': 'Reports',
    '/visitor-stats': 'Visitor Stats',
    '/billing': 'Billing',
    '/expenses': 'Expenses',
    '/ledger': 'Ledger',
    '/vendors': 'Vendors',
    '/invoices': 'Invoices',
  };

  static Widget build(String route, String role) {
    final r = _residentPrefix(role);
    switch (route) {
      case '/home':
        return const SizedBox.shrink();
      case '/profile':
        return _ProfileScreen();
      case '/family':
        return _FamilyScreen();
      case '/vehicles':
        return _VehiclesScreen();
      case '/documents':
        return _DocumentsScreen();
      case '/bills':
        return _BillsScreen(path: '$r/bills');
      case '/payments':
        if (role == 'vendor') {
          return ApiScreen(
            title: 'Payment tracking',
            path: '/vendor/payments',
            itemBuilder: (ctx, item) => DataListTile(
              title: item['message']?.toString() ?? 'Payments',
              subtitle: 'Pending: ${item['pending_invoices'] ?? 0}',
              leading: Icons.payment_rounded,
            ),
          );
        }
        return ApiScreen(title: 'Payment History', path: '$r/payments/history', itemBuilder: _paymentTile);
      case '/complaints':
        return _ComplaintsScreen(path: role == 'committee' ? '/committee/complaints' : '$r/complaints');
      case '/visitors':
        return _VisitorsScreen(path: '$r/visitors');
      case '/notices':
        return ApiScreen(title: 'Notices', path: role == 'committee' ? '/committee/notices' : '$r/notices', itemBuilder: _noticeTile);
      case '/events':
        return ApiScreen(title: 'Events', path: role == 'committee' ? '/committee/events' : '$r/events', itemBuilder: _eventTile);
      case '/amenities':
        return _AmenitiesScreen(path: '$r/amenities');
      case '/bookings':
        return ApiScreen(title: 'Bookings', path: '$r/bookings', itemBuilder: _bookingTile);
      case '/sos':
        return _SosScreen(path: '$r/sos');
      case '/emergency':
        return _EmergencyScreen(path: '$r/emergency-contacts');
      case '/notifications':
        return ApiScreen(title: 'Society Alerts', path: '/notifications', itemBuilder: _noticeTile);
      case '/chat':
        return const ChatScreen();
      case '/gate-pass':
        return const _GatePassListScreen();
      case '/scan':
        return _GuardScanScreen();
      case '/new-entry':
        return _GuardEntryScreen();
      case '/inside':
        return ApiScreen(title: 'Inside Gate', path: '/guard/inside', itemBuilder: _visitorTile);
      case '/checkin':
        return _GuardActionScreen(title: 'Check-in', action: 'check-in');
      case '/checkout':
        return _GuardActionScreen(title: 'Check-out', action: 'check-out');
      case '/log':
        return ApiScreen(title: 'Visitor Log', path: '/guard/log', itemBuilder: _visitorTile);
      case '/delivery':
        return ApiScreen(title: 'Delivery Staff', path: '/guard/delivery-staff', itemBuilder: _deliveryTile);
      case '/vehicle':
        return _VehicleVerifyScreen();
      case '/attendance':
        return role == 'guard' ? _GuardAttendanceScreen() : _StaffAttendanceScreen();
      case '/staff-list':
        return ApiScreen(title: 'Staff', path: '/guard/staff', itemBuilder: _staffTile);
      case '/sos-alerts':
        return _SosAlertsScreen();
      case '/incidents':
        return _IncidentScreen();
      case '/tasks':
        return _TasksScreen(role: role);
      case '/schedule':
        return _ScheduleScreen(role: role);
      case '/inventory':
        return _InventoryScreen();
      case '/financial':
        return _FinancialScreen();
      case '/reports':
        return _ReportsScreen(role: role);
      case '/visitor-stats':
        return _VisitorStatsScreen();
      case '/billing':
        return ApiScreen(title: 'Billing', path: '/accountant/billing', itemBuilder: _billTile);
      case '/expenses':
        return _ExpensesScreen();
      case '/ledger':
        return ApiScreen(title: 'Ledger', path: '/accountant/ledger', itemBuilder: _ledgerTile);
      case '/vendors':
        return ApiScreen(title: 'Vendors', path: '/accountant/vendors', itemBuilder: _vendorTile);
      case '/invoices':
        return _VendorInvoiceScreen();
      default:
        return Center(child: Text('Module $route'));
    }
  }

  static String _residentPrefix(String role) => (role == 'resident' || role == 'tenant') ? '/resident' : '/resident';

  static Widget _paymentTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: formatMoney(item['amount']),
        subtitle: '${item['payment_method'] ?? ''} • ${item['paid_at'] ?? item['created_at'] ?? ''}',
        status: item['status']?.toString(),
        leading: Icons.payment_rounded,
      );

  static Widget _noticeTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: item['title']?.toString() ?? 'Notice',
        subtitle: item['body']?.toString(),
        leading: Icons.campaign_rounded,
      );

  static Widget _eventTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: item['title']?.toString() ?? 'Event',
        subtitle: '${item['starts_at'] ?? ''} • ${item['location'] ?? ''}',
        leading: Icons.event_rounded,
      );

  static Widget _bookingTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: item['amenity']?['name']?.toString() ?? 'Booking',
        subtitle: '${item['booked_from'] ?? ''} — ${item['booked_to'] ?? ''}',
        status: item['status']?.toString(),
        leading: Icons.pool_rounded,
      );

  static Widget _visitorTile(BuildContext c, Map<String, dynamic> item, {VoidCallback? onTap}) => DataListTile(
        title: item['visitor_name']?.toString() ?? 'Visitor',
        subtitle: '${item['visit_type'] ?? ''} • ${item['pass_code'] ?? ''}',
        status: item['status']?.toString(),
        leading: Icons.qr_code_rounded,
        onTap: onTap,
        trailing: const Icon(Icons.chevron_right),
      );

  static Widget _deliveryTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: item['name']?.toString() ?? 'Staff',
        subtitle: item['company']?.toString(),
        leading: Icons.delivery_dining_rounded,
      );

  static Widget _staffTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: item['name']?.toString() ?? '',
        subtitle: item['role']?.toString(),
        leading: Icons.badge_rounded,
      );

  static Widget _billTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: '${item['bill_month'] ?? item['title']} — ${formatMoney(item['amount'] ?? item['total_due'])}',
        subtitle: 'Flat ${item['flat']?['flat_no'] ?? ''}',
        status: item['status']?.toString(),
        leading: Icons.receipt_long_rounded,
      );

  static Widget _ledgerTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: '${item['type'] ?? ''} — ${formatMoney(item['amount'])}',
        subtitle: item['description']?.toString(),
        leading: Icons.menu_book_rounded,
      );

  static Widget _vendorTile(BuildContext c, Map<String, dynamic> item) => DataListTile(
        title: item['name']?.toString() ?? '',
        subtitle: item['service_type']?.toString(),
        leading: Icons.store_rounded,
      );
}

// --- Profile & family ---

class _ProfileScreen extends StatefulWidget {
  @override
  State<_ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<_ProfileScreen> {
  final _name = TextEditingController();
  final _phone = TextEditingController();
  bool saving = false;

  @override
  void initState() {
    super.initState();
    final u = context.read<AuthProvider>().user;
    _name.text = u?.name ?? '';
    _phone.text = u?.phone ?? '';
  }

  @override
  Widget build(BuildContext context) {
    final u = context.watch<AuthProvider>().user;
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Profile', subtitle: 'Update personal details'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Card(
                  child: ListTile(
                    leading: CircleAvatar(backgroundColor: AppTheme.primary, child: Text(u?.name.isNotEmpty == true ? u!.name[0] : '?', style: const TextStyle(color: Colors.white))),
                    title: Text(u?.roleLabel ?? ''),
                    subtitle: Text('${u?.email ?? ''}\nFlat: ${u?.flat ?? '—'}'),
                    isThreeLine: true,
                  ),
                ),
                const SizedBox(height: 16),
                TextField(controller: _name, decoration: const InputDecoration(labelText: 'Full name')),
                const SizedBox(height: 12),
                TextField(controller: _phone, decoration: const InputDecoration(labelText: 'Phone')),
                const SizedBox(height: 20),
                FilledButton(
                  onPressed: saving
                      ? null
                      : () async {
                          setState(() => saving = true);
                          try {
                            await context.read<AuthProvider>().authService.client.put('/profile', body: {'name': _name.text, 'phone': _phone.text});
                            if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Profile saved')));
                          } catch (e) {
                            if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                          }
                          setState(() => saving = false);
                        },
                  child: saving ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white)) : const Text('Save profile'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _FamilyScreen extends StatefulWidget {
  @override
  State<_FamilyScreen> createState() => _FamilyScreenState();
}

class _FamilyScreenState extends State<_FamilyScreen> {
  final _name = TextEditingController();
  final _relation = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Family members'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                TextField(controller: _name, decoration: const InputDecoration(labelText: 'Name')),
                TextField(controller: _relation, decoration: const InputDecoration(labelText: 'Relation')),
                const SizedBox(height: 16),
                FilledButton(
                  onPressed: () async {
                    try {
                      await context.read<AuthProvider>().authService.client.post('/profile/family', body: {'name': _name.text, 'relation': _relation.text});
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Family member added')));
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: const Text('Add family member'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _VehiclesScreen extends StatefulWidget {
  @override
  State<_VehiclesScreen> createState() => _VehiclesScreenState();
}

class _VehiclesScreenState extends State<_VehiclesScreen> {
  final _no = TextEditingController();
  String _type = 'car';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Vehicles'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                TextField(controller: _no, decoration: const InputDecoration(labelText: 'Vehicle number')),
                DropdownButtonFormField(
                  value: _type,
                  decoration: const InputDecoration(labelText: 'Type'),
                  items: const [
                    DropdownMenuItem(value: 'car', child: Text('Car')),
                    DropdownMenuItem(value: 'bike', child: Text('Bike')),
                    DropdownMenuItem(value: 'other', child: Text('Other')),
                  ],
                  onChanged: (v) => setState(() => _type = v ?? 'car'),
                ),
                const SizedBox(height: 16),
                FilledButton(
                  onPressed: () async {
                    try {
                      await context.read<AuthProvider>().authService.client.post('/profile/vehicle', body: {'vehicle_no': _no.text, 'type': _type});
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Vehicle registered')));
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: const Text('Add vehicle'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _DocumentsScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Documents', subtitle: 'ID proof & society documents'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: const [
                DataListTile(title: 'Aadhaar / ID proof', subtitle: 'Update via Profile → member ID fields', leading: Icons.badge_rounded),
                DataListTile(title: 'Maintenance receipts', subtitle: 'Available under Payment History', leading: Icons.receipt_rounded),
                DataListTile(title: 'Visitor passes', subtitle: 'QR links sent when you pre-register visitors', leading: Icons.qr_code_rounded),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _BillsScreen extends StatelessWidget {
  const _BillsScreen({required this.path});
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
        subtitle: 'Due: ${item['due_date'] ?? '—'}',
        status: item['status']?.toString(),
        leading: Icons.receipt_long_rounded,
        trailing: const Chip(label: Text('Office pay', style: TextStyle(fontSize: 10))),
      ),
    );
  }
}

class _ComplaintsScreen extends StatelessWidget {
  const _ComplaintsScreen({required this.path});
  final String path;

  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'Complaints',
      path: path,
      itemBuilder: (c, item) => DataListTile(
        title: item['subject']?.toString() ?? 'Complaint',
        subtitle: item['description']?.toString(),
        status: item['status']?.toString(),
        leading: Icons.report_problem_rounded,
        onTap: () => _showComplaintActions(c, item),
      ),
      floatingAction: path.contains('resident')
          ? FloatingActionButton.extended(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => _NewComplaintScreen())),
              icon: const Icon(Icons.add),
              label: const Text('Raise'),
            )
          : null,
    );
  }

  static void _showComplaintActions(BuildContext c, Map<String, dynamic> item) {
    if (!['resolved', 'closed'].contains(item['status'])) return;
    showModalBottomSheet(
      context: c,
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('Rate resolution', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: List.generate(5, (i) {
                final star = i + 1;
                return IconButton(
                  icon: Icon(Icons.star, color: AppTheme.accent, size: 32),
                  onPressed: () async {
                    Navigator.pop(ctx);
                    try {
                      await ctx.read<AuthProvider>().authService.client.post('/resident/complaints/${item['id']}/rate', body: {'rating': star});
                      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(const SnackBar(content: Text('Thank you for feedback')));
                    } catch (e) {
                      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                );
              }),
            ),
          ],
        ),
      ),
    );
  }
}

class _NewComplaintScreen extends StatefulWidget {
  @override
  State<_NewComplaintScreen> createState() => _NewComplaintScreenState();
}

class _NewComplaintScreenState extends State<_NewComplaintScreen> {
  final _subject = TextEditingController();
  final _desc = TextEditingController();
  String _cat = 'maintenance';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Raise complaint')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          TextField(controller: _subject, decoration: const InputDecoration(labelText: 'Subject')),
          TextField(controller: _desc, decoration: const InputDecoration(labelText: 'Description'), maxLines: 4),
          DropdownButtonFormField(
            value: _cat,
            decoration: const InputDecoration(labelText: 'Category'),
            items: ['maintenance', 'security', 'parking', 'noise', 'other'].map((e) => DropdownMenuItem(value: e, child: Text(e))).toList(),
            onChanged: (v) => setState(() => _cat = v ?? 'maintenance'),
          ),
          const SizedBox(height: 16),
          FilledButton(
            onPressed: () async {
              try {
                await context.read<AuthProvider>().authService.client.post('/resident/complaints', body: {
                  'subject': _subject.text,
                  'description': _desc.text,
                  'category': _cat,
                });
                if (context.mounted) Navigator.pop(context);
              } catch (e) {
                if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
              }
            },
            child: const Text('Submit complaint'),
          ),
        ],
      ),
    );
  }
}

class _VisitorsScreen extends StatelessWidget {
  const _VisitorsScreen({required this.path});
  final String path;

  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'Visitors',
      path: path,
      itemBuilder: (c, item) => ModuleScreens._visitorTile(c, item, onTap: () => _VisitorsScreen._openPass(c, path, item['id'])),
      floatingAction: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const _PreRegisterVisitorScreen())),
        icon: const Icon(Icons.person_add),
        label: const Text('Create gate pass'),
      ),
    );
  }

  static Future<void> _openPass(BuildContext c, String basePath, dynamic id) async {
    try {
      final j = await c.read<AuthProvider>().authService.client.get('$basePath/$id/pass');
      if (!c.mounted) return;
      Navigator.push(c, MaterialPageRoute(builder: (_) => GatePassScreen.fromApi(parseMap(j['data']))));
    } catch (e) {
      if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(SnackBar(content: Text('$e')));
    }
  }
}

class _PreRegisterVisitorScreen extends StatefulWidget {
  const _PreRegisterVisitorScreen();
  @override
  State<_PreRegisterVisitorScreen> createState() => _PreRegisterVisitorScreenState();
}

class _PreRegisterVisitorScreenState extends State<_PreRegisterVisitorScreen> {
  final _name = TextEditingController();
  final _phone = TextEditingController();
  String _type = 'guest';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pre-register visitor')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          TextField(controller: _name, decoration: const InputDecoration(labelText: 'Visitor name')),
          TextField(controller: _phone, decoration: const InputDecoration(labelText: 'Phone')),
          DropdownButtonFormField(
            value: _type,
            items: ['guest', 'delivery', 'cab', 'domestic_help', 'vendor', 'other']
                .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                .toList(),
            onChanged: (v) => setState(() => _type = v ?? 'guest'),
          ),
          const SizedBox(height: 16),
          FilledButton(
            onPressed: () async {
              try {
                final j = await context.read<AuthProvider>().authService.client.post('/resident/visitors', body: {
                  'visitor_name': _name.text,
                  'phone': _phone.text,
                  'visit_type': _type,
                });
                if (context.mounted) {
                  Navigator.pushReplacement(
                    context,
                    MaterialPageRoute(builder: (_) => GatePassScreen.fromApi(parseMap(j['data']))),
                  );
                }
              } catch (e) {
                if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
              }
            },
            child: const Text('Create pass'),
          ),
        ],
      ),
    );
  }
}

class _AmenitiesScreen extends StatelessWidget {
  const _AmenitiesScreen({required this.path});
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
        onTap: () => Navigator.push(c, MaterialPageRoute(builder: (_) => _BookAmenityScreen(amenityId: item['id'] as int, name: item['name']?.toString() ?? ''))),
      ),
    );
  }
}

class _BookAmenityScreen extends StatefulWidget {
  const _BookAmenityScreen({required this.amenityId, required this.name});
  final int amenityId;
  final String name;

  @override
  State<_BookAmenityScreen> createState() => _BookAmenityScreenState();
}

class _BookAmenityScreenState extends State<_BookAmenityScreen> {
  DateTime from = DateTime.now().add(const Duration(hours: 2));
  DateTime to = DateTime.now().add(const Duration(hours: 4));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Book ${widget.name}')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: FilledButton(
          onPressed: () async {
            try {
              await context.read<AuthProvider>().authService.client.post('/resident/bookings', body: {
                'amenity_id': widget.amenityId,
                'booked_from': from.toIso8601String(),
                'booked_to': to.toIso8601String(),
              });
              if (context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Booking requested')));
                Navigator.pop(context);
              }
            } catch (e) {
              if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
            }
          },
          child: const Text('Request booking'),
        ),
      ),
    );
  }
}

class _SosScreen extends StatelessWidget {
  const _SosScreen({required this.path});
  final String path;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Emergency SOS', subtitle: 'Alerts security & admin instantly'),
          Expanded(
            child: Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Material(
                    elevation: 8,
                    shape: const CircleBorder(),
                    color: AppTheme.danger,
                    child: InkWell(
                      customBorder: const CircleBorder(),
                      onTap: () async {
                        try {
                          await context.read<AuthProvider>().authService.client.post(path, body: {'message': 'SOS from mobile'});
                          if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('SOS sent')));
                        } catch (e) {
                          if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                        }
                      },
                      child: const SizedBox(width: 180, height: 180, child: Center(child: Text('SOS', style: TextStyle(color: Colors.white, fontSize: 36, fontWeight: FontWeight.bold)))),
                    ),
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

class _EmergencyScreen extends StatelessWidget {
  const _EmergencyScreen({required this.path});
  final String path;

  @override
  Widget build(BuildContext context) {
    return ApiScreen(title: 'Emergency contacts', path: path, itemBuilder: (c, item) => DataListTile(
          title: item['label']?.toString() ?? 'Contact',
          subtitle: item['phone']?.toString(),
          leading: Icons.phone_rounded,
        ));
  }
}

// --- Guard ---

class _GuardScanScreen extends StatefulWidget {
  @override
  State<_GuardScanScreen> createState() => _GuardScanScreenState();
}

class _GuardScanScreenState extends State<_GuardScanScreen> {
  final _code = TextEditingController();
  final _otp = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Scan QR / OTP'),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                TextField(controller: _code, decoration: const InputDecoration(labelText: 'QR token or pass code', prefixIcon: Icon(Icons.qr_code))),
                TextField(controller: _otp, decoration: const InputDecoration(labelText: 'OTP (optional)', prefixIcon: Icon(Icons.pin))),
                const SizedBox(height: 16),
                FilledButton(
                  onPressed: () async {
                    try {
                      final j = await context.read<AuthProvider>().authService.client.post('/guard/verify', body: {
                        'lookup': _code.text.trim(),
                        if (_otp.text.isNotEmpty) 'otp': _otp.text.trim(),
                      });
                      if (!context.mounted) return;
                      showDialog(context: context, builder: (c) => AlertDialog(title: const Text('Verified'), content: Text('$j'), actions: [TextButton(onPressed: () => Navigator.pop(c), child: const Text('OK'))]));
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: const Text('Verify pass'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _GuardEntryScreen extends StatefulWidget {
  @override
  State<_GuardEntryScreen> createState() => _GuardEntryScreenState();
}

class _GuardEntryScreenState extends State<_GuardEntryScreen> {
  final _name = TextEditingController();
  final _phone = TextEditingController();
  String _type = 'guest';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'New visitor entry'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                TextField(controller: _name, decoration: const InputDecoration(labelText: 'Visitor name')),
                TextField(controller: _phone, decoration: const InputDecoration(labelText: 'Phone')),
                DropdownButtonFormField(
                  value: _type,
                  items: ['guest', 'delivery', 'cab', 'domestic_help', 'vendor', 'other'].map((e) => DropdownMenuItem(value: e, child: Text(e))).toList(),
                  onChanged: (v) => setState(() => _type = v ?? 'guest'),
                ),
                const SizedBox(height: 16),
                FilledButton(
                  onPressed: () async {
                    try {
                      final j = await context.read<AuthProvider>().authService.client.post('/guard/entries', body: {
                        'visitor_name': _name.text,
                        'phone': _phone.text,
                        'visit_type': _type,
                      });
                      if (context.mounted) {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => GatePassScreen.fromApi(parseMap(j['data']))));
                      }
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: const Text('Create entry'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _GuardActionScreen extends StatefulWidget {
  const _GuardActionScreen({required this.title, required this.action});
  final String title;
  final String action;

  @override
  State<_GuardActionScreen> createState() => _GuardActionScreenState();
}

class _GuardActionScreenState extends State<_GuardActionScreen> {
  final _id = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          PageHeader(title: widget.title),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                TextField(controller: _id, decoration: const InputDecoration(labelText: 'Visitor entry ID', prefixIcon: Icon(Icons.tag))),
                const SizedBox(height: 16),
                FilledButton(
                  onPressed: () async {
                    try {
                      await context.read<AuthProvider>().authService.client.post('/guard/entries/${_id.text}/${widget.action}');
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('${widget.title} done')));
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: Text(widget.title),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _VehicleVerifyScreen extends StatefulWidget {
  @override
  State<_VehicleVerifyScreen> createState() => _VehicleVerifyScreenState();
}

class _VehicleVerifyScreenState extends State<_VehicleVerifyScreen> {
  final _no = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Vehicle verification'),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                TextField(controller: _no, decoration: const InputDecoration(labelText: 'Vehicle number')),
                FilledButton(
                  onPressed: () async {
                    try {
                      final j = await context.read<AuthProvider>().authService.client.post('/guard/verify-vehicle', body: {'vehicle_no': _no.text});
                      if (!context.mounted) return;
                      showDialog(context: context, builder: (c) => AlertDialog(title: const Text('Result'), content: Text('$j')));
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: const Text('Verify'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _GuardAttendanceScreen extends StatefulWidget {
  @override
  State<_GuardAttendanceScreen> createState() => _GuardAttendanceScreenState();
}

class _GuardAttendanceScreenState extends State<_GuardAttendanceScreen> {
  List<Map<String, dynamic>> staff = [];
  int? selectedId;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final j = await context.read<AuthProvider>().authService.client.get('/guard/staff');
      staff = parseList(j['data']);
      setState(() {});
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Staff attendance'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                DropdownButtonFormField<int>(
                  value: selectedId,
                  decoration: const InputDecoration(labelText: 'Staff member'),
                  items: staff.map((s) => DropdownMenuItem(value: s['id'] as int, child: Text(s['name']?.toString() ?? ''))).toList(),
                  onChanged: (v) => setState(() => selectedId = v),
                ),
                FilledButton(
                  onPressed: selectedId == null
                      ? null
                      : () async {
                          try {
                            await context.read<AuthProvider>().authService.client.post('/guard/attendance', body: {'staff_id': selectedId, 'status': 'present'});
                            if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Recorded')));
                          } catch (e) {
                            if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                          }
                        },
                  child: const Text('Mark present'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _SosAlertsScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'SOS Alerts',
      path: '/guard/sos',
      itemBuilder: (c, item) => DataListTile(
        title: item['message']?.toString() ?? 'SOS',
        subtitle: 'Flat ${item['flat']?['flat_no'] ?? ''}',
        status: item['status']?.toString(),
        leading: Icons.sos_rounded,
        onTap: () async {
          try {
            await c.read<AuthProvider>().authService.client.post('/guard/sos/${item['id']}/resolve');
            if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(const SnackBar(content: Text('Resolved')));
          } catch (e) {
            if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(SnackBar(content: Text('$e')));
          }
        },
      ),
    );
  }
}

class _IncidentScreen extends StatefulWidget {
  @override
  State<_IncidentScreen> createState() => _IncidentScreenState();
}

class _IncidentScreenState extends State<_IncidentScreen> {
  final _title = TextEditingController();
  final _desc = TextEditingController();
  String _sev = 'medium';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Report incident'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                TextField(controller: _title, decoration: const InputDecoration(labelText: 'Title')),
                TextField(controller: _desc, decoration: const InputDecoration(labelText: 'Description'), maxLines: 3),
                DropdownButtonFormField(
                  value: _sev,
                  items: ['low', 'medium', 'high', 'critical'].map((e) => DropdownMenuItem(value: e, child: Text(e))).toList(),
                  onChanged: (v) => setState(() => _sev = v ?? 'medium'),
                ),
                FilledButton(
                  onPressed: () async {
                    try {
                      await context.read<AuthProvider>().authService.client.post('/guard/incidents', body: {
                        'title': _title.text,
                        'description': _desc.text,
                        'severity': _sev,
                      });
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Incident reported')));
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: const Text('Submit'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// --- Staff ---

class _StaffAttendanceScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'My attendance'),
          Padding(
            padding: const EdgeInsets.all(24),
            child: FilledButton(
              onPressed: () async {
                try {
                  await context.read<AuthProvider>().authService.client.post('/staff/attendance');
                  if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Present marked')));
                } catch (e) {
                  if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                }
              },
              child: const Text('Mark present today'),
            ),
          ),
        ],
      ),
    );
  }
}

class _InventoryScreen extends StatefulWidget {
  @override
  State<_InventoryScreen> createState() => _InventoryScreenState();
}

class _InventoryScreenState extends State<_InventoryScreen> {
  final _item = TextEditingController();
  final _qty = TextEditingController(text: '1');

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Inventory request'),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                TextField(controller: _item, decoration: const InputDecoration(labelText: 'Item')),
                TextField(controller: _qty, decoration: const InputDecoration(labelText: 'Quantity'), keyboardType: TextInputType.number),
                FilledButton(
                  onPressed: () async {
                    try {
                      await context.read<AuthProvider>().authService.client.post('/staff/inventory', body: {
                        'item': _item.text,
                        'quantity': int.tryParse(_qty.text) ?? 1,
                      });
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Request submitted')));
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: const Text('Submit request'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _TasksScreen extends StatelessWidget {
  const _TasksScreen({required this.role});
  final String role;

  @override
  Widget build(BuildContext context) {
    final path = role == 'vendor' ? '/vendor/tasks' : '/staff/tasks';
    return ApiScreen(
      title: 'Assigned tasks',
      path: path,
      itemBuilder: (c, item) => DataListTile(
        title: item['subject']?.toString() ?? 'Task',
        subtitle: item['description']?.toString(),
        status: item['status']?.toString(),
        leading: Icons.build_rounded,
        onTap: () => _updateTask(c, item, role),
      ),
    );
  }

  static void _updateTask(BuildContext c, Map<String, dynamic> item, String role) {
    showModalBottomSheet(
      context: c,
      builder: (ctx) => Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          ListTile(
            title: const Text('Mark in progress'),
            onTap: () => _put(ctx, item['id'], role, 'in_progress'),
          ),
          ListTile(
            title: const Text('Mark resolved'),
            onTap: () => _put(ctx, item['id'], role, 'resolved'),
          ),
        ],
      ),
    );
  }

  static Future<void> _put(BuildContext ctx, dynamic id, String role, String status) async {
    Navigator.pop(ctx);
    final path = role == 'vendor' ? '/vendor/tasks/$id' : '/staff/tasks/$id';
    try {
      await ctx.read<AuthProvider>().authService.client.put(path, body: {'status': status});
      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(const SnackBar(content: Text('Updated')));
    } catch (e) {
      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('$e')));
    }
  }
}

class _ScheduleScreen extends StatefulWidget {
  const _ScheduleScreen({required this.role});
  final String role;

  @override
  State<_ScheduleScreen> createState() => _ScheduleScreenState();
}

class _ScheduleScreenState extends State<_ScheduleScreen> {
  Map<String, dynamic> data = {};
  bool loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final path = widget.role == 'vendor' ? '/vendor/schedule' : '/staff/schedule';
    try {
      final j = await context.read<AuthProvider>().authService.client.get(path);
      data = parseMap(j['data']);
    } catch (_) {}
    if (mounted) setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Schedule'),
          Expanded(child: AsyncBody(loading: loading, empty: data.isEmpty, child: StatGrid(stats: data))),
        ],
      ),
    );
  }
}

// --- Committee / Accountant ---

class _FinancialScreen extends StatefulWidget {
  @override
  State<_FinancialScreen> createState() => _FinancialScreenState();
}

class _FinancialScreenState extends State<_FinancialScreen> {
  Map<String, dynamic> data = {};
  bool loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final j = await context.read<AuthProvider>().authService.client.get('/committee/financial');
      data = parseMap(j['data']);
    } catch (_) {}
    if (mounted) setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Financial summary'),
          Expanded(child: AsyncBody(loading: loading, empty: data.isEmpty, child: StatGrid(stats: data))),
        ],
      ),
    );
  }
}

class _VisitorStatsScreen extends StatefulWidget {
  @override
  State<_VisitorStatsScreen> createState() => _VisitorStatsScreenState();
}

class _VisitorStatsScreenState extends State<_VisitorStatsScreen> {
  Map<String, dynamic> data = {};
  bool loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final j = await context.read<AuthProvider>().authService.client.get('/committee/visitor-stats');
      data = parseMap(j['data']);
    } catch (_) {}
    if (mounted) setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Visitor statistics'),
          Expanded(child: AsyncBody(loading: loading, empty: data.isEmpty, child: StatGrid(stats: data))),
        ],
      ),
    );
  }
}

class _ReportsScreen extends StatefulWidget {
  const _ReportsScreen({required this.role});
  final String role;

  @override
  State<_ReportsScreen> createState() => _ReportsScreenState();
}

class _ReportsScreenState extends State<_ReportsScreen> {
  Map<String, dynamic> data = {};
  bool loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final path = widget.role == 'accountant' ? '/accountant/reports' : '/committee/financial';
    try {
      final j = await context.read<AuthProvider>().authService.client.get(path);
      data = parseMap(j['data']);
    } catch (_) {}
    if (mounted) setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          PageHeader(title: widget.role == 'accountant' ? 'Reports & GST' : 'Society reports'),
          Expanded(
            child: AsyncBody(
              loading: loading,
              empty: data.isEmpty,
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: data.entries.map((e) => DataListTile(title: e.key.replaceAll('_', ' '), subtitle: '${e.value}')).toList(),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _ExpensesScreen extends StatefulWidget {
  @override
  State<_ExpensesScreen> createState() => _ExpensesScreenState();
}

class _ExpensesScreenState extends State<_ExpensesScreen> {
  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'Expenses',
      path: '/accountant/expenses',
      itemBuilder: ModuleScreens._ledgerTile,
      floatingAction: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => _AddExpenseScreen())),
        icon: const Icon(Icons.add),
        label: const Text('Add'),
      ),
    );
  }
}

class _AddExpenseScreen extends StatefulWidget {
  @override
  State<_AddExpenseScreen> createState() => _AddExpenseScreenState();
}

class _AddExpenseScreenState extends State<_AddExpenseScreen> {
  final _cat = TextEditingController();
  final _amt = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('New expense')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          TextField(controller: _cat, decoration: const InputDecoration(labelText: 'Category')),
          TextField(controller: _amt, decoration: const InputDecoration(labelText: 'Amount'), keyboardType: TextInputType.number),
          FilledButton(
            onPressed: () async {
              try {
                await context.read<AuthProvider>().authService.client.post('/accountant/expenses', body: {
                  'category': _cat.text,
                  'amount': double.tryParse(_amt.text) ?? 0,
                  'expense_date': DateTime.now().toIso8601String().split('T').first,
                });
                if (context.mounted) Navigator.pop(context);
              } catch (e) {
                if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
              }
            },
            child: const Text('Save expense'),
          ),
        ],
      ),
    );
  }
}

class _VendorInvoiceScreen extends StatefulWidget {
  @override
  State<_VendorInvoiceScreen> createState() => _VendorInvoiceScreenState();
}

class _VendorInvoiceScreenState extends State<_VendorInvoiceScreen> {
  final _amt = TextEditingController();
  final _desc = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Submit invoice'),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                TextField(controller: _amt, decoration: const InputDecoration(labelText: 'Amount'), keyboardType: TextInputType.number),
                TextField(controller: _desc, decoration: const InputDecoration(labelText: 'Description'), maxLines: 2),
                const SizedBox(height: 16),
                FilledButton(
                  onPressed: () async {
                    try {
                      await context.read<AuthProvider>().authService.client.post('/vendor/invoices', body: {
                        'amount': double.tryParse(_amt.text) ?? 0,
                        'description': _desc.text,
                      });
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Invoice submitted')));
                    } catch (e) {
                      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                  child: const Text('Submit for review'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _GatePassListScreen extends StatelessWidget {
  const _GatePassListScreen();

  @override
  Widget build(BuildContext context) {
    final role = context.read<AuthProvider>().user?.role ?? '';
    if (role == 'guard') {
      return ApiScreen(
        title: 'Gate passes',
        path: '/guard/log',
        itemBuilder: (c, item) => ModuleScreens._visitorTile(
          c,
          item,
          onTap: () => Navigator.push(c, MaterialPageRoute(builder: (_) => GatePassScreen(pass: Map<String, dynamic>.from(item)))),
        ),
      );
    }
    const path = '/resident/visitors';
    return ApiScreen(
      title: 'My gate passes',
      path: path,
      itemBuilder: (c, item) => ModuleScreens._visitorTile(c, item, onTap: () => _VisitorsScreen._openPass(c, path, item['id'])),
      floatingAction: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const _PreRegisterVisitorScreen())),
        icon: const Icon(Icons.qr_code),
        label: const Text('New pass'),
      ),
    );
  }
}
