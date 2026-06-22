import 'package:flutter/material.dart';

/// Society Analytics Dashboard — royal blue → cyan gradient
class AppTheme {
  static const Color primary = Color(0xFF3949AB);
  static const Color primaryLight = Color(0xFF00BCD4);
  static const Color accent = Color(0xFF5C6BC0);
  static const Color surfaceLight = Color(0xFFE8EAF6);
  static const Color danger = Color(0xFFDC2626);

  static const LinearGradient brandGradient = LinearGradient(
    colors: [primary, primaryLight],
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
  );

  static ThemeData light() {
    final base = ColorScheme.fromSeed(seedColor: primary, brightness: Brightness.light, surface: Colors.white);
    return ThemeData(
      useMaterial3: true,
      colorScheme: base.copyWith(primary: primary, secondary: primaryLight),
      scaffoldBackgroundColor: const Color(0xFFF5F7FB),
      appBarTheme: const AppBarTheme(
        centerTitle: true,
        elevation: 0,
        backgroundColor: primary,
        foregroundColor: Colors.white,
      ),
      cardTheme: CardThemeData(
        elevation: 0,
        color: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        margin: EdgeInsets.zero,
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: primary,
          foregroundColor: Colors.white,
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: primaryLight, width: 2)),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      ),
      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: Colors.white,
        indicatorColor: primary.withValues(alpha: 0.12),
        labelTextStyle: WidgetStateProperty.all(const TextStyle(fontSize: 11)),
      ),
      drawerTheme: const DrawerThemeData(backgroundColor: Colors.white),
    );
  }

  static ThemeData dark() {
    final base = ColorScheme.fromSeed(seedColor: primaryLight, brightness: Brightness.dark);
    return ThemeData(
      useMaterial3: true,
      colorScheme: base,
      appBarTheme: const AppBarTheme(centerTitle: true, elevation: 0, backgroundColor: Color(0xFF283593)),
    );
  }

  static LinearGradient headerGradient(BuildContext context) => brandGradient;
}
