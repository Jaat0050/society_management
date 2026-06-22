# Society Mobile App (Flutter)

Role-based customer app for Green Valley Society. Each login sees **only menus returned by the API** for that role.

## Setup

1. Start Laragon and ensure backend runs at e.g. `http://society.test` or `http://localhost/society/public`
2. Run migrations & seed: `php artisan migrate --seed` (includes `MobileAppSeeder`)
3. From this folder:

```bash
flutter pub get
flutter run -d chrome --web-port=7357
```

Or double-click **`run_chrome.bat`** (Windows).

## API URL

**Chrome / Web (default):** `http://localhost/society/public/api/v1`  
**Laragon vhost:** `http://society.test/api/v1` (change in login → API settings)

**Android emulator:** `http://10.0.2.2/society/public/api/v1`

Change on login screen → **API / server URL**, or in Settings.

Physical device: use your PC LAN IP, e.g. `http://192.168.1.10/society/public/api/v1`

## Payment gateway

**Disabled** — bills show "Office pay" chip; no online pay button action.

## Demo logins

Password: `password`

| Role | Email |
|------|--------|
| Resident | resident@greenvalley.test |
| Tenant | tenant@greenvalley.test |
| Guard | guard@greenvalley.test |
| Staff | staff@greenvalley.test |
| Committee | committee@greenvalley.test |
| Accountant | accountant@greenvalley.test |
| Vendor | vendor@greenvalley.test |

See [MOBILE_API.md](../MOBILE_API.md) for full endpoint list.
