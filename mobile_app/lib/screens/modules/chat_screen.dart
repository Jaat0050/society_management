import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/theme.dart';
import '../../providers/auth_provider.dart';
import '../../services/chat_service.dart';
import '../../widgets/ui_kit.dart';

class ChatScreen extends StatefulWidget {
  const ChatScreen({super.key});

  @override
  State<ChatScreen> createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  ChatService? _chat;
  List<Map<String, dynamic>> _rooms = [];
  int? _roomId;
  String _roomName = 'Community';
  final _messages = <ChatMessage>[];
  final _ctrl = TextEditingController();
  final _scroll = ScrollController();
  bool _loading = true;
  bool _live = false;

  @override
  void initState() {
    super.initState();
    _init();
  }

  Future<void> _init() async {
    final auth = context.read<AuthProvider>();
    final userId = auth.user?.id ?? 0;
    _chat = ChatService(auth.authService.client, userId);
    try {
      _rooms = await _chat!.rooms();
      if (_rooms.isNotEmpty) {
        _roomId = _rooms.first['id'] as int?;
        _roomName = _rooms.first['name']?.toString() ?? 'Chat';
      }
      if (_roomId != null) {
        final msgs = await _chat!.loadMessages(_roomId!);
        setState(() {
          _messages.addAll(msgs);
          _loading = false;
        });
        _startLive();
      } else {
        setState(() => _loading = false);
      }
    } catch (_) {
      setState(() => _loading = false);
    }
  }

  void _startLive() {
    if (_roomId == null || _chat == null) return;
    setState(() => _live = true);
    _chat!.listen(_roomId!, (incoming) {
      setState(() => _messages.addAll(incoming));
      _scrollToEnd();
    }, pollSeconds: 3);
  }

  void _scrollToEnd() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scroll.hasClients) _scroll.animateTo(_scroll.position.maxScrollExtent, duration: const Duration(milliseconds: 200), curve: Curves.easeOut);
    });
  }

  Future<void> _send() async {
    final text = _ctrl.text.trim();
    if (text.isEmpty || _roomId == null || _chat == null) return;
    _ctrl.clear();
    try {
      final msg = await _chat!.send(_roomId!, text);
      setState(() => _messages.add(msg));
      _scrollToEnd();
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    }
  }

  @override
  void dispose() {
    _chat?.dispose();
    _ctrl.dispose();
    _scroll.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          PageHeader(
            title: 'Community Chat',
            subtitle: _roomName,
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(12)),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(_live ? Icons.circle : Icons.circle_outlined, color: _live ? Colors.lightGreenAccent : Colors.white54, size: 10),
                      const SizedBox(width: 6),
                      Text(_live ? 'Live sync' : 'Connecting…', style: const TextStyle(color: Colors.white, fontSize: 12)),
                    ],
                  ),
                ),
                const Spacer(),
                if (_rooms.length > 1)
                  PopupMenuButton<int>(
                    icon: const Icon(Icons.forum, color: Colors.white),
                    onSelected: (id) async {
                      _chat?.dispose();
                      setState(() {
                        _roomId = id;
                        _roomName = _rooms.firstWhere((r) => r['id'] == id)['name']?.toString() ?? '';
                        _messages.clear();
                        _loading = true;
                      });
                      final msgs = await _chat!.loadMessages(id);
                      setState(() {
                        _messages.addAll(msgs);
                        _loading = false;
                      });
                      _startLive();
                    },
                    itemBuilder: (c) => _rooms.map((r) => PopupMenuItem(value: r['id'] as int, child: Text(r['name']?.toString() ?? ''))).toList(),
                  ),
              ],
            ),
          ),
          Expanded(
            child: AsyncBody(
              loading: _loading,
              empty: _messages.isEmpty,
              child: ListView.builder(
                controller: _scroll,
                padding: const EdgeInsets.all(12),
                itemCount: _messages.length,
                itemBuilder: (c, i) => _Bubble(msg: _messages[i]),
              ),
            ),
          ),
          Material(
            elevation: 8,
            child: SafeArea(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(12, 8, 12, 12),
                child: Row(
                  children: [
                    Expanded(
                      child: TextField(
                        controller: _ctrl,
                        decoration: InputDecoration(
                          hintText: 'Message society members…',
                          filled: true,
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(24)),
                        ),
                        onSubmitted: (_) => _send(),
                      ),
                    ),
                    const SizedBox(width: 8),
                    FilledButton(onPressed: _send, child: const Icon(Icons.send_rounded)),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _Bubble extends StatelessWidget {
  const _Bubble({required this.msg});
  final ChatMessage msg;

  @override
  Widget build(BuildContext context) {
    final align = msg.isMine ? CrossAxisAlignment.end : CrossAxisAlignment.start;
    final color = msg.isMine ? AppTheme.primary : Colors.white;
    final textColor = msg.isMine ? Colors.white : Colors.black87;

    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Column(
        crossAxisAlignment: align,
        children: [
          if (!msg.isMine)
            Padding(
              padding: const EdgeInsets.only(left: 8, bottom: 4),
              child: Text('${msg.userName} • ${msg.userRole}', style: TextStyle(fontSize: 11, color: Colors.grey.shade600)),
            ),
          Container(
            constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.78),
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
            decoration: BoxDecoration(
              color: color,
              borderRadius: BorderRadius.only(
                topLeft: const Radius.circular(16),
                topRight: const Radius.circular(16),
                bottomLeft: Radius.circular(msg.isMine ? 16 : 4),
                bottomRight: Radius.circular(msg.isMine ? 4 : 16),
              ),
              boxShadow: [if (!msg.isMine) BoxShadow(color: Colors.black.withValues(alpha: 0.06), blurRadius: 6)],
            ),
            child: Text(msg.body, style: TextStyle(color: textColor)),
          ),
        ],
      ),
    );
  }
}
