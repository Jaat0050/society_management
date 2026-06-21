import 'package:flutter/material.dart';

IconData menuIcon(String name) {
  const map = {
    'home': Icons.home_rounded,
    'receipt': Icons.receipt_long_rounded,
    'report': Icons.report_problem_rounded,
    'people': Icons.people_rounded,
    'pool': Icons.pool_rounded,
    'campaign': Icons.campaign_rounded,
    'person': Icons.person_rounded,
    'emergency': Icons.sos_rounded,
    'security': Icons.security_rounded,
    'qr_code': Icons.qr_code_scanner_rounded,
    'add': Icons.person_add_alt_1_rounded,
    'groups': Icons.groups_rounded,
    'list': Icons.list_alt_rounded,
    'build': Icons.build_circle_rounded,
    'calendar': Icons.calendar_month_rounded,
    'check': Icons.fact_check_rounded,
    'dashboard': Icons.dashboard_rounded,
    'analytics': Icons.analytics_rounded,
    'money': Icons.payments_rounded,
    'book': Icons.menu_book_rounded,
    'work': Icons.work_rounded,
    'description': Icons.description_rounded,
    'family': Icons.family_restroom_rounded,
    'car': Icons.directions_car_rounded,
    'folder': Icons.folder_rounded,
    'payment': Icons.account_balance_wallet_rounded,
    'event': Icons.event_rounded,
    'phone': Icons.phone_in_talk_rounded,
    'notifications': Icons.notifications_rounded,
    'delivery': Icons.delivery_dining_rounded,
    'login': Icons.login_rounded,
    'logout': Icons.logout_rounded,
    'warning': Icons.warning_amber_rounded,
    'badge': Icons.badge_rounded,
    'inventory': Icons.inventory_2_rounded,
    'store': Icons.store_rounded,
    'chat': Icons.forum_rounded,
  };
  return map[name] ?? Icons.apps_rounded;
}

Color menuColor(String id) {
  const colors = {
    'sos': Color(0xFFDC2626),
    'complaints': Color(0xFFEA580C),
    'bills': Color(0xFF3949AB),
    'visitors': Color(0xFF5C6BC0),
    'amenities': Color(0xFF00BCD4),
    'tasks': Color(0xFF00897B),
    'chat': Color(0xFF3949AB),
    'gate_pass': Color(0xFF00BCD4),
  };
  return colors[id] ?? const Color(0xFF3949AB);
}
