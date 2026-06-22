import 'package:shared_preferences/shared_preferences.dart';

import '../core/api_client.dart';
import '../core/constants.dart';
import '../models/features_result.dart';
import '../models/menu_item.dart';
import '../models/user_model.dart';

class AuthService {
  AuthService(this._prefs);

  final SharedPreferences _prefs;
  ApiClient? _client;

  String get baseUrl => _prefs.getString(ApiConstants.baseUrlKey) ?? ApiConstants.defaultBaseUrl;

  String? get token => _prefs.getString(ApiConstants.tokenKey);

  ApiClient get client {
    _client ??= ApiClient(baseUrl: baseUrl, token: token);
    return _client!;
  }

  void _refreshClient() {
    _client = ApiClient(baseUrl: baseUrl, token: token);
  }

  Future<void> setBaseUrl(String url) async {
    await _prefs.setString(ApiConstants.baseUrlKey, url);
    _refreshClient();
  }

  Future<UserModel> login(String login, String password) async {
    final json = await client.post('/auth/login', body: {'login': login, 'password': password});
    final data = json['data'] as Map<String, dynamic>;
    await _prefs.setString(ApiConstants.tokenKey, data['token'] as String);
    _refreshClient();
    return UserModel.fromJson(data['user'] as Map<String, dynamic>);
  }

  Future<UserModel> verifyOtp(String phone, String otp) async {
    final json = await client.post('/auth/otp/verify', body: {'phone': phone, 'otp': otp});
    final data = json['data'] as Map<String, dynamic>;
    await _prefs.setString(ApiConstants.tokenKey, data['token'] as String);
    _refreshClient();
    return UserModel.fromJson(data['user'] as Map<String, dynamic>);
  }

  Future<Map<String, dynamic>> sendOtp(String phone) async {
    final json = await client.post('/auth/otp/send', body: {'phone': phone});
    return json['data'] as Map<String, dynamic>;
  }

  Future<UserModel> me() async {
    final json = await client.get('/auth/me');
    return UserModel.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<FeaturesResult> features() async {
    final json = await client.get('/config/features');
    final data = json['data'] as Map<String, dynamic>;
    final menus = (data['menus'] as List<dynamic>? ?? [])
        .map((e) => MenuItem.fromJson(e as Map<String, dynamic>))
        .toList();
    final features = (data['features'] as List<dynamic>? ?? []).map((e) => e.toString()).toList();
    return FeaturesResult(
      menus: menus,
      features: features,
      paymentGateway: data['payment_gateway_enabled'] == true,
    );
  }

  Future<void> logout() async {
    try {
      await client.post('/auth/logout');
    } catch (_) {}
    await _prefs.remove(ApiConstants.tokenKey);
    _refreshClient();
  }

  Future<void> updatePreferences({bool? darkMode, String? locale}) async {
    await client.put('/preferences', body: {
      if (darkMode != null) 'dark_mode': darkMode,
      if (locale != null) 'locale': locale,
    });
  }

  bool get isLoggedIn => token != null && token!.isNotEmpty;
}
