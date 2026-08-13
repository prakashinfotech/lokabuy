# Coding Standards

## PHP

- `declare(strict_types=1)` must be the first statement in every PHP file, after the opening `<?php` tag — no exceptions
- PSR-4 naming: one class per file, class name matches filename
- PSR-12 formatting enforced by Laravel Pint — run `./vendor/bin/pint` before committing
- No raw SQL for data mutations — Eloquent only
- No `DB::statement()` or `DB::unprepared()` for data writes
- No inline `if ($user->id !== $listing->user_id)` authorization — use Policies
- Never store secrets in code — `.env` only, accessed via `config()` helpers, never `env()` directly in application code
- No `dd()`, `dump()`, `var_dump()` left in committed code

## Architecture Rules

- **Fat Services, thin Controllers**: business logic lives in `app/Services/` — controllers only call services and return responses
- **Form Requests for all validation**: never use `$request->validate()` inside a controller
- **API Resources for all JSON output**: never return `$model->toArray()`, `->toJson()`, or raw arrays from API controllers
- **Route model binding** over manual `Model::findOrFail($id)` wherever possible
- **Policies** for all authorization checks — register every policy in `AuthServiceProvider`
- **Notifications** via Laravel Notification classes in `app/Notifications/` — never call `Mail::send()` directly

## Naming Conventions

| Thing | Convention | Example |
|-------|-----------|---------|
| Model | PascalCase singular | `Listing`, `ListingImage` |
| Controller | PascalCase + Controller | `ListingController` |
| Form Request | Action + Request | `StoreListingRequest`, `UpdateProfileRequest` |
| API Resource | Model + Resource | `ListingResource`, `UserResource` |
| Service | Model/Domain + Service | `ListingService`, `ImageService` |
| Policy | Model + Policy | `ListingPolicy` |
| Migration | snake_case descriptive | `create_listings_table`, `add_featured_until_to_listings_table` |
| Seeder | PascalCase + Seeder | `CategorySeeder`, `AdminUserSeeder` |
| Factory | Model + Factory | `ListingFactory` |
| Blade view | snake_case | `listing-card.blade.php` |
| Route name | dot-separated | `listings.show`, `admin.listings.approve` |

## Blade / Frontend

- All public pages extend `layouts.guest`
- All authenticated user pages extend `layouts.app`
- All admin pages extend `layouts.admin`
- Repeated HTML extracted into `resources/views/partials/` with a leading underscore: `_listing-card.blade.php`, `_filters.blade.php`
- Alpine.js for interactive UI (image galleries, dropdowns, modals, toggles) — keep `x-data` scopes small and focused
- Bootstrap 5 utility classes for layout — no inline `<style>` blocks
- One custom stylesheet: `public/css/app.css` for overrides only
- Images always served via `Storage::url()` — never hardcoded paths

## Git

- Branch naming: `feature/<short-name>`, `fix/<short-name>`, `chore/<short-name>`
- Commit message format: `type: short description` — e.g., `feat: add listing approval workflow`, `fix: slug collision on title edit`, `chore: seed subcategories`
- Never commit: `.env`, `storage/app/`, `vendor/`, OS-specific files (`.DS_Store`, `Thumbs.db`)
- `main` = production branch; `develop` = integration branch; all feature work branches off `develop`

## Comments

- Write no comments by default
- Add a comment only when the **why** is non-obvious: a hidden constraint, a workaround, a subtle invariant
- Never write comments that describe what the code does — well-named identifiers do that
