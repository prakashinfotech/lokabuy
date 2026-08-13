# Tech Stack

## Backend
- **Framework**: Laravel 8
- **Language**: PHP 8.1
- **ORM**: Laravel Eloquent
- **Auth**: Laravel Sanctum (JWT Bearer tokens for API, session for web)
- **Cache / Queue**: Redis via `predis/predis`
- **Code Style**: Laravel Pint (`pint.json` at project root)
- **API base path**: `/api/v1`

## Frontend
- **Templating**: Laravel Blade
- **CSS Framework**: Bootstrap 5 — never mix with Tailwind; pick one and stay consistent
- **JavaScript**: Vanilla JS + Alpine.js
- **Icons**: Bootstrap Icons
- **Build pipeline**: None — no npm/Node build step unless explicitly agreed by the user

## Database
- **Engine**: MySQL 8
- **Schema management**: Laravel migrations only — never alter the database manually
- **Seeding**: Laravel seeders via `php artisan db:seed`

## Infrastructure
- **Local dev**: Laragon (no Docker)
- **Cache**: Redis (also used for rate limiting and session tokens)
- **File storage**: Local public disk (`storage/app/public`) — switchable to S3 via `FILESYSTEM_DRIVER`
- **Mail**: Configurable transactional provider (SendGrid / AWS SES) via `.env`

## Development Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Approved Composer Packages
The following packages are pre-approved. Ask the user before adding anything not on this list.

| Package | Purpose |
|---------|---------|
| `laravel/sanctum` | API token authentication |
| `predis/predis` | Redis client |
| `laravel/pint` | Code style enforcement |
| `barryvdh/laravel-ide-helper` | IDE autocompletion (dev only) |
| `nunomaduro/collision` | Better CLI error output (dev only) |
| `fakerphp/faker` | Test data generation (dev only) |
| `phpunit/phpunit` | Testing (dev only) |

## Environment Variables Reference
```dotenv
APP_NAME="Lokabuy"
APP_URL=http://lokabuy.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lokabuy
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=file

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@lokabuy.com
MAIL_FROM_NAME="Lokabuy"

FILESYSTEM_DRIVER=public
SANCTUM_STATEFUL_DOMAINS=localhost,lokabuy.test
CORS_ALLOWED_ORIGINS=http://localhost,http://lokabuy.test

ADMIN_EMAIL=admin@lokabuy.com
ADMIN_PASSWORD=secret
```
