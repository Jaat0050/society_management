import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../core/platform_config.dart';
import '../core/theme.dart';
import 'modules/chat_screen.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import '../providers/auth_provider.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  late final _urlCtrl = TextEditingController(text: context.read<AuthProvider>().baseUrl);
  String _locale = 'en';

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    return Scaffold(
      appBar: AppBar(title: const Text('Settings')),
      body: ListView(
        children: [
          SwitchListTile(
            title: const Text('Dark mode'),
            value: auth.darkMode,
            onChanged: (v) => auth.toggleDarkMode(v),
          ),
          ListTile(
            title: const Text('Language'),
            subtitle: Text(_locale == 'hi' ? 'Hindi (UI stub)' : 'English'),
            trailing: DropdownButton<String>(
              value: _locale,
              items: const [
                DropdownMenuItem(value: 'en', child: Text('English')),
                DropdownMenuItem(value: 'hi', child: Text('हिंदी')),
              ],
              onChanged: (v) {
                if (v != null) {
                  setState(() => _locale = v);
                  auth.authService.updatePreferences(locale: v);
                }
              },
            ),
          ),
          const Divider(),
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              controller: _urlCtrl,
              decoration: InputDecoration(
                labelText: 'API base URL (${PlatformConfig.platformLabel})',
                helperText: kIsWeb
                    ? '${PlatformConfig.webApiUrl}\nAlt: ${PlatformConfig.webApiUrlVhost}'
                    : PlatformConfig.androidApiUrl,
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: FilledButton(
              onPressed: () async {
                await auth.setBaseUrl(_urlCtrl.text.trim());
                if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('URL saved — re-login if needed')));
              },
              child: const Text('Save API URL'),
            ),
          ),
          ListTile(title: const Text('Online payment'), subtitle: Text(auth.paymentGatewayEnabled ? 'Enabled' : 'Disabled — pay at society office')),
          const ListTile(title: Text('Push notifications'), subtitle: Text('Configure FCM token in production')),
          ListTile(
            title: const Text('Community chat'),
            subtitle: const Text('Real-time society forum (SSE / live sync)'),
            leading: const Icon(Icons.forum_rounded, color: AppTheme.primary),
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ChatScreen())),
          ),
        ],
      ),
    );
  }
}
