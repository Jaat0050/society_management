import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/api_client.dart';
import '../../providers/auth_provider.dart';
import '../../utils/data_parser.dart';
import '../../widgets/ui_kit.dart';

/// Reusable API list screen with improved UI.
class ApiScreen extends StatefulWidget {
  const ApiScreen({
    super.key,
    required this.title,
    required this.path,
    this.itemBuilder,
    this.floatingAction,
    this.header,
  });

  final String title;
  final String path;
  final Widget Function(BuildContext, Map<String, dynamic>)? itemBuilder;
  final Widget? floatingAction;
  final Widget? header;

  @override
  State<ApiScreen> createState() => _ApiScreenState();
}

class _ApiScreenState extends State<ApiScreen> {
  bool loading = true;
  String? error;
  List<Map<String, dynamic>> items = [];

  @override
  void initState() {
    super.initState();
    load();
  }

  Future<void> load() async {
    setState(() {
      loading = true;
      error = null;
    });
    try {
      final json = await context.read<AuthProvider>().authService.client.get(widget.path);
      items = parseList(json['data']);
    } on ApiException catch (e) {
      error = e.message;
    }
    if (mounted) setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          PageHeader(title: widget.title),
          if (widget.header != null) widget.header!,
          Expanded(
            child: RefreshIndicator(
              onRefresh: load,
              child: AsyncBody(
                loading: loading,
                error: error,
                empty: items.isEmpty,
                onRetry: load,
                child: ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: items.length,
                  itemBuilder: (c, i) => widget.itemBuilder?.call(c, items[i]) ?? _defaultTile(items[i]),
                ),
              ),
            ),
          ),
        ],
      ),
      floatingActionButton: widget.floatingAction,
    );
  }

  Widget _defaultTile(Map<String, dynamic> item) {
    final title = item['subject'] ?? item['title'] ?? item['name'] ?? item['visitor_name'] ?? item['bill_month'] ?? item['category'] ?? '#${item['id']}';
    final sub = item['description'] ?? item['body'] ?? item['purpose'] ?? item['flat']?['label'] ?? '';
    return DataListTile(
      title: title.toString(),
      subtitle: sub.toString().isNotEmpty ? sub.toString() : null,
      status: item['status']?.toString(),
      leading: Icons.article_outlined,
    );
  }
}
