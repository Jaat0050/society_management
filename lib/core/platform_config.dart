import 'package:flutter/foundation.dart' show kIsWeb;

/// API base URL defaults per platform (Chrome / web vs Android emulator).
class PlatformConfig {
  /// Laragon subfolder — same origin friendly if you use a proxy; otherwise CORS is enabled on API.
  static const String webApiUrl = 'https://pretty-mauve-bison.103-191-209-182.cpanel.site/api';

  /// Virtual host (uncomment in login if you use society.test in Laragon)
  static const String webApiUrlVhost = 'https://pretty-mauve-bison.103-191-209-182.cpanel.site/api';

  static const String androidApiUrl = 'http://10.0.2.2/society/public/api/v1';

  static String get defaultApiUrl => kIsWeb ? webApiUrl : androidApiUrl;

  static String get platformLabel => kIsWeb ? 'Chrome / Web' : 'Mobile';
}
