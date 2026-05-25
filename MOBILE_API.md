# Society Mobile API (v1)

Base URL examples:
- Laragon virtual host: `http://society.test/api/v1`
- Subfolder: `http://localhost/society/public/api/v1`
- Android emulator → host: `http://10.0.2.2/society/public/api/v1`

All responses: `{ "success": true|false, "message": "...", "data": ... }`

Auth header: `Authorization: Bearer {token}`

**Online payment is disabled** (`payment_gateway_enabled: false`). `POST /resident/bills/{id}/pay` returns 503.

## Auth

| Method | Path | Body |
|--------|------|------|
| POST | `/auth/login` | `login`, `password` |
| POST | `/auth/otp/send` | `phone` |
| POST | `/auth/otp/verify` | `phone`, `otp` (dev: `123456`) |
| GET | `/auth/me` | — |
| POST | `/auth/logout` | — |
| GET | `/config/features` | role menus + feature flags (filtered by role) |
| GET | `/config/app` | broadcast + chat settings |
| GET | `/notifications` | society alerts (all mobile roles) |

## Chat (community / socket-ready)

| GET | `/chat/rooms` |
| GET | `/chat/rooms/{id}/messages?after_id=` |
| POST | `/chat/rooms/{id}/messages` | `body` |
| GET | `/chat/rooms/{id}/stream` | SSE live stream (Chrome) |

Messages broadcast on channel `society.chat.{roomId}` when Pusher/Reverb enabled.

## Gate pass

| POST | `/resident/visitors` | returns `gate_pass` with QR, pass_code, OTP |
| GET | `/resident/visitors/{id}/pass` |
| POST | `/guard/entries` | returns `gate_pass` |
| PUT | `/preferences` | `locale`, `dark_mode` |

## Profile (all mobile roles)

| PUT | `/profile` | name, phone |
| PUT | `/profile/member` | member fields |
| POST | `/profile/family` | family member |
| POST | `/profile/vehicle` | vehicle |

## Resident / Tenant (`api.role: resident, tenant`)

| GET | `/resident/dashboard` |
| GET | `/resident/bills` |
| GET | `/resident/payments/history` |
| POST | `/resident/bills/{id}/pay` | **disabled** |
| GET/POST | `/resident/complaints` |
| POST | `/resident/complaints/{id}/rate` |
| GET/POST | `/resident/visitors` |
| POST | `/resident/visitors/{id}/approve` |
| GET | `/resident/notices`, `/events` |
| GET/POST/DELETE | `/resident/amenities`, `/bookings` |
| POST | `/resident/sos` |
| GET | `/resident/emergency-contacts` |

## Guard

| GET | `/guard/dashboard` |
| POST | `/guard/entries`, `/verify` |
| POST | `/guard/entries/{id}/check-in`, `/check-out` |
| GET | `/guard/inside`, `/log`, `/delivery-staff`, `/flats` |
| GET/POST | `/guard/sos`, `/sos/{id}/resolve` |
| POST | `/guard/incidents`, `/verify-vehicle`, `/attendance` |

## Staff

| GET | `/staff/tasks` |
| PUT | `/staff/tasks/{id}` |
| GET | `/staff/schedule` |
| POST | `/staff/attendance`, `/inventory` |

## Committee

| GET | `/committee/overview`, `/complaints`, `/notices`, `/financial` |

## Accountant

| GET | `/accountant/billing`, `/expenses`, `/ledger`, `/vendors`, `/reports` |
| POST | `/accountant/expenses` |

## Vendor

| GET | `/vendor/tasks`, `/schedule`, `/payments` |
| PUT | `/vendor/tasks/{id}` |
| POST | `/vendor/invoices` |

## Demo users (password: `password`)

| Role | Email |
|------|--------|
| Resident | `resident@greenvalley.test` |
| Tenant | `tenant@greenvalley.test` |
| Guard | `guard@greenvalley.test` |
| Staff | `staff@greenvalley.test` |
| Committee | `committee@greenvalley.test` |
| Accountant | `accountant@greenvalley.test` |
| Vendor | `vendor@greenvalley.test` |

## Flutter app

```bash
cd mobile_app
flutter pub get
flutter run
```

Role menus come from `GET /config/features` — only features for that role are shown.
