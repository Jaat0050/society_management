import 'platform_config.dart';

class ApiConstants {
  static String get defaultBaseUrl => PlatformConfig.defaultApiUrl;

  static const String tokenKey = 'auth_token';
  static const String baseUrlKey = 'api_base_url';
}
