import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:qr_flutter/qr_flutter.dart';

import '../../core/theme.dart';
import '../../widgets/ui_kit.dart';

class GatePassScreen extends StatelessWidget {
  const GatePassScreen({super.key, required this.pass});

  final Map<String, dynamic> pass;

  factory GatePassScreen.fromApi(Map<String, dynamic> data) {
    final p = data['gate_pass'] as Map<String, dynamic>? ?? data['entry'] as Map<String, dynamic>? ?? data;
    return GatePassScreen(pass: Map<String, dynamic>.from(p));
  }

  @override
  Widget build(BuildContext context) {
    final name = pass['visitor_name']?.toString() ?? 'Visitor';
    final code = pass['pass_code']?.toString() ?? '—';
    final otp = pass['approval_otp']?.toString();
    final status = pass['status_label']?.toString() ?? pass['status']?.toString() ?? '';
    final url = pass['approval_url']?.toString() ?? '';
    final flat = pass['flat']?.toString();
    final type = pass['visit_type']?.toString() ?? '';

    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Digital Gate Pass', subtitle: 'Show QR or pass code at security'),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: AppTheme.brandGradient,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [BoxShadow(color: AppTheme.primary.withValues(alpha: 0.25), blurRadius: 16, offset: const Offset(0, 8))],
                    ),
                    child: Column(
                      children: [
                        const Icon(Icons.badge_rounded, color: Colors.white, size: 40),
                        const SizedBox(height: 8),
                        Text(name, style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
                        if (flat != null) Text('Flat $flat', style: TextStyle(color: Colors.white.withValues(alpha: 0.9))),
                        const SizedBox(height: 12),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(20)),
                          child: Text(status, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  if (url.isNotEmpty)
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppTheme.primaryLight.withValues(alpha: 0.3))),
                      child: QrImageView(data: url, size: 200, backgroundColor: Colors.white),
                    ),
                  const SizedBox(height: 20),
                  _PassRow(label: 'Pass code', value: code, copy: true),
                  if (otp != null) _PassRow(label: 'Approval OTP', value: otp, copy: true),
                  _PassRow(label: 'Visit type', value: type),
                  if (pass['purpose'] != null) _PassRow(label: 'Purpose', value: pass['purpose'].toString()),
                  const SizedBox(height: 16),
                  Text('Valid for today • Green Valley Society', style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _PassRow extends StatelessWidget {
  const _PassRow({required this.label, required this.value, this.copy = false});

  final String label;
  final String value;
  final bool copy;

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: ListTile(
        title: Text(label, style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
        subtitle: Text(value, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.primary)),
        trailing: copy
            ? IconButton(
                icon: const Icon(Icons.copy_rounded),
                onPressed: () {
                  Clipboard.setData(ClipboardData(text: value));
                  ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$label copied')));
                },
              )
            : null,
      ),
    );
  }
}
