import 'menu_item.dart';

class FeaturesResult {
  FeaturesResult({required this.menus, required this.features, required this.paymentGateway});

  final List<MenuItem> menus;
  final List<String> features;
  final bool paymentGateway;
}
