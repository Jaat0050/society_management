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

String formatMoney(dynamic v) {
  if (v == null) return '—';
  final n = double.tryParse(v.toString()) ?? 0;
  return '₹${n.toStringAsFixed(n.truncateToDouble() == n ? 0 : 2)}';
}

String statusLabel(String? s) => (s ?? '').replaceAll('_', ' ').toUpperCase();
