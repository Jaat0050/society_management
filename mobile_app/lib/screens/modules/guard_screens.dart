import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/theme.dart';
import '../../providers/auth_provider.dart';
import '../../utils/data_parser.dart';
import '../../widgets/detail_widgets.dart';
import '../../widgets/form_ui.dart';
import '../../widgets/ui_kit.dart';
import 'api_screen.dart';
import 'gate_pass_screen.dart';

class GuardVerifyScreen extends StatefulWidget {
  const GuardVerifyScreen({super.key});

  @override
  State<GuardVerifyScreen> createState() => _GuardVerifyScreenState();
}

class _GuardVerifyScreenState extends State<GuardVerifyScreen> {
  final _code = TextEditingController();
  final _otp = TextEditingController();
  Map<String, dynamic>? result;
  bool loading = false;
  String? error;

  Future<void> _verify() async {
    if (_code.text.trim().isEmpty) return;
    setState(() {
      loading = true;
      error = null;
      result = null;
    });
    try {
      final j = await context.read<AuthProvider>().authService.client.post('/guard/verify', body: {
        'lookup': _code.text.trim(),
        if (_otp.text.isNotEmpty) 'otp': _otp.text.trim(),
      });
      result = parseMap(j['data']);
    } catch (e) {
      error = e.toString();
    }
    if (mounted) setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Scan QR / OTP', subtitle: 'Enter pass code or QR token'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(AppSpacing.page),
              children: [
                FormSection(
                  title: 'Verify pass',
                  icon: Icons.qr_code_scanner_rounded,
                  children: [
                    AppTextField(controller: _code, label: 'QR token or pass code *', prefixIcon: Icons.qr_code),
                    AppTextField(controller: _otp, label: 'OTP (if required)', prefixIcon: Icons.pin_outlined, keyboardType: TextInputType.number),
                    AppPrimaryButton(label: 'Verify pass', loading: loading, onPressed: _verify, icon: Icons.verified_rounded),
                  ],
                ),
                if (error != null)
                  Padding(
                    padding: const EdgeInsets.only(bottom: 16),
                    child: Text(error!, style: const TextStyle(color: AppTheme.danger)),
                  ),
                if (result != null) ...[
                  DetailCard(
                    title: 'Verified visitor',
                    children: [
                      InfoRow(label: 'Name', value: result!['visitor_name']?.toString() ?? '—', icon: Icons.person_outline),
                      InfoRow(label: 'Type', value: categoryLabel(result!['visit_type']?.toString()), icon: Icons.category_outlined),
                      InfoRow(label: 'Phone', value: result!['phone']?.toString() ?? '—', icon: Icons.phone_outlined),
                      InfoRow(label: 'Flat', value: flatLabel(result!['flat'] as Map<String, dynamic>?), icon: Icons.apartment_outlined),
                      InfoRow(label: 'Status', value: statusLabel(result!['status']?.toString()), icon: Icons.info_outline),
                      InfoRow(label: 'Pass code', value: result!['pass_code']?.toString() ?? '—', icon: Icons.qr_code_outlined),
                    ],
                  ),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () async {
                            try {
                              await context.read<AuthProvider>().authService.client.post('/guard/entries/${result!['id']}/check-in');
                              if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Checked in')));
                            } catch (e) {
                              if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                            }
                          },
                          child: const Text('Check-in'),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: FilledButton(
                          onPressed: () async {
                            try {
                              await context.read<AuthProvider>().authService.client.post('/guard/entries/${result!['id']}/check-out');
                              if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Checked out')));
                            } catch (e) {
                              if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                            }
                          },
                          child: const Text('Check-out'),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class GuardInsideScreen extends StatelessWidget {
  const GuardInsideScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'Visitors Inside',
      path: '/guard/inside',
      itemBuilder: (c, item) => DataListTile(
        title: item['visitor_name']?.toString() ?? 'Visitor',
        subtitle: '${flatLabel(item['flat'] as Map<String, dynamic>?)} • ${categoryLabel(item['visit_type']?.toString())}',
        status: item['status']?.toString(),
        leading: Icons.person_pin_circle_outlined,
        trailing: FilledButton(
          onPressed: () async {
            try {
              await c.read<AuthProvider>().authService.client.post('/guard/entries/${item['id']}/check-out');
              if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(const SnackBar(content: Text('Checked out')));
            } catch (e) {
              if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(SnackBar(content: Text('$e')));
            }
          },
          child: const Text('Out', style: TextStyle(fontSize: 12)),
        ),
      ),
    );
  }
}

class VehicleVerifyScreen extends StatefulWidget {
  const VehicleVerifyScreen({super.key});

  @override
  State<VehicleVerifyScreen> createState() => _VehicleVerifyScreenState();
}

class _VehicleVerifyScreenState extends State<VehicleVerifyScreen> {
  final _no = TextEditingController();
  Map<String, dynamic>? result;
  bool loading = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Vehicle verification'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(AppSpacing.page),
              children: [
                FormSection(
                  title: 'Check registration',
                  icon: Icons.directions_car_outlined,
                  children: [
                    AppTextField(controller: _no, label: 'Vehicle number', prefixIcon: Icons.pin_outlined, textCapitalization: TextCapitalization.characters),
                    AppPrimaryButton(
                      label: 'Verify vehicle',
                      loading: loading,
                      icon: Icons.search_rounded,
                      onPressed: loading
                          ? null
                          : () async {
                              setState(() => loading = true);
                              try {
                                final j = await context.read<AuthProvider>().authService.client.post('/guard/verify-vehicle', body: {'vehicle_no': _no.text.trim()});
                                result = parseMap(j['data']);
                              } catch (e) {
                                if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
                              }
                              if (mounted) setState(() => loading = false);
                            },
                    ),
                  ],
                ),
                if (result != null) ...[
                  if (result!['found'] == false)
                    const Card(child: ListTile(title: Text('Vehicle not registered'), leading: Icon(Icons.warning_amber_outlined)))
                  else
                    DetailCard(
                      title: 'Registered vehicle',
                      children: [
                        InfoRow(label: 'Number', value: result!['vehicle_no']?.toString() ?? '—', icon: Icons.directions_car_outlined),
                        InfoRow(label: 'Type', value: statusLabel(result!['type']?.toString()), icon: Icons.category_outlined),
                        InfoRow(label: 'Owner', value: result!['member']?['name']?.toString() ?? '—', icon: Icons.person_outline),
                        InfoRow(label: 'Flat', value: flatLabel(result!['member']?['flat'] as Map<String, dynamic>?), icon: Icons.apartment_outlined),
                      ],
                    ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class GuardNewEntryScreen extends StatefulWidget {
  const GuardNewEntryScreen({super.key});

  @override
  State<GuardNewEntryScreen> createState() => _GuardNewEntryScreenState();
}

class _GuardNewEntryScreenState extends State<GuardNewEntryScreen> {
  final _name = TextEditingController();
  final _phone = TextEditingController();
  String _type = 'guest';
  bool saving = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'New visitor entry'),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(AppSpacing.page),
              children: [
                FormSection(
                  title: 'Visitor details',
                  subtitle: 'Gate pass will be generated',
                  icon: Icons.person_add_alt_1_rounded,
                  children: [
                    AppTextField(controller: _name, label: 'Visitor name *', prefixIcon: Icons.person_outline),
                    AppTextField(controller: _phone, label: 'Phone', prefixIcon: Icons.phone_outlined, keyboardType: TextInputType.phone),
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
                      label: 'Create entry & pass',
                      loading: saving,
                      icon: Icons.qr_code_rounded,
                      onPressed: saving
                          ? null
                          : () async {
                              if (_name.text.trim().isEmpty) return;
                              setState(() => saving = true);
                              try {
                                final j = await context.read<AuthProvider>().authService.client.post('/guard/entries', body: {
                                  'visitor_name': _name.text.trim(),
                                  if (_phone.text.isNotEmpty) 'phone': _phone.text.trim(),
                                  'visit_type': _type,
                                });
                                if (context.mounted) {
                                  Navigator.push(context, MaterialPageRoute(builder: (_) => GatePassScreen.fromApi(parseMap(j['data']))));
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
          ),
        ],
      ),
    );
  }
}

class SosAlertsScreen extends StatelessWidget {
  const SosAlertsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return ApiScreen(
      title: 'SOS Alerts',
      path: '/guard/sos',
      itemBuilder: (c, item) => DataListTile(
        title: item['message']?.toString() ?? 'SOS Alert',
        subtitle: 'Flat ${flatLabel(item['flat'] as Map<String, dynamic>?)} • ${formatDate(item['created_at'], withTime: true)}',
        status: item['status']?.toString(),
        leading: Icons.sos_rounded,
        onTap: () => showModalBottomSheet(
          context: c,
          builder: (ctx) => SafeArea(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Text(item['message']?.toString() ?? 'Emergency SOS', style: const TextStyle(fontWeight: FontWeight.bold)),
                ),
                ListTile(
                  leading: const Icon(Icons.check_circle_outline, color: Colors.green),
                  title: const Text('Mark as resolved'),
                  onTap: () async {
                    Navigator.pop(ctx);
                    try {
                      await c.read<AuthProvider>().authService.client.post('/guard/sos/${item['id']}/resolve');
                      if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(const SnackBar(content: Text('SOS resolved')));
                    } catch (e) {
                      if (c.mounted) ScaffoldMessenger.of(c).showSnackBar(SnackBar(content: Text('$e')));
                    }
                  },
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
