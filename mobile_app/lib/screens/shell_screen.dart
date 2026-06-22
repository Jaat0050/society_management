import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../core/icon_map.dart';
import '../models/menu_item.dart';
import '../providers/auth_provider.dart';
import 'hub_screen.dart';
import 'login_screen.dart';
import 'module_router.dart';
import 'settings_screen.dart';

class ShellScreen extends StatefulWidget {
  const ShellScreen({super.key});

  @override
  State<ShellScreen> createState() => _ShellScreenState();
}

class _ShellScreenState extends State<ShellScreen> {
  String _route = '/home';

  void _openModule(MenuItem menu) => setState(() => _route = menu.route);

  void _goHome() => setState(() => _route = '/home');

  /// Bottom bar only shows first [maxItems] menus — index must match destinations length.
  Widget? _buildBottomNav(List<MenuItem> menus) {
    const maxItems = 4;
    if (menus.length <= 1) return null;

    final navMenus = menus.take(maxItems).toList();
    final routeInBar = navMenus.any((m) => m.route == _route);

    // Drawer-only modules (not in bottom bar) — hide bar to avoid selectedIndex crash
    if (!routeInBar && _route != '/home') return null;

    var selected = navMenus.indexWhere((m) => m.route == _route);
    if (selected < 0) selected = 0;

    return NavigationBar(
      selectedIndex: selected,
      onDestinationSelected: (i) {
        if (i >= 0 && i < navMenus.length) _openModule(navMenus[i]);
      },
      destinations: navMenus
          .map((m) => NavigationDestination(
                icon: Icon(menuIcon(m.icon)),
                label: _short(m.title),
              ))
          .toList(),
    );
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final menus = auth.menus;
    if (menus.isEmpty) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    final onHub = _route == '/home';
    final title = onHub ? 'Home' : ModuleRouter.titleFor(_route);

    return Scaffold(
      appBar: AppBar(
        title: Text(title),
        leading: onHub
            ? null
            : IconButton(icon: const Icon(Icons.arrow_back_rounded), onPressed: _goHome),
        actions: [
          IconButton(
            icon: const Icon(Icons.settings_outlined),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const SettingsScreen())),
          ),
          IconButton(
            icon: const Icon(Icons.logout_rounded),
            onPressed: () async {
              await auth.logout();
              if (!context.mounted) return;
              Navigator.of(context).pushAndRemoveUntil(
                MaterialPageRoute(builder: (_) => const LoginScreen()),
                (_) => false,
              );
            },
          ),
        ],
      ),
      drawer: Drawer(
        child: Column(
          children: [
            UserAccountsDrawerHeader(
              currentAccountPicture: CircleAvatar(
                backgroundColor: Colors.white24,
                child: Text(
                  auth.user?.name.isNotEmpty == true ? auth.user!.name[0] : '?',
                  style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                ),
              ),
              accountName: Text(auth.user?.name ?? '', style: const TextStyle(fontWeight: FontWeight.bold)),
              accountEmail: Text('${auth.user?.roleLabel ?? ''}\n${auth.user?.societyName ?? ''}'),
            ),
            Expanded(
              child: ListView(
                padding: EdgeInsets.zero,
                children: menus.map((m) {
                  final selected = _route == m.route;
                  return ListTile(
                    leading: Icon(menuIcon(m.icon), color: selected ? Theme.of(context).colorScheme.primary : null),
                    title: Text(m.title),
                    selected: selected,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    onTap: () {
                      Navigator.pop(context);
                      _openModule(m);
                    },
                  );
                }).toList(),
              ),
            ),
          ],
        ),
      ),
      body: onHub
          ? HubScreen(onModuleTap: _openModule)
          : ModuleRouter.build(_route, auth.user?.role ?? ''),
      bottomNavigationBar: _buildBottomNav(menus),
    );
  }

  String _short(String t) => t.length > 10 ? '${t.substring(0, 9)}…' : t;
}
