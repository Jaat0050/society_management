import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../core/constants.dart';
import '../core/platform_config.dart';
import '../core/theme.dart';
import '../providers/auth_provider.dart';
import 'shell_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _loginCtrl = TextEditingController(text: 'resident@greenvalley.test');
  final _passCtrl = TextEditingController(text: 'password');
  final _phoneCtrl = TextEditingController();
  final _otpCtrl = TextEditingController(text: '123456');
  late final _urlCtrl = TextEditingController();
  bool _otpMode = false;
  bool _showAdvanced = false;

  @override
  void initState() {
    super.initState();
    _urlCtrl.text = ApiConstants.defaultBaseUrl;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final saved = context.read<AuthProvider>().baseUrl;
      if (saved.isNotEmpty && mounted) _urlCtrl.text = saved;
    });
  }

  @override
  void dispose() {
    _loginCtrl.dispose();
    _passCtrl.dispose();
    _phoneCtrl.dispose();
    _otpCtrl.dispose();
    _urlCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    final auth = context.read<AuthProvider>();
    if (_showAdvanced) await auth.setBaseUrl(_urlCtrl.text.trim());

    final ok = _otpMode ? await auth.loginOtp(_phoneCtrl.text.trim(), _otpCtrl.text.trim()) : await auth.login(_loginCtrl.text.trim(), _passCtrl.text);

    if (ok && mounted) {
      Navigator.of(context).pushReplacement(MaterialPageRoute(builder: (_) => const ShellScreen()));
    } else if (auth.error != null && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(auth.error!), backgroundColor: AppTheme.danger));
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final maxW = kIsWeb ? 480.0 : double.infinity;

    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(gradient: AppTheme.brandGradient),
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: ConstrainedBox(
                constraints: BoxConstraints(maxWidth: maxW),
                child: Card(
                  elevation: 8,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Icon(Icons.apartment_rounded, size: 56, color: Theme.of(context).colorScheme.primary),
                        const SizedBox(height: 12),
                        Text('Society App', textAlign: TextAlign.center, style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.bold)),
                        Text(
                          '${PlatformConfig.platformLabel} • role-based modules',
                          textAlign: TextAlign.center,
                          style: Theme.of(context).textTheme.bodySmall,
                        ),
                        const SizedBox(height: 24),
                        SegmentedButton<bool>(
                          segments: const [
                            ButtonSegment(value: false, label: Text('Password')),
                            ButtonSegment(value: true, label: Text('OTP')),
                          ],
                          selected: {_otpMode},
                          onSelectionChanged: (s) => setState(() => _otpMode = s.first),
                        ),
                        const SizedBox(height: 20),
                        if (_otpMode) ...[
                          TextField(controller: _phoneCtrl, decoration: const InputDecoration(labelText: 'Phone', prefixIcon: Icon(Icons.phone))),
                          const SizedBox(height: 12),
                          TextField(controller: _otpCtrl, decoration: const InputDecoration(labelText: 'OTP (dev: 123456)', prefixIcon: Icon(Icons.pin)), keyboardType: TextInputType.number),
                        ] else ...[
                          TextField(controller: _loginCtrl, decoration: const InputDecoration(labelText: 'Email or phone', prefixIcon: Icon(Icons.person))),
                          const SizedBox(height: 12),
                          TextField(controller: _passCtrl, decoration: const InputDecoration(labelText: 'Password', prefixIcon: Icon(Icons.lock)), obscureText: true),
                        ],
                        const SizedBox(height: 8),
                        TextButton(
                          onPressed: () => setState(() => _showAdvanced = !_showAdvanced),
                          child: Text(_showAdvanced ? 'Hide server URL' : 'API server URL'),
                        ),
                        if (_showAdvanced)
                          TextField(
                            controller: _urlCtrl,
                            decoration: InputDecoration(
                              labelText: 'API base URL',
                              helperText: kIsWeb ? PlatformConfig.webApiUrl : PlatformConfig.androidApiUrl,
                            ),
                          ),
                        const SizedBox(height: 20),
                        FilledButton(
                          onPressed: auth.loading ? null : _submit,
                          child: auth.loading
                              ? const SizedBox(height: 22, width: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                              : const Text('Sign in'),
                        ),
                        const SizedBox(height: 20),
                        Text('Quick demo login', style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w600)),
                        const SizedBox(height: 8),
                        Wrap(
                          spacing: 8,
                          runSpacing: 8,
                          children: _demoAccounts.map((a) {
                            return ActionChip(
                              label: Text(a.$1, style: const TextStyle(fontSize: 11)),
                              onPressed: () {
                                setState(() {
                                  _otpMode = false;
                                  _loginCtrl.text = a.$2;
                                  _passCtrl.text = 'password';
                                });
                              },
                            );
                          }).toList(),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

const _demoAccounts = [
  ('Resident', 'resident@greenvalley.test'),
  ('Tenant', 'tenant@greenvalley.test'),
  ('Guard', 'guard@greenvalley.test'),
  ('Staff', 'staff@greenvalley.test'),
  ('Committee', 'committee@greenvalley.test'),
  ('Accountant', 'accountant@greenvalley.test'),
  ('Vendor', 'vendor@greenvalley.test'),
];
