import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

import '../../core/api_client.dart';
import '../../core/theme.dart';
import '../../providers/auth_provider.dart';
import '../../utils/data_parser.dart';
import '../../widgets/detail_widgets.dart';
import '../../widgets/form_ui.dart';
import '../../widgets/ui_kit.dart';

/// Complaints list with filters — resident, tenant, committee.
class ComplaintsListScreen extends StatefulWidget {
  const ComplaintsListScreen({super.key, required this.path, this.canCreate = false, this.readOnly = false});

  final String path;
  final bool canCreate;
  final bool readOnly;

  @override
  State<ComplaintsListScreen> createState() => _ComplaintsListScreenState();
}

class _ComplaintsListScreenState extends State<ComplaintsListScreen> {
  bool loading = true;
  String? error;
  List<Map<String, dynamic>> items = [];
  String filter = 'all';

  static const _filters = ['all', 'open', 'in_progress', 'resolved', 'closed'];

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
      final json = await context.read<AuthProvider>().authService.client.get(widget.path);
      items = parseList(json['data']);
    } on ApiException catch (e) {
      error = e.message;
    }
    if (mounted) setState(() => loading = false);
  }

  List<Map<String, dynamic>> get _filtered {
    if (filter == 'all') return items;
    return items.where((c) => c['status']?.toString() == filter).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Complaints', subtitle: 'Track status & resolution'),
          StatusFilterBar(selected: filter, options: _filters, onChanged: (v) => setState(() => filter = v)),
          Expanded(
            child: RefreshIndicator(
              onRefresh: _load,
              child: AsyncBody(
                loading: loading,
                error: error,
                empty: _filtered.isEmpty,
                onRetry: _load,
                child: ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _filtered.length,
                  itemBuilder: (c, i) {
                    final item = _filtered[i];
                    return DataListTile(
                      title: item['subject']?.toString() ?? 'Complaint',
                      subtitle: '${categoryLabel(item['category']?.toString())} • ${formatDate(item['created_at'])}',
                      status: item['status']?.toString(),
                      leading: Icons.report_problem_rounded,
                      trailing: const Icon(Icons.chevron_right),
                      onTap: () async {
                        await Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => ComplaintDetailScreen(
                              complaint: item,
                              readOnly: widget.readOnly,
                              listPath: widget.path,
                            ),
                          ),
                        );
                        _load();
                      },
                    );
                  },
                ),
              ),
            ),
          ),
        ],
      ),
      floatingActionButton: widget.canCreate
          ? FloatingActionButton.extended(
              onPressed: () async {
                await Navigator.push(context, MaterialPageRoute(builder: (_) => const ComplaintFormScreen()));
                _load();
              },
              icon: const Icon(Icons.add),
              label: const Text('Raise complaint'),
            )
          : null,
    );
  }
}

/// Full complaint detail with status timeline.
class ComplaintDetailScreen extends StatefulWidget {
  const ComplaintDetailScreen({
    super.key,
    required this.complaint,
    this.readOnly = false,
    this.listPath,
  });

  final Map<String, dynamic> complaint;
  final bool readOnly;
  final String? listPath;

  @override
  State<ComplaintDetailScreen> createState() => _ComplaintDetailScreenState();
}

class _ComplaintDetailScreenState extends State<ComplaintDetailScreen> {
  late Map<String, dynamic> complaint;

  @override
  void initState() {
    super.initState();
    complaint = Map<String, dynamic>.from(widget.complaint);
  }

  List<Map<String, dynamic>> get _logs => parseLogs(complaint['logs']);

  bool get _canRate {
    if (widget.readOnly) return false;
    final s = complaint['status']?.toString() ?? '';
    return s == 'resolved' || s == 'closed';
  }

  Future<void> _rate(int stars) async {
    try {
      await context.read<AuthProvider>().authService.client.post(
            '/resident/complaints/${complaint['id']}/rate',
            body: {'rating': stars},
          );
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Thank you for your feedback!')));
      }
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    final staff = complaint['assigned_staff'] ?? complaint['assignedStaff'];
    final flat = complaint['flat'] as Map<String, dynamic>?;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Complaint details'),
        actions: [if (complaint['status'] != null) Padding(padding: const EdgeInsets.only(right: 12), child: Center(child: StatusChip(status: statusLabel(complaint['status']?.toString()))))],
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text(complaint['subject']?.toString() ?? '', style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          Text(complaint['description']?.toString() ?? '', style: TextStyle(color: Colors.grey.shade700, height: 1.4)),
          const SizedBox(height: 16),
          DetailCard(
            title: 'Details',
            children: [
              InfoRow(label: 'Category', value: categoryLabel(complaint['category']?.toString()), icon: Icons.category_outlined),
              InfoRow(label: 'Priority', value: statusLabel(complaint['priority']?.toString()), icon: Icons.flag_outlined),
              InfoRow(label: 'Raised on', value: formatDate(complaint['created_at'], withTime: true), icon: Icons.calendar_today_outlined),
              if (flat != null) InfoRow(label: 'Flat', value: flatLabel(flat), icon: Icons.apartment_outlined),
              if (staff != null && staff is Map) InfoRow(label: 'Assigned to', value: staff['name']?.toString() ?? '—', icon: Icons.engineering_outlined),
              if (complaint['resolved_at'] != null) InfoRow(label: 'Resolved', value: formatDate(complaint['resolved_at'], withTime: true), icon: Icons.check_circle_outline),
            ],
          ),
          if (complaint['resolution_notes'] != null && complaint['resolution_notes'].toString().isNotEmpty)
            DetailCard(
              title: 'Resolution notes',
              children: [Text(complaint['resolution_notes'].toString())],
            ),
          DetailCard(
            title: 'Status timeline',
            children: [StatusTimeline(logs: _logs)],
          ),
          if (_canRate) ...[
            const SizedBox(height: 8),
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Rate this resolution', style: TextStyle(fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(5, (i) {
                        return IconButton(
                          icon: Icon(Icons.star_rounded, color: AppTheme.accent, size: 36),
                          onPressed: () => _rate(i + 1),
                        );
                      }),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}

/// Raise new complaint with category, priority, optional photo.
class ComplaintFormScreen extends StatefulWidget {
  const ComplaintFormScreen({super.key});

  @override
  State<ComplaintFormScreen> createState() => _ComplaintFormScreenState();
}

class _ComplaintFormScreenState extends State<ComplaintFormScreen> {
  final _subject = TextEditingController();
  final _desc = TextEditingController();
  String _cat = 'maintenance';
  String _priority = 'medium';
  XFile? _photo;
  bool saving = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Raise complaint')),
      body: FormPageBody(
        children: [
          FormSection(
            title: 'Complaint details',
            subtitle: 'Society admin will review and assign staff',
            icon: Icons.report_problem_outlined,
            children: [
              AppTextField(controller: _subject, label: 'Subject *', prefixIcon: Icons.title_outlined, hint: 'Brief title'),
              AppTextField(controller: _desc, label: 'Description *', prefixIcon: Icons.description_outlined, hint: 'Describe the issue', maxLines: 4),
              AppDropdownField<String>(
                value: _cat,
                label: 'Category',
                prefixIcon: Icons.category_outlined,
                items: ['maintenance', 'security', 'parking', 'noise', 'other']
                    .map((e) => DropdownMenuItem(value: e, child: Text(categoryLabel(e))))
                    .toList(),
                onChanged: (v) => setState(() => _cat = v ?? 'maintenance'),
              ),
              AppDropdownField<String>(
                value: _priority,
                label: 'Priority',
                prefixIcon: Icons.flag_outlined,
                items: ['low', 'medium', 'high']
                    .map((e) => DropdownMenuItem(value: e, child: Text(statusLabel(e))))
                    .toList(),
                onChanged: (v) => setState(() => _priority = v ?? 'medium'),
              ),
              AppSecondaryButton(
                label: _photo == null ? 'Attach photo (optional)' : 'Photo: ${_photo!.name}',
                icon: Icons.photo_camera_outlined,
                onPressed: () async {
                  final p = await ImagePicker().pickImage(source: ImageSource.gallery, imageQuality: 70);
                  if (p != null) setState(() => _photo = p);
                },
              ),
              AppPrimaryButton(label: 'Submit complaint', loading: saving, onPressed: _submit, icon: Icons.send_rounded),
            ],
          ),
        ],
      ),
    );
  }

  Future<void> _submit() async {
    if (_subject.text.trim().isEmpty || _desc.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Subject and description are required')));
      return;
    }
    setState(() => saving = true);
    try {
      final client = context.read<AuthProvider>().authService.client;
      if (_photo != null) {
        await client.postMultipart(
          '/resident/complaints',
          fields: {
            'subject': _subject.text.trim(),
            'description': _desc.text.trim(),
            'category': _cat,
            'priority': _priority,
          },
          files: [await http.MultipartFile.fromPath('media[]', _photo!.path)],
        );
      } else {
        await client.post('/resident/complaints', body: {
          'subject': _subject.text.trim(),
          'description': _desc.text.trim(),
          'category': _cat,
          'priority': _priority,
        });
      }
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Complaint submitted successfully')));
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    }
    if (mounted) setState(() => saving = false);
  }
}

/// Staff / vendor assigned task with full update flow.
class TaskDetailScreen extends StatefulWidget {
  const TaskDetailScreen({super.key, required this.task, required this.role});

  final Map<String, dynamic> task;
  final String role;

  @override
  State<TaskDetailScreen> createState() => _TaskDetailScreenState();
}

class _TaskDetailScreenState extends State<TaskDetailScreen> {
  late String _status;
  final _notes = TextEditingController();
  XFile? _photo;
  bool saving = false;

  List<String> get _statusOptions {
    if (widget.role == 'vendor') return ['in_progress', 'resolved'];
    return ['open', 'in_progress', 'resolved', 'closed'];
  }

  @override
  void initState() {
    super.initState();
    _status = widget.task['status']?.toString() ?? 'open';
    _notes.text = widget.task['resolution_notes']?.toString() ?? '';
  }

  Future<void> _save() async {
    setState(() => saving = true);
    try {
      final client = context.read<AuthProvider>().authService.client;
      final path = widget.role == 'vendor' ? '/vendor/tasks/${widget.task['id']}' : '/staff/tasks/${widget.task['id']}';
      final fields = {'status': _status, if (_notes.text.isNotEmpty) 'resolution_notes': _notes.text};

      if (_photo != null && widget.role == 'staff') {
        await client.putMultipart(path, fields: fields, files: [await http.MultipartFile.fromPath('photo', _photo!.path)]);
      } else {
        await client.put(path, body: fields);
      }
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Task updated')));
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    }
    if (mounted) setState(() => saving = false);
  }

  @override
  Widget build(BuildContext context) {
    final flat = widget.task['flat'] as Map<String, dynamic>?;
    final logs = parseLogs(widget.task['logs']);

    return Scaffold(
      appBar: AppBar(title: const Text('Task details')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text(widget.task['subject']?.toString() ?? '', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          Text(widget.task['description']?.toString() ?? '', style: TextStyle(color: Colors.grey.shade700)),
          const SizedBox(height: 16),
          DetailCard(
            title: 'Info',
            children: [
              InfoRow(label: 'Flat', value: flatLabel(flat), icon: Icons.apartment_outlined),
              InfoRow(label: 'Category', value: categoryLabel(widget.task['category']?.toString()), icon: Icons.category_outlined),
              InfoRow(label: 'Priority', value: statusLabel(widget.task['priority']?.toString()), icon: Icons.flag_outlined),
              InfoRow(label: 'Current status', value: statusLabel(widget.task['status']?.toString()), icon: Icons.info_outline),
            ],
          ),
          if (logs.isNotEmpty)
            DetailCard(title: 'History', children: [StatusTimeline(logs: logs)]),
          FormSection(
            title: 'Update status',
            subtitle: 'Change task status and add notes',
            icon: Icons.update_rounded,
            children: [
              AppDropdownField<String>(
                value: _statusOptions.contains(_status) ? _status : _statusOptions.first,
                label: 'New status',
                prefixIcon: Icons.flag_outlined,
                items: _statusOptions.map((e) => DropdownMenuItem(value: e, child: Text(statusLabel(e)))).toList(),
                onChanged: (v) => setState(() => _status = v ?? _status),
              ),
              AppTextField(controller: _notes, label: 'Resolution notes', prefixIcon: Icons.notes_outlined, maxLines: 3),
              if (widget.role == 'staff')
                AppSecondaryButton(
                  label: _photo == null ? 'Add completion photo' : 'Photo attached',
                  icon: Icons.camera_alt_outlined,
                  onPressed: () async {
                    final p = await ImagePicker().pickImage(source: ImageSource.camera, imageQuality: 70);
                    if (p != null) setState(() => _photo = p);
                  },
                ),
              AppPrimaryButton(label: 'Save update', loading: saving, onPressed: _save, icon: Icons.check_rounded),
            ],
          ),
        ],
      ),
    );
  }
}

/// Staff / vendor task list.
class TasksListScreen extends StatefulWidget {
  const TasksListScreen({super.key, required this.role});

  final String role;

  @override
  State<TasksListScreen> createState() => _TasksListScreenState();
}

class _TasksListScreenState extends State<TasksListScreen> {
  bool loading = true;
  String? error;
  List<Map<String, dynamic>> items = [];
  String filter = 'all';

  String get _path => widget.role == 'vendor' ? '/vendor/tasks' : '/staff/tasks';

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
      final json = await context.read<AuthProvider>().authService.client.get(_path);
      items = parseList(json['data']);
    } on ApiException catch (e) {
      error = e.message;
    }
    if (mounted) setState(() => loading = false);
  }

  List<Map<String, dynamic>> get _filtered {
    if (filter == 'all') return items;
    return items.where((t) => t['status']?.toString() == filter).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          const PageHeader(title: 'Assigned tasks', subtitle: 'Update status as you work'),
          StatusFilterBar(selected: filter, options: const ['all', 'open', 'in_progress', 'resolved'], onChanged: (v) => setState(() => filter = v)),
          Expanded(
            child: RefreshIndicator(
              onRefresh: _load,
              child: AsyncBody(
                loading: loading,
                error: error,
                empty: _filtered.isEmpty,
                onRetry: _load,
                child: ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _filtered.length,
                  itemBuilder: (c, i) {
                    final item = _filtered[i];
                    final flat = item['flat'] as Map<String, dynamic>?;
                    return DataListTile(
                      title: item['subject']?.toString() ?? 'Task',
                      subtitle: '${flatLabel(flat)} • ${categoryLabel(item['category']?.toString())}',
                      status: item['status']?.toString(),
                      leading: Icons.build_rounded,
                      trailing: const Icon(Icons.chevron_right),
                      onTap: () async {
                        final updated = await Navigator.push<bool>(
                          context,
                          MaterialPageRoute(builder: (_) => TaskDetailScreen(task: item, role: widget.role)),
                        );
                        if (updated == true) _load();
                      },
                    );
                  },
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
