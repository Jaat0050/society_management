# society-managment

Society Management — Laravel admin panels + Flutter mobile app (role-based).

# Society Manager — Admin Panels

Laravel 10 + Livewire 4 web admin panels based on `Book1.xlsx` requirements.

**Phase 1 (current):** Super Admin + Society Admin web panels  
**Phase 2 (later):** Flutter apps (Resident, Guard)

## Setup (Laragon)

1. Create MySQL database: `society`
2. Copy `.env.example` to `.env` and set `DB_*` for MySQL
3. Run:

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Or use Laragon virtual host: `http://society.test`

## Login credentials (after seed)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@society.test | password |
| Society Admin | admin@greenvalley.test | password |

Login: `/login` — redirects by role automatically.

## Super Admin Panel (`/super-admin`)

- Global dashboard
- Create / edit / delete / block societies
- Assign society admins
- Subscription plans
- Global notifications
- Reports & analytics
- App settings

## Society Admin Panel (`/admin`)

- Society dashboard
- Towers / blocks
- Flats
- Residents
- Staff
- Complaints
- Notice board
- Events
- Maintenance billing & payment tracking
- Parking
- Amenity booking
- Society settings (visitor approval, etc.)
- Reports

## Roles (from Book1)

- `super_admin` — platform control
- `admin` — single society management
- `resident`, `guard`, `staff`, `committee` — reserved for Flutter apps
