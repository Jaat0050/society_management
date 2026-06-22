import 'dart:async';
import 'dart:convert';

import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:http/http.dart' as http;

import '../core/api_client.dart';
import '../utils/data_parser.dart';

class ChatMessage {
  ChatMessage({required this.id, required this.body, required this.userName, required this.userRole, required this.isMine, this.createdAt});

  final int id;
  final String body;
  final String userName;
  final String userRole;
  final bool isMine;
  final String? createdAt;

  factory ChatMessage.fromJson(Map<String, dynamic> j, int myUserId) {
    final user = j['user'] as Map<String, dynamic>? ?? {};
    return ChatMessage(
      id: j['id'] as int? ?? 0,
      body: j['body']?.toString() ?? '',
      userName: user['name']?.toString() ?? 'User',
      userRole: user['role']?.toString() ?? '',
      isMine: (user['id'] as int? ?? 0) == myUserId,
      createdAt: j['created_at']?.toString(),
    );
  }
}

class ChatService {
  ChatService(this._client, this.myUserId);

  final ApiClient _client;
  final int myUserId;
  Timer? _pollTimer;
  StreamSubscription<String>? _sseSub;
  http.Client? _sseHttp;

  void dispose() {
    _pollTimer?.cancel();
    _sseSub?.cancel();
    _sseHttp?.close();
  }

  Future<List<Map<String, dynamic>>> rooms() async {
    final j = await _client.get('/chat/rooms');
    return parseList(j['data']);
  }

  Future<List<ChatMessage>> loadMessages(int roomId, {int afterId = 0}) async {
    final path = afterId > 0 ? '/chat/rooms/$roomId/messages?after_id=$afterId' : '/chat/rooms/$roomId/messages';
    final j = await _client.get(path);
    final data = j['data'] as Map<String, dynamic>? ?? {};
    final list = parseList(data['messages']);
    return list.map((m) => ChatMessage.fromJson(m, myUserId)).toList();
  }

  Future<ChatMessage> send(int roomId, String body) async {
    final j = await _client.post('/chat/rooms/$roomId/messages', body: {'body': body});
    return ChatMessage.fromJson(parseMap(j['data']), myUserId);
  }

  /// Live updates: SSE on web, polling elsewhere (socket-ready when Pusher configured).
  void listen(int roomId, void Function(List<ChatMessage> newMsgs) onMessages, {int pollSeconds = 3}) {
    dispose();
    if (kIsWeb) {
      _listenSse(roomId, onMessages);
    } else {
      _listenPoll(roomId, onMessages, pollSeconds);
    }
  }

  void _listenPoll(int roomId, void Function(List<ChatMessage>) onMessages, int seconds) {
    var lastId = 0;
    _pollTimer = Timer.periodic(Duration(seconds: seconds), (_) async {
      try {
        final msgs = await loadMessages(roomId, afterId: lastId);
        if (msgs.isEmpty) return;
        lastId = msgs.last.id;
        onMessages(msgs);
      } catch (_) {}
    });
  }

  void _listenSse(int roomId, void Function(List<ChatMessage>) onMessages) {
    final request = http.Request('GET', _client.uri('/chat/rooms/$roomId/stream'));
    request.headers['Accept'] = 'text/event-stream';
    if (_client.token != null) {
      request.headers['Authorization'] = 'Bearer ${_client.token}';
    }
    _sseHttp = http.Client();
    _sseHttp!.send(request).then((streamed) {
      streamed.stream.transform(utf8.decoder).listen((chunk) {
        for (final line in chunk.split('\n')) {
          if (!line.startsWith('data:')) continue;
          final raw = line.substring(5).trim();
          if (raw.isEmpty || raw == '[]') continue;
          try {
            final decoded = jsonDecode(raw);
            final list = decoded is List ? decoded : [decoded];
            final msgs = list.map((e) => ChatMessage.fromJson(Map<String, dynamic>.from(e as Map), myUserId)).toList();
            if (msgs.isNotEmpty) onMessages(msgs);
          } catch (_) {}
        }
      });
    });
  }
}
