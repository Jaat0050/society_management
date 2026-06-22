import 'package:intl/intl.dart';

List<Map<String, dynamic>> parseList(dynamic data) {
  if (data == null) return [];
  if (data is List) {
    return data.map((e) => Map<String, dynamic>.from(e as Map)).toList();
  }
  if (data is Map) {
    if (data['data'] is List) {
      return (data['data'] as List).map((e) => Map<String, dynamic>.from(e as Map)).toList();
    }
  }
  return [];
}

Map<String, dynamic> parseMap(dynamic data) {
  if (data is Map<String, dynamic>) return data;
  if (data is Map) return Map<String, dynamic>.from(data);
  return {};
}

List<Map<String, dynamic>> parseLogs(dynamic data) {
  if (data is List) {
    return data.map((e) => Map<String, dynamic>.from(e as Map)).toList();
  }
  return [];
}

String formatMoney(dynamic v) {
  if (v == null) return '—';
  final n = double.tryParse(v.toString()) ?? 0;
  return '₹${n.toStringAsFixed(n.truncateToDouble() == n ? 0 : 2)}';
}

String statusLabel(String? s) => (s ?? 'unknown').replaceAll('_', ' ');

String formatDate(dynamic v, {bool withTime = false}) {
  if (v == null || v.toString().isEmpty) return '—';
  final dt = DateTime.tryParse(v.toString());
  if (dt == null) return v.toString();
  return DateFormat(withTime ? 'dd MMM yyyy, hh:mm a' : 'dd MMM yyyy').format(dt.toLocal());
}

String categoryLabel(String? cat) {
  if (cat == null) return '—';
  return cat.replaceAll('_', ' ').split(' ').map((w) => w.isEmpty ? w : '${w[0].toUpperCase()}${w.substring(1)}').join(' ');
}

String flatLabel(Map<String, dynamic>? flat) {
  if (flat == null) return '—';
  final block = flat['block']?.toString();
  final no = flat['flat_no']?.toString();
  if (block != null && no != null) return '$block — $no';
  return flat['label']?.toString() ?? no ?? '—';
}
