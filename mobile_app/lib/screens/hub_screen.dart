import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../core/api_client.dart';
import '../models/menu_item.dart';
import '../providers/auth_provider.dart';
import '../utils/data_parser.dart';
import '../widgets/ui_kit.dart';

class HubScreen extends StatefulWidget {
  const HubScreen({super.key, required this.onModuleTap});

  final void Function(MenuItem menu) onModuleTap;

  @override
  State<HubScreen> createState() => _HubScreenState();
}

class _HubScreenState extends State<HubScreen> {
  bool loading = true;
  Map<String, dynamic> stats = {};
  String? error;

  String get _dashboardPath {
    final role = context.read<AuthProvider>().user?.role ?? '';
    return switch (role) {
      'guard' => '/guard/dashboard',
      'committee' => '/committee/overview',
      'staff' => '/staff/schedule',
      'accountant' => '/accountant/reports',
      'vendor' => '/vendor/schedule',
      _ => '/resident/dashboard',
    };
  }

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      loading = true;
      error = null;
    });
    try {
      final json = await context.read<AuthProvider>().authService.client.get(_dashboardPath);
      stats = parseMap(json['data']);
    } on ApiException catch (e) {
      error = e.message;
      stats = {};
    }
    if (mounted) setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final user = auth.user;
    final menus = auth.menus;

    return RefreshIndicator(
      onRefresh: _load,
      child: CustomScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        slivers: [
          SliverToBoxAdapter(
            child: PageHeader(
              title: user?.societyName ?? 'Society',
              subtitle: '${user?.roleLabel ?? ''} • ${user?.name ?? ''}${user?.flat != null ? ' • ${user!.flat}' : ''}\nGreen Valley Residency — real-time overview',
            ),
          ),
          if (!loading && stats.isNotEmpty)
            SliverToBoxAdapter(child: StatGrid(stats: stats)),
          if (error != null)
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Text(error!, style: TextStyle(color: Colors.orange.shade800)),
              ),
            ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
              child: Text('All modules', style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold)),
            ),
          ),
          SliverToBoxAdapter(
            child: ModuleGrid(
              menus: menus,
              onTap: widget.onModuleTap,
            ),
          ),
          const SliverToBoxAdapter(child: SizedBox(height: 80)),
        ],
      ),
    );
  }
}
