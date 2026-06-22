import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'core/theme.dart';
import 'providers/auth_provider.dart';
import 'screens/login_screen.dart';
import 'screens/shell_screen.dart';
import 'services/auth_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final prefs = await SharedPreferences.getInstance();
  final authService = AuthService(prefs);
  runApp(SocietyApp(authService: authService));
}

class SocietyApp extends StatelessWidget {
  const SocietyApp({super.key, required this.authService});

  final AuthService authService;

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (_) => AuthProvider(authService)..init(),
      child: Consumer<AuthProvider>(
        builder: (context, auth, _) {
          return MaterialApp(
            title: 'Society App',
            debugShowCheckedModeBanner: false,
            theme: AppTheme.light(),
            darkTheme: AppTheme.dark(),
            themeMode: auth.darkMode ? ThemeMode.dark : ThemeMode.light,
            home: auth.loading && auth.isLoggedIn
                ? const Scaffold(body: Center(child: CircularProgressIndicator()))
                : auth.isLoggedIn && auth.user != null
                    ? const ShellScreen()
                    : const LoginScreen(),
          );
        },
      ),
    );
  }
}
