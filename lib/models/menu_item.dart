class MenuItem {
  MenuItem({required this.id, required this.title, required this.icon, required this.route});

  final String id;
  final String title;
  final String icon;
  final String route;

  factory MenuItem.fromJson(Map<String, dynamic> j) => MenuItem(
        id: j['id']?.toString() ?? '',
        title: j['title']?.toString() ?? '',
        icon: j['icon']?.toString() ?? 'home',
        route: j['route']?.toString() ?? '/home',
      );
}
