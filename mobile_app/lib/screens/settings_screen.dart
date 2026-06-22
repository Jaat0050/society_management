import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../core/platform_config.dart';
import '../core/theme.dart';
import '../widgets/form_ui.dart';
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
          const Divider(height: 32),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.page),
            child: FormSection(
              title: 'API server',
              subtitle: 'Change only if using a different backend URL',
              icon: Icons.dns_outlined,
              children: [
                AppTextField(
                  controller: _urlCtrl,
                  label: 'API base URL',
                  prefixIcon: Icons.link,
                  hint: kIsWeb ? PlatformConfig.webApiUrl : PlatformConfig.androidApiUrl,
                ),
                AppPrimaryButton(
                  label: 'Save API URL',
                  icon: Icons.save_outlined,
                  onPressed: () async {
                    await auth.setBaseUrl(_urlCtrl.text.trim());
                    if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('URL saved — re-login if needed')));
                  },
                ),
              ],
            ),
          ),
          const SizedBox(height: 8),
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
