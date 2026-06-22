import 'dart:convert';

import 'package:http/http.dart' as http;

class ApiException implements Exception {
  ApiException(this.message, {this.statusCode});
  final String message;
  final int? statusCode;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient({required this.baseUrl, this.token});

  final String baseUrl;
  String? token;

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      };

  Uri uri(String path, [Map<String, String>? query]) {
    final base = baseUrl.endsWith('/') ? baseUrl.substring(0, baseUrl.length - 1) : baseUrl;
    final p = path.startsWith('/') ? path : '/$path';
    return Uri.parse('$base$p').replace(queryParameters: query);
  }

  Future<Map<String, dynamic>> get(String path, {Map<String, String>? query}) async {
    final res = await http.get(uri(path, query), headers: _headers);
    return _parse(res);
  }

  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? body}) async {
    final res = await http.post(uri(path), headers: _headers, body: body != null ? jsonEncode(body) : null);
    return _parse(res);
  }

  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? body}) async {
    final res = await http.put(uri(path), headers: _headers, body: body != null ? jsonEncode(body) : null);
    return _parse(res);
  }

  Future<Map<String, dynamic>> delete(String path) async {
    final res = await http.delete(uri(path), headers: _headers);
    return _parse(res);
  }

  Future<Map<String, dynamic>> postMultipart(
    String path, {
    required Map<String, String> fields,
    List<http.MultipartFile> files = const [],
  }) async {
    final req = http.MultipartRequest('POST', uri(path));
    req.headers['Accept'] = 'application/json';
    if (token != null) req.headers['Authorization'] = 'Bearer $token';
    req.fields.addAll(fields);
    req.files.addAll(files);
    final streamed = await req.send();
    final res = await http.Response.fromStream(streamed);
    return _parse(res);
  }

  Map<String, dynamic> _parse(http.Response res) {
    Map<String, dynamic> json;
    try {
      json = jsonDecode(res.body.isEmpty ? '{}' : res.body) as Map<String, dynamic>;
    } catch (_) {
      throw ApiException('Invalid server response (${res.statusCode})', statusCode: res.statusCode);
    }

    if (res.statusCode >= 200 && res.statusCode < 300 && json['success'] == true) {
      return json;
    }

    final msg = json['message']?.toString() ?? 'Request failed (${res.statusCode})';
    throw ApiException(msg, statusCode: res.statusCode);
  }
}
