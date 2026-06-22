import 'package:flutter/material.dart';

import '../core/theme.dart';
import '../utils/data_parser.dart';
import 'ui_kit.dart';

class InfoRow extends StatelessWidget {
  const InfoRow({super.key, required this.label, required this.value, this.icon});

  final String label;
  final String value;
  final IconData? icon;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (icon != null) ...[
            Icon(icon, size: 18, color: Colors.grey.shade600),
            const SizedBox(width: 8),
          ],
          SizedBox(
            width: 110,
            child: Text(label, style: TextStyle(color: Colors.grey.shade600, fontSize: 13)),
          ),
          Expanded(child: Text(value, style: const TextStyle(fontWeight: FontWeight.w500))),
        ],
      ),
    );
  }
}

class DetailCard extends StatelessWidget {
  const DetailCard({super.key, required this.title, required this.children});

  final String title;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(title, style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold, color: AppTheme.primary)),
            const SizedBox(height: 8),
            ...children,
          ],
        ),
      ),
    );
  }
}

class StatusTimeline extends StatelessWidget {
  const StatusTimeline({super.key, required this.logs});

  final List<Map<String, dynamic>> logs;

  @override
  Widget build(BuildContext context) {
    if (logs.isEmpty) {
      return Text('No status updates yet', style: TextStyle(color: Colors.grey.shade600));
    }
    final sorted = List<Map<String, dynamic>>.from(logs)
      ..sort((a, b) => (a['created_at'] ?? '').toString().compareTo((b['created_at'] ?? '').toString()));

    return Column(
      children: List.generate(sorted.length, (i) {
        final log = sorted[i];
        final isLast = i == sorted.length - 1;
        final status = log['status']?.toString() ?? '';
        return Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Column(
              children: [
                Container(
                  width: 12,
                  height: 12,
                  decoration: BoxDecoration(color: AppTheme.primary, shape: BoxShape.circle),
                ),
                if (!isLast) Container(width: 2, height: 40, color: Colors.grey.shade300),
              ],
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Padding(
                padding: EdgeInsets.only(bottom: isLast ? 0 : 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        StatusChip(status: statusLabel(status)),
                        const Spacer(),
                        Text(formatDate(log['created_at'], withTime: true), style: TextStyle(fontSize: 11, color: Colors.grey.shade600)),
                      ],
                    ),
                    if (log['note'] != null && log['note'].toString().isNotEmpty) ...[
                      const SizedBox(height: 4),
                      Text(log['note'].toString(), style: TextStyle(color: Colors.grey.shade700, fontSize: 13)),
                    ],
                  ],
                ),
              ),
            ),
          ],
        );
      }),
    );
  }
}

class StatusFilterBar extends StatelessWidget {
  const StatusFilterBar({super.key, required this.selected, required this.options, required this.onChanged});

  final String selected;
  final List<String> options;
  final ValueChanged<String> onChanged;

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: Row(
        children: options.map((opt) {
          final active = selected == opt;
          return Padding(
            padding: const EdgeInsets.only(right: 8),
            child: FilterChip(
              label: Text(statusLabel(opt)),
              selected: active,
              onSelected: (_) => onChanged(opt),
              selectedColor: AppTheme.primary.withValues(alpha: 0.15),
              checkmarkColor: AppTheme.primary,
            ),
          );
        }).toList(),
      ),
    );
  }
}
