# Task List — Lokabuy (Phase 1)

Tasks are ordered by dependency. Execute top-to-bottom. Do not start a task until all tasks above it are done.

Legend: `[ ]` pending · `[x]` done · `[-]` skipped

---

## Group 1 — Project Setup

- [x] **T01** Scaffold Laravel 8 project into the current directory
- [x] **T02** Install core Composer packages: `laravel/sanctum`, `predis/predis`, `laravel/pint`
- [x] **T03** Create `.env.example` with all required keys (from `tech-stack.md`) and inline comments
- [x] **T04** Configure `.env` for local Laragon setup (MySQL, Redis)
- [x] **T05** Create `pint.json` with Laravel Pint rules
- [x] **T06** Configure Laravel Sanctum (publish config, add middleware to `api` group in `Kernel.php`)
- [x] **T07** Configure Redis as cache and queue driver in `config/cache.php` and `config/queue.php`
- [x] **T08** Configure CORS in `config/cors.php` using `CORS_ALLOWED_ORIGINS` env variable
- [-] **T09** Initialize git — skipped (user handles git manually)
- [x] **T10** Write `README.md` (project description, setup steps, env variables, how to run)

---

## Group 2 — Database Migrations

- [x] **T11** Migration: `users` table (all columns, indexes)
- [x] **T12** Migration: `categories` table
- [x] **T13** Migration: `subcategories` table (FK → categories)
- [x] **T14** Migration: `listings` table (all columns, FULLTEXT index on title+description, status indexes)
- [x] **T15** Migration: `listing_images` table (FK → listings, cascade delete)
- [x] **T16** Migration: `favorites` table (FK → users + listings, unique index)
- [x] **T17** Migration: `messages` table (schema only — no API or UI in Phase 1)
- [x] **T18** Migration: `reports` table (FK → listings + users)
- [x] **T19** Verify: `php artisan migrate` runs all migrations cleanly on a fresh database

---

## Group 3 — Eloquent Models

- [x] **T20** Model: `User` (`$fillable`, `$casts`, `$hidden`, relationships, role enum)
- [x] **T21** Model: `Category` (`$fillable`, `$casts`, relationships, `scopeActive()`)
- [x] **T22** Model: `Subcategory` (`$fillable`, `$casts`, relationships, `scopeActive()`)
- [x] **T23** Model: `Listing` (`$fillable`, `$casts`, `SoftDeletes`, relationships, `scopeActive()`, `scopeApproved()`, `scopeFeatured()`, `getRouteKeyName()` → slug)
- [x] **T24** Model: `ListingImage` (`$fillable`, `$casts`, relationship to Listing)
- [x] **T25** Model: `Favorite` (`$fillable`, relationships to User and Listing)
- [x] **T26** Model: `Message` (`$fillable`, `$casts`, relationships — schema only)
- [x] **T27** Model: `Report` (`$fillable`, `$casts`, relationships)

---

## Group 4 — Seeders

- [x] **T28** `CategorySeeder` — seed all 10 top-level categories with slug and sort order
- [x] **T29** `SubcategorySeeder` — seed subcategories for all 10 categories
- [x] **T30** `AdminUserSeeder` — seed one admin user from `ADMIN_EMAIL` / `ADMIN_PASSWORD` env values
- [x] **T31** Wire `DatabaseSeeder` to call all seeders in order
- [x] **T32** Verify: `php artisan migrate --seed` runs without errors

---

## Group 5 — Blade Layouts & Partials

- [x] **T33** Layout: `layouts/guest.blade.php` (public pages — navbar with login/register, footer)
- [x] **T34** Layout: `layouts/app.blade.php` (authenticated pages — navbar with user menu)
- [x] **T35** Layout: `layouts/admin.blade.php` (admin pages — sidebar navigation)
- [x] **T36** Partial: `partials/_navbar.blade.php`
- [x] **T37** Partial: `partials/_footer.blade.php`
- [x] **T38** Partial: `partials/_alert.blade.php` (success / error flash messages)
- [x] **T39** Partial: `partials/_pagination.blade.php`
- [x] **T40** Error pages: `errors/403.blade.php`, `errors/404.blade.php`, `errors/500.blade.php`

---

## Group 6 — Exception Handler

- [x] **T41** Customize `app/Exceptions/Handler.php` to return JSON envelope for all API errors (401, 403, 404, 422, 500)
- [x] **T42** Register fallback route in `routes/api.php` to return 404 JSON for undefined API routes

---

## Group 7 — Authentication

- [x] **T43** Form Requests: `RegisterRequest`, `LoginRequest`, `ForgotPasswordRequest`, `ResetPasswordRequest`
- [x] **T44** API Resource: `UserResource`
- [x] **T45** API: `AuthController` — `register`, `login`, `logout`, `refresh`
- [x] **T46** API: `PasswordResetController` — `forgot`, `reset`
- [x] **T47** Web: Auth Blade views — `auth/login.blade.php`, `auth/register.blade.php`
- [x] **T48** Web: Password views — `auth/passwords/email.blade.php`, `auth/passwords/reset.blade.php`
- [x] **T49** Web: `Web/Auth/AuthController` (login, register, logout for Blade flow)
- [x] **T50** Register all auth routes in `routes/api.php` and `routes/web.php`

---

## Group 8 — Categories

- [x] **T51** API Resource: `CategoryResource` (includes subcategories)
- [x] **T52** API: `CategoryController` — `index` (all active categories + subcategories, cached)
- [x] **T53** Web: Category browse page — `listings/index.blade.php` filtered by category
- [x] **T54** Register category routes in `routes/api.php` and `routes/web.php`

---

## Group 9 — Listing Core (CRUD)

- [x] **T55** `ListingService` — `create()` with slug generation (title + 6-char random suffix)
- [x] **T56** `ListingService` — `update()`, `delete()` (soft delete), `markAsSold()`, `renew()`
- [x] **T57** `ImageService` — `store()`, `delete()`, `deleteAll()` with validation (type, size)
- [x] **T58** Form Requests: `StoreListingRequest`, `UpdateListingRequest`
- [x] **T59** `ListingPolicy` — `update`, `delete`, `markAsSold`, `renew` (owns listing check)
- [x] **T60** API Resource: `ListingResource`, `ListingCollection`
- [x] **T61** API: `ListingController` — `store`, `show`, `update`, `destroy`, `markAsSold`, `renew`
- [x] **T62** Register listing routes in `routes/api.php`
- [x] **T63** Web: `listings/create.blade.php` — post ad form (title, description, price, category, subcategory, city, state, listing_type conditional on Real Estate)
- [x] **T64** Web: `listings/edit.blade.php` — edit ad form
- [x] **T65** Web: `listings/my-listings.blade.php` — user's own listings with status badges and action buttons

---

## Group 10 — Listing Images

- [x] **T66** Form Request: `StoreListingImageRequest` (MIME, size, max 10 per listing)
- [x] **T67** API: `ListingImageController` — `store`, `destroy`
- [x] **T68** Register image routes in `routes/api.php`

---

## Group 11 — Search & Browse

- [x] **T69** `SearchService` — build filter chain (keyword FULLTEXT, category, subcategory, city, state, price range, listing_type), featured-first ordering, sort, paginate
- [x] **T70** API: `GET /api/v1/listings` — wire to `SearchService`, return paginated `ListingCollection`
- [x] **T71** Web: `listings/index.blade.php` — browse/search results with filter sidebar, sort dropdown, pagination
- [x] **T72** Autocomplete endpoint: `GET /api/v1/listings/autocomplete?q=` — returns matching titles (cached 60s)
- [x] **T73** Alpine.js autocomplete on search bar (300ms debounce)

---

## Group 12 — Listing Detail Page

- [x] **T74** Web: `listings/show.blade.php` — full listing detail (title, description, price, images gallery, category, location, seller info, posting date)
- [x] **T75** View counter: `ListingService::incrementViewCount()` called on show
- [x] **T76** Related listings: fetch up to 6 active listings from same subcategory, display at bottom
- [x] **T77** "Contact Seller" button: redirect guest to login with return URL; show message UI placeholder for logged-in users (Phase 2 full messaging)
- [x] **T78** Partial: `partials/_listing-card.blade.php` (used on browse, search, homepage, related listings)

---

## Group 13 — Favorites

- [x] **T79** API Resource: `FavoriteResource`
- [x] **T80** API: `FavoriteController` — `index`, `toggle` (add if not exists, remove if exists)
- [x] **T81** Web: `favorites/index.blade.php` — user's saved listings
- [x] **T82** Register favorite routes in `routes/api.php` and `routes/web.php`

---

## Group 14 — Reports

- [x] **T83** Form Request: `StoreReportRequest` (reason enum, listing exists, not own listing)
- [x] **T84** `ReportPolicy` — prevent self-report
- [x] **T85** API: `ReportController` — `store`
- [x] **T86** Report form on listing detail page (Alpine.js modal with predefined reason options)
- [x] **T87** Register report route in `routes/api.php`

---

## Group 15 — User Dashboard & Profile

- [x] **T88** API: `DashboardController` — return stats (active count, sold count, total views, unread messages count)
- [x] **T89** Form Requests: `UpdateProfileRequest`, `UpdateAvatarRequest`
- [x] **T90** API: `ProfileController` — `show`, `update`, `uploadAvatar`
- [x] **T91** Web: `dashboard/index.blade.php` — stats summary + listings tab
- [x] **T92** Web: `profile/edit.blade.php` — profile form with avatar upload
- [x] **T93** Register dashboard and profile routes in `routes/api.php` and `routes/web.php`

---

## Group 16 — Admin Middleware & Base

- [x] **T94** Middleware: `EnsureAdmin` — allow only `role = admin`
- [x] **T95** Middleware: `EnsureModerator` — allow `role = admin` OR `role = moderator`
- [x] **T96** Register both middleware in `app/Http/Kernel.php`

---

## Group 17 — Admin Dashboard & Listing Approval

- [x] **T97** `AdminService` — `approve()`, `reject()`, `setFeatured()`
- [x] **T98** API: `AdminDashboardController` — stats (total users, active listings, pending listings, pending reports, new registrations last 7 days)
- [x] **T99** Form Request: `RejectListingRequest` (requires rejection reason)
- [x] **T100** API: `AdminListingController` — `pending` (approval queue), `approve`, `reject`, `setFeatured`
- [x] **T101** Web: `admin/dashboard.blade.php` — stats overview
- [x] **T102** Web: `admin/listings/index.blade.php` — approval queue with approve/reject actions
- [x] **T103** Register admin listing routes in `routes/api.php` and `routes/web.php`

---

## Group 18 — Admin Reports

- [x] **T104** API: `AdminReportController` — `index` (pending reports), `action` (remove listing + resolve), `dismiss`
- [x] **T105** Web: `admin/reports/index.blade.php` — reports queue with action/dismiss buttons
- [x] **T106** Register admin report routes in `routes/api.php` and `routes/web.php`

---

## Group 19 — Admin Users & Categories

- [x] **T107** API: `AdminUserController` — `index` (paginated 50/page), `deactivate` (set inactive, revoke tokens)
- [x] **T108** Form Requests: `StoreCategoryRequest`, `UpdateCategoryRequest`
- [x] **T109** API: `AdminCategoryController` — `index`, `store`, `update`
- [x] **T110** Web: `admin/users/index.blade.php` — users list with deactivate action
- [x] **T111** Web: `admin/categories/index.blade.php` — categories with add/rename/deactivate
- [x] **T112** Register admin user and category routes in `routes/api.php` and `routes/web.php`

---

## Group 20 — Email Notifications

- [x] **T113** `NotificationService` — wraps Notification dispatch, checks user opt-out before sending
- [x] **T114** `ListingApprovedNotification` — email to listing owner on approval
- [x] **T115** `ListingRejectedNotification` — email to listing owner on rejection with reason
- [x] **T116** `ListingExpiryWarningNotification` — email 7 days before `expires_at` with renewal link
- [x] **T117** `ListingRemovedNotification` — email to listing owner when removed by moderator
- [x] **T118** Wire notifications into `AdminService` (approve, reject, remove) and `NotificationService`
- [x] **T119** Add `email_notifications` boolean column to `users` (migration + model update) for opt-out

---

## Group 21 — Scheduler

- [ ] **T120** Artisan command: `listings:expire` — set `status = expired` where `expires_at < now()` and status is active
- [ ] **T121** Artisan command: `listings:expiry-warning` — dispatch `ListingExpiryWarningNotification` for listings expiring in 7 days
- [ ] **T122** Artisan command: `listings:unfeature` — set `is_featured = false` where `featured_until < now()`
- [ ] **T123** Register all three commands in `app/Console/Kernel.php` to run daily

---

## Group 22 — Redis Caching

- [ ] **T124** Listing cache in `ListingService` — `Cache::remember('listing:{slug}', 300, ...)` on read; `Cache::forget()` on any write
- [ ] **T125** Categories cache in `CategoryController` / `AdminService` — cache indefinitely; bust on any category or subcategory change

---

## Group 23 — Homepage

- [x] **T126** Web: `HomeController` — fetch featured listings (up to 8) and recent listings (up to 16)
- [x] **T127** Web: `home.blade.php` — hero search bar, category grid, featured listings section, recently added listings section
- [x] **T128** Register home route in `routes/web.php`

---

## Group 24 — OpenAPI Spec

- [x] **T129** `docs/openapi.yaml` — document all Phase 1 API endpoints with request/response schemas

---

## Group 25 — Testing

- [ ] **T130** Configure `phpunit.xml` with a dedicated test database; set `CACHE_DRIVER=array`, `QUEUE_CONNECTION=sync` for tests
- [x] **T131** Feature tests: Auth (register, login, logout, refresh, password reset)
- [ ] **T132** Feature tests: Listings (create, update, delete, mark sold, renew, approval flow)
- [ ] **T133** Feature tests: Search (keyword, filters, sort, empty results)
- [ ] **T134** Feature tests: Listing images (upload, delete, size/type validation)
- [ ] **T135** Feature tests: Favorites (toggle add, toggle remove, list)
- [ ] **T136** Feature tests: Reports (store, prevent self-report)
- [ ] **T137** Feature tests: Admin — listing approval (approve, reject)
- [ ] **T138** Feature tests: Admin — reports (action, dismiss)
- [ ] **T139** Feature tests: Admin — users (list, deactivate)
- [ ] **T140** Feature tests: Admin — categories (create, update)
- [ ] **T141** Unit tests: `ListingService` (slug generation, state transitions)
- [ ] **T142** Unit tests: `SearchService` (filter correctness, sort order)
- [ ] **T143** Unit tests: `ImageService` (validation rules, storage path)
- [ ] **T144** Verify: `php artisan test --coverage` passes with ≥ 80% coverage

---

## Group 26 — CI/CD

- [ ] **T145** `.github/workflows/ci.yml` — on PR to `develop` and `main`: run `pint --test` (lint check) then `php artisan test`

---

## Group 27 — QA Fixes & Functional Improvements

### 17.1 — XSS Prevention
- [x] **T146** `StoreListingRequest` — add `prepareForValidation()` calling `strip_tags()` on `title` and `description`
- [x] **T147** `UpdateListingRequest` — same as T146
- [x] **T148** `RegisterRequest` — add `prepareForValidation()` calling `strip_tags()` on `name`
- [x] **T149** `UpdateProfileRequest` — add `prepareForValidation()` calling `strip_tags()` on `name`

### 17.2 — Registration Validation
- [x] **T150** `RegisterRequest` — strengthen password: use Laravel `Password` rule, min 10, uppercase, lowercase, digit, symbols
- [x] **T151** `RegisterRequest` — phone regex: `nullable|regex:/^\+?[\d\s\-]{7,15}$/`
- [x] **T152** `UpdateProfileRequest` — apply same phone regex
- [x] **T153** `ResetPasswordRequest` — apply same password strength rule
- [x] **T154** `User` model — implement `MustVerifyEmail`; add activation email on register; add resend-verification route
- [x] **T155** Listing create routes (web + API) — add `verified` middleware guard; redirect unverified users with message

### 17.3 — Login Security
- [x] **T156** Web `AuthController::login` — `RateLimiter`: max 3 attempts per `email|ip`, 900s lockout
- [x] **T157** API `AuthController::login` — same `RateLimiter` logic
- [x] **T158** "Remember Me" — Sanctum expiration kept null; web remember-me via `Auth::attempt($credentials, $remember)` (5-year cookie); documented in `config/sanctum.php`
- [x] **T159** Forgot-password — verified: `config/auth.php` has `expire: 60` (1h); `Password::reset()` invalidates token after use

### 17.4 — Location Country Field
- [x] **T160** Migration: add `location_country string(100) default 'India'` to `listings` after `location_state`
- [x] **T161** `Listing` model — add `location_country` to `$fillable`
- [x] **T162** `StoreListingRequest` — add `location_country: required|string|max:100`
- [x] **T163** `UpdateListingRequest` — same
- [x] **T164** `ListingResource` — include `location_country` in output
- [x] **T165** `listings/create.blade.php` — add Country input after State (default "India")
- [x] **T166** `listings/edit.blade.php` — add Country input after State (pre-filled)
- [x] **T167** `listings/show.blade.php` — display country in the location meta line

### 17.5 — Price Field Browser Enforcement
- [x] **T168** `listings/create.blade.php` — price input: `type="number" min="0" step="0.01"` *(already correct — no change needed)*
- [x] **T169** `listings/edit.blade.php` — same *(already correct — no change needed)*

### 17.6 — Search & Filter Improvements
- [x] **T170** `SearchService` — add `applyCountry()` method for `country` param (case-insensitive `LIKE` on `location_country`)
- [x] **T171** `SearchService::applyKeyword()` — also OR-match category name and subcategory name via `orWhereHas`
- [x] **T172** `listings/index.blade.php` — add subcategory dropdown to sidebar, populated by selected category's subcategories; hidden when no category selected
- [x] **T173** `listings/index.blade.php` — add Country text input to sidebar below State
- [x] **T174** `listings/index.blade.php` — add missing active-filter chips: state, price_min, price_max, listing_type, country, subcategory
- [x] **T175** Web `ListingController::index()` — pass `$subcategories` (for selected category) to view
- [x] **T176** `SearchService` — add `whereHas('category', active check)` and `subcategory active check` to `scopeVisible()`/base query to exclude inactive-category listings

### 17.7 — Listing Detail Page Cleanup
- [x] **T177** `listings/show.blade.php` — remove Subcategory row from Ad Details right panel
- [x] **T178** `listings/show.blade.php` — remove Ad ID row from Ad Details right panel

### 17.8 — Image Upload UX
- [x] **T179** `listings/create.blade.php` — add constraint hint "Up to 10 photos · JPEG, PNG, WebP · Max 5 MB each"; add JS counter "X / 10 photos"; disable picker at 10
- [x] **T180** `listings/edit.blade.php` — same as T179

### 17.9 — Footer
- [x] **T181** `partials/_footer.blade.php` — remove "Safety Tips" list item
- [x] **T182** `partials/_footer.blade.php` — replace `href="#"` on "Contact Us" with `mailto:support@lokabuy.com`
- [x] **T183** `partials/_footer.blade.php` — replace `href="#"` on Privacy Policy and Terms of Use with placeholder routes or a tooltip

### 17.10 — Admin: Listing Detail Popup
- [x] **T184** `admin/listings/index.blade.php` — add "View Details" button per row; open modal showing: title, description, price, category, subcategory, location, seller, date, all images in scrollable gallery
- [x] **T185** Detail modal — include Approve, Reject, Delete action buttons wired to existing admin routes

### 17.11 — Admin: Category & Subcategory Management
- [x] **T186** `StoreCategoryRequest` / `UpdateCategoryRequest` — change icon validation to `nullable|file|mimes:svg,png|max:100`
- [x] **T187** `AdminCategoryController` — handle icon file upload in `store()` and `update()` via `ImageService`; store URL
- [x] **T188** `admin/categories/index.blade.php` — replace icon text input with file upload; show current icon preview
- [x] **T189** `admin/categories/index.blade.php` — add subcategory panel per category: list + add / rename / toggle-active controls
- [x] **T190** Add subcategory admin actions to `AdminCategoryController`: `storeSubcategory`, `updateSubcategory`, `toggleSubcategory`
- [x] **T191** Register new subcategory admin routes in `routes/api.php` and `routes/web.php`
- [x] **T192** `Listing` model / `SearchService` — add `whereHas('category', fn($q) => $q->where('is_active', true))` guard to all public-facing queries

---

## Group 28 — Documentation *(Req 1, criteria 7–10)*

- [x] **T193** Rewrite `README.md` — project overview, tech stack, prerequisites, Laragon setup steps, env variables table, seeded data, API quick reference with curl examples, running tests, code style, known limitations
- [x] **T194** Rewrite `docs/architecture.md` — ASCII layer diagram, request lifecycle, directory map with one-line purpose per folder, auth flow (incl. email verification + login rate limiting), listing lifecycle (pending → approved → active → expired/sold), search chain (incl. country filter + category name keyword match), DB transaction boundaries, caching strategy (keys, TTL, bust triggers), Phase 1 vs Phase 2 scope
- [x] **T195** Create `docs/API.md` — base URL, auth header, response envelope format, all endpoints grouped by domain, request body examples, query param reference for search, error code table with example responses
- [x] **T196** Create `docs/FEATURES.md` — Phase 1 features table (feature, description, status), Phase 2 deferred items with rationale, Req 17 QA improvements list
- [ ] **T197** Create `docs/TESTING.md` — test environment setup (`.env.testing`, test DB), how to run (`php artisan test`, coverage), coverage target (80%), scenario table (feature, scenario, expected HTTP status)

---

## Group 29 — Code Quality Audit *(Refactor for modularity and maintainability)*

- [x] **T198** Run `./vendor/bin/pint` — fix all code style violations across the entire codebase
- [x] **T199** Audit all controllers — move any business logic found in controllers into the appropriate Service class
- [x] **T200** Audit all list/search queries — add missing `with()` eager loading on `user`, `category`, `subcategory`, `images` relations to eliminate N+1 queries
- [x] **T201** Audit `ListingService` and `ImageService` — wrap listing create + image upload in `DB::transaction()`; wrap listing update + image changes in `DB::transaction()`
- [x] **T202** Audit `AdminService` — wrap approve/reject + notification dispatch in `DB::transaction()`
- [x] **T203** Audit all PHP files — verify `declare(strict_types=1)` is present as the first statement in every file
- [x] **T204** Audit all application code — replace any `env()` calls with `config()` equivalents; move missing keys into appropriate config files
- [x] **T205** Audit Blade views — extract any repeated HTML blocks (> 3 occurrences) into named partials in `resources/views/partials/`
- [x] **T206** Verify all API controller responses use an API Resource class — remove any raw `->toArray()` or plain array returns

---

## Summary

| Group | Tasks | Scope |
|-------|-------|-------|
| 1 — Project Setup | T01–T10 | Scaffold, config, git, README |
| 2 — Migrations | T11–T19 | All 8 tables |
| 3 — Models | T20–T27 | All 8 Eloquent models |
| 4 — Seeders | T28–T32 | Categories, subcategories, admin user |
| 5 — Layouts & Partials | T33–T40 | 3 layouts, 4 partials, 3 error pages |
| 6 — Exception Handler | T41–T42 | JSON error responses, 404 fallback |
| 7 — Authentication | T43–T50 | Register, login, logout, password reset |
| 8 — Categories | T51–T54 | API + browse page |
| 9 — Listing Core | T55–T65 | CRUD, images, service, policy |
| 10 — Listing Images | T66–T68 | Upload + delete API |
| 11 — Search & Browse | T69–T73 | Filter chain, autocomplete |
| 12 — Listing Detail | T74–T78 | Show page, view counter, related |
| 13 — Favorites | T79–T82 | Toggle + list |
| 14 — Reports | T83–T87 | Store + modal |
| 15 — User Dashboard | T88–T93 | Stats, profile, my listings |
| 16 — Admin Middleware | T94–T96 | EnsureAdmin, EnsureModerator |
| 17 — Admin Listings | T97–T103 | Approval queue, approve, reject |
| 18 — Admin Reports | T104–T106 | Reports queue, action, dismiss |
| 19 — Admin Users & Categories | T107–T112 | Deactivate users, manage categories |
| 20 — Email Notifications | T113–T119 | 4 notification classes + opt-out |
| 21 — Scheduler | T120–T123 | Expire, expiry warning, unfeature |
| 22 — Redis Caching | T124–T125 | Listing + category cache |
| 23 — Homepage | T126–T128 | Hero, featured, recent |
| 24 — OpenAPI Spec | T129 | docs/openapi.yaml |
| 25 — Testing | T130–T144 | Feature + unit tests, 80% coverage |
| 26 — CI/CD | T145 | GitHub Actions workflow |
| 27 — QA Fixes & Improvements | T146–T192 | XSS, password, phone, rate limit, country field, search, admin, footer |
| 28 — Documentation | T193–T197 | README, ARCHITECTURE.md, API.md, FEATURES.md, TESTING.md |
| 29 — Code Quality Audit | T198–T206 | Pint, N+1, transactions, strict types, env(), partials, resources |
| **Total** | **206 tasks** | |
