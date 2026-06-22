import 'package:flutter/foundation.dart';

import '../core/api_client.dart';
import '../models/menu_item.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';

class AuthProvider extends ChangeNotifier {
  AuthProvider(this._auth);

  final AuthService _auth;

  UserModel? user;
  List<MenuItem> menus = [];
  List<String> features = [];
  bool paymentGatewayEnabled = false;
  bool loading = false;
  String? error;
  bool darkMode = false;

  bool get isLoggedIn => _auth.isLoggedIn;

  Future<void> init() async {
    if (!isLoggedIn) return;
    loading = true;
    notifyListeners();
    try {
      user = await _auth.me();
      darkMode = user?.darkMode ?? false;
      await _loadFeatures();
    } catch (e) {
      await _auth.logout();
      user = null;
    }
    loading = false;
    notifyListeners();
  }

  Future<bool> login(String login, String password) async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      user = await _auth.login(login, password);
      darkMode = user?.darkMode ?? false;
      await _loadFeatures();
      loading = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      error = e.message;
      loading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> loginOtp(String phone, String otp) async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      user = await _auth.verifyOtp(phone, otp);
      darkMode = user?.darkMode ?? false;
      await _loadFeatures();
      loading = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      error = e.message;
      loading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> _loadFeatures() async {
    final f = await _auth.features();
    menus = f.menus;
    features = f.features;
    paymentGatewayEnabled = f.paymentGateway;
  }

  bool hasFeature(String key) => features.contains(key);

  Future<void> logout() async {
    await _auth.logout();
    user = null;
    menus = [];
    features = [];
    notifyListeners();
  }

  Future<void> toggleDarkMode(bool value) async {
    darkMode = value;
    await _auth.updatePreferences(darkMode: value);
    notifyListeners();
  }

  Future<void> setBaseUrl(String url) async {
    await _auth.setBaseUrl(url);
  }

  Future<void> refreshUser() async {
    if (!isLoggedIn) return;
    try {
      user = await _auth.me();
      notifyListeners();
    } catch (_) {}
  }

  String get baseUrl => _auth.baseUrl;
  AuthService get authService => _auth;
}
