import 'package:flutter/material.dart';

import '../core/icon_map.dart';
import '../core/theme.dart';
import '../models/menu_item.dart';

class PageHeader extends StatelessWidget {
  const PageHeader({super.key, required this.title, this.subtitle, this.child});

  final String title;
  final String? subtitle;
  final Widget? child;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(20, 20, 20, 24),
      decoration: BoxDecoration(gradient: AppTheme.headerGradient(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
          if (subtitle != null) ...[
            const SizedBox(height: 4),
            Text(subtitle!, style: TextStyle(color: Colors.white.withValues(alpha: 0.9), fontSize: 14)),
          ],
          if (child != null) ...[const SizedBox(height: 16), child!],
        ],
      ),
    );
  }
}

class StatGrid extends StatelessWidget {
  const StatGrid({super.key, required this.stats});

  final Map<String, dynamic> stats;

  @override
  Widget build(BuildContext context) {
    if (stats.isEmpty) return const SizedBox.shrink();
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Wrap(
        spacing: 12,
        runSpacing: 12,
        children: stats.entries.map((e) {
          final w = (MediaQuery.of(context).size.width - 44) / 2;
          return SizedBox(
            width: w,
            child: Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      e.key.replaceAll('_', ' '),
                      style: Theme.of(context).textTheme.labelMedium?.copyWith(color: Colors.grey.shade600),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      '${e.value}',
                      style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold, color: AppTheme.primary),
                    ),
                  ],
                ),
              ),
            ),
          );
        }).toList(),
      ),
    );
  }
}

class ModuleGrid extends StatelessWidget {
  const ModuleGrid({super.key, required this.menus, required this.onTap});

  final List<MenuItem> menus;
  final void Function(MenuItem) onTap;

  @override
  Widget build(BuildContext context) {
    final items = menus.where((m) => m.route != '/home').toList();
    return Padding(
      padding: const EdgeInsets.all(16),
      child: GridView.builder(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 3,
          mainAxisSpacing: 12,
          crossAxisSpacing: 12,
          childAspectRatio: 0.92,
        ),
        itemCount: items.length,
        itemBuilder: (context, i) {
          final m = items[i];
          final color = menuColor(m.id);
          return Material(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            elevation: 1,
            shadowColor: Colors.black12,
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
              onTap: () => onTap(m),
              child: Padding(
                padding: const EdgeInsets.all(10),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(color: color.withValues(alpha: 0.12), shape: BoxShape.circle),
                      child: Icon(menuIcon(m.icon), color: color, size: 26),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      m.title,
                      textAlign: TextAlign.center,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

class StatusChip extends StatelessWidget {
  const StatusChip({super.key, required this.status});

  final String status;

  Color get _color {
    final s = status.toLowerCase();
    if (s.contains('paid') || s.contains('resolved') || s.contains('closed') || s.contains('approved')) {
      return Colors.green;
    }
    if (s.contains('pending') || s.contains('open') || s.contains('active')) return Colors.orange;
    if (s.contains('overdue') || s.contains('rejected')) return AppTheme.danger;
    return Colors.blueGrey;
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: _color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(8)),
      child: Text(status, style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: _color)),
    );
  }
}

class DataListTile extends StatelessWidget {
  const DataListTile({
    super.key,
    required this.title,
    this.subtitle,
    this.trailing,
    this.status,
    this.leading,
    this.onTap,
  });

  final String title;
  final String? subtitle;
  final Widget? trailing;
  final String? status;
  final IconData? leading;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: ListTile(
        leading: leading != null
            ? CircleAvatar(backgroundColor: AppTheme.primary.withValues(alpha: 0.1), child: Icon(leading, color: AppTheme.primary, size: 20))
            : null,
        title: Text(title, style: const TextStyle(fontWeight: FontWeight.w600)),
        subtitle: subtitle != null ? Text(subtitle!, maxLines: 2, overflow: TextOverflow.ellipsis) : null,
        trailing: trailing ?? (status != null ? StatusChip(status: status!) : null),
        onTap: onTap,
      ),
    );
  }
}

class AsyncBody extends StatelessWidget {
  const AsyncBody({super.key, required this.loading, this.error, this.empty = false, required this.child, this.onRetry});

  final bool loading;
  final String? error;
  final bool empty;
  final Widget child;
  final VoidCallback? onRetry;

  @override
  Widget build(BuildContext context) {
    if (loading) return const Center(child: Padding(padding: EdgeInsets.all(48), child: CircularProgressIndicator()));
    if (error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(Icons.cloud_off_rounded, size: 48, color: Colors.grey.shade400),
              const SizedBox(height: 12),
              Text(error!, textAlign: TextAlign.center),
              if (onRetry != null) ...[const SizedBox(height: 16), FilledButton(onPressed: onRetry, child: const Text('Retry'))],
            ],
          ),
        ),
      );
    }
    if (empty) {
      return Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.inbox_rounded, size: 56, color: Colors.grey.shade300),
            const SizedBox(height: 12),
            Text('No records yet', style: TextStyle(color: Colors.grey.shade600)),
          ],
        ),
      );
    }
    return child;
  }
}
