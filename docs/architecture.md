# Architecture Overview

## 1. Layer Diagram

```
Browser / API Client
│
├── routes/web.php ──► Web Controllers ──► Blade Views
│
└── routes/api.php ──► API Controllers ──► API Resources (JSON)
          │
          │  (both stacks share everything below)
          │
     Middleware
     throttle · CORS · auth · role check
          │
     Form Requests
     validate · request-level authorize
          │
     Controllers  (thin — receive, delegate, respond)
          │
     Services     (all business logic)
          │
     ┌────┴────┐
     │         │
  Eloquent   Redis
  Models     cache · queue
     │
  MySQL 8
```

---

## 2. Directory Map

```
app/
  Exceptions/Handler.php          maps every exception to the correct HTTP status + envelope
  Http/
    Controllers/Api/V1/           JSON-only controllers; one class per domain area
    Controllers/Web/              Blade-facing controllers; mirror API structure
    Middleware/
      EnsureAdmin.php             role = admin only
      EnsureModerator.php         role = admin OR moderator
    Requests/                     one FormRequest per action; all validation lives here
    Resources/                    transform Eloquent models into JSON envelope output
  Models/                         Eloquent models with fillable, casts, scopes, relationships
  Notifications/                  queued email notification classes (ShouldQueue)
  Policies/                       resource-level authorization; admin bypasses via before()
  Services/
    ListingService.php            create · update · delete · sell · renew · expire
    ImageService.php              validate · store · delete images and avatars
    SearchService.php             composable query chain, FULLTEXT, paginate
    AdminService.php              approve · reject · feature · deactivate · reports
    NotificationService.php       wraps Notification::send() with try/catch + logging

database/
  migrations/                     one file per table or schema change; never modified after run
  seeders/                        CategorySeeder → SubcategorySeeder → AdminUserSeeder
  factories/                      UserFactory, ListingFactory for testing

resources/views/
  layouts/                        guest.blade.php · app.blade.php · admin.blade.php
  partials/                       reusable snippets prefixed with underscore (_listing-card, etc.)
  listings/                       index · show · create · edit · my-listings
  admin/                          dashboard · listings · reports · users · categories
  auth/                           login · register · password reset · verify-email
  errors/                         403 · 404 · 500

routes/
  api.php                         /api/v1 routes; grouped by middleware; throttle applied
  web.php                         Blade routes; verification + role middleware groups

docs/
  openapi.yaml                    OpenAPI 3.0 spec for all /api/v1 endpoints
  architecture.md                 this file
  task-list.md                    implementation task tracker

.claude/rules/                    project rules loaded by Claude Code at session start
```

---

## 3. Request Lifecycle

### API Request
1. `routes/api.php` matches route → applies `throttle:100,1` (unauth) or `throttle:300,1` (auth)
2. `auth:sanctum` middleware validates Bearer token → 401 on invalid/missing
3. Role middleware (`EnsureAdmin` / `EnsureModerator`) → 403 on role mismatch
4. Form Request validates input → 422 with field errors on failure
5. Controller calls Service method
6. Service interacts with Eloquent and Redis cache
7. Controller wraps result in an API Resource
8. Resource returns standard envelope: `{ success, data, error }`

### Web (Blade) Request
1. `routes/web.php` matches route
2. `auth` middleware checks session → redirect to login on failure
3. `verified` middleware (listing create/store routes) → redirect to verify-email notice
4. Form Request validates input → redirect back with errors on failure
5. Controller calls the same Service methods as the API
6. Controller returns Blade view

> Web and API controllers call the same Service layer — no duplicated business logic.

---

## 4. Auth Flow

### Registration
1. `RegisterRequest` validates: name (strip_tags), unique email, phone regex, password strength (`min:10, mixedCase, numbers, symbols`)
2. `User` created with bcrypt cost-12 password; `is_active = true`, `is_verified = false`
3. `sendEmailVerificationNotification()` dispatched — queued email with signed verification link
4. Web: redirect to `verification.notice`; API: return `{ email_verified: false, token }`

### Email Verification
1. User clicks signed link → `GET /email/verify/{id}/{hash}` (web) with `signed` middleware
2. `$request->fulfill()` sets `email_verified_at`, fires `Verified` event
3. `verified` middleware on listing create routes blocks unverified users → redirect to notice

### Login & Rate Limiting
1. `LoginRequest` validates credentials
2. Rate limiter checked: key = `login:{email|ip}`, max 3 attempts, 900s (15 min) decay
3. Deactivated account → 403; wrong password → counter incremented, 401 returned
4. On lockout (≥ 3 failures): 429 with `retry_after` seconds
5. On success: counter cleared; web uses `Auth::attempt($credentials, $remember)` for session; API creates Sanctum token (no expiry — revoked on logout only)

### Logout
1. `currentAccessToken()->delete()` removes token from `personal_access_tokens`
2. Web: session invalidated + CSRF token regenerated

### Password Reset
1. Reset link emailed (signed, 60-minute TTL) via `password_resets` table
2. On reset: password updated, all existing Sanctum tokens revoked

---

## 5. Listing Lifecycle

```
User creates listing
        │
        ▼
approval_status = pending
status          = inactive
        │
   ┌────┴────┐
   │         │
Admin      Admin
approves  rejects
   │         │
   ▼         ▼
approved   rejected
active     inactive  ◄── stays here, email sent with reason
   │
   ├──────────────────────────────────────┐
   │                                      │
User edits                          Scheduler (daily)
   │                                      │
   ▼                                      ▼
approval_status = pending           expires_at < now()
status          = inactive               │
(re-approval required)                   ▼
                                    status = expired
                                         │
                                    User renews
                                         │
                                         ▼
                                    status = active
                                    expires_at = now() + 60 days

User marks sold → status = sold
User deletes    → soft delete (deleted_at set)
```

**`scopeVisible()`** — the public query scope applied to all browse/search queries:
- `status = active` AND `approval_status = approved` AND `category.is_active = true`

---

## 6. Search Chain

`SearchService::search()` builds a single query with composable AND-chained filters:

```
1. Base scope      Listing::visible() → active + approved + active category
2. Keyword         MATCH(title, description) AGAINST(? IN BOOLEAN MODE)   [≥3 chars]
                   OR category.name LIKE ?                                  [orWhereHas]
                   OR subcategory.name LIKE ?                               [orWhereHas]
                   Fallback: title LIKE ?                                   [<3 chars]
3. Category        whereHas('category', slug = ?)
4. Subcategory     whereHas('subcategory', slug = ?)
5. City            location_city LIKE ?
6. State           location_state LIKE ?
7. Country         location_country LIKE ?
8. Price min       price >= ?
9. Price max       price <= ?
10. Listing type   listing_type = ?
11. Sort           ORDER BY is_featured DESC, then: created_at / price / oldest
```

Paginated at 20 results per page. `withQueryString()` preserves all filter params across pages.

**FULLTEXT index** on `listings(title, description)` — created in migration.

**Autocomplete** — separate lightweight endpoint; `title LIKE ?` ordered by `view_count DESC`; limited to 10 results.

---

## 7. Service Layer

### `ListingService`
- `create()` — generates unique slug, sets defaults, dispatches approval pending notification
- `update()` — resets to `pending/inactive`, busts cache
- `delete()` — soft delete, busts cache
- `markAsSold()` — sets `status = sold`
- `renew()` — resets `expires_at`, sets `status = active`
- `expireListings()` — called by scheduler; bulk updates where `expires_at < now()`
- `incrementViewCount()` — `DB::statement` increment (avoids Eloquent overhead on every view)

### `ImageService`
- `store()` — validates MIME (jpeg/png/webp) and size (≤ 5 MB); saves to `listings/{id}/`; creates `ListingImage` record
- `storeAvatar()` — same flow, 2 MB limit, saved to `avatars/{userId}/`
- `delete()` / `deleteAll()` — removes file from storage + DB record
- `canAddImages()` — enforces 10-image cap per listing

### `SearchService`
- Composable private `apply*()` methods — each is a no-op when its parameter is absent
- `autocomplete()` — returns `[{title, slug}]` array for the search dropdown

### `AdminService`
- `approve()` — sets `approved/active`, dispatches `ListingApprovedNotification`
- `reject()` — sets `rejected/inactive`, stores reason, dispatches `ListingRejectedNotification`
- `actionReport()` — soft-deletes listing, resolves report, dispatches `ListingRemovedNotification`
- `dismissReport()` — sets report `status = dismissed`
- `deactivate()` / `activate()` — toggles `is_active` on user
- `setFeatured()` — toggles `is_featured`, sets/clears `featured_until`

### `NotificationService`
- Single `send(Notifiable $user, Notification $notification)` method
- Wrapped in try/catch — notification failure never breaks the primary request
- All notification classes implement `ShouldQueue` (Redis queue, max 3 retries)

---

## 8. Image Upload Flow

```
POST /listings/{listing}/images
        │
StoreListingImageRequest
  validates: mimes(jpeg,png,webp), max 5120 KB
        │
ListingImageController
  → ImageService::canAddImages()   abort 422 if already at 10
  → ImageService::store()
        │
  $file->store("listings/{id}", 'public')
  Storage::disk('public')->url($path)  → public URL
        │
  ListingImage::create(['url' => ..., 'order' => ...])
        │
  ListingImageResource returned in envelope
```

Avatar upload: same flow via `POST /profile/avatar`, 2 MB limit, path `avatars/{userId}/`.

Category icon upload: `POST /admin/categories` or `PUT /admin/categories/{id}`, SVG/PNG only, 100 KB limit, path `icons/`.

---

## 9. DB Transaction Boundaries

| Operation | Transaction scope |
|-----------|-----------------|
| Listing create + image upload | `DB::transaction()` in `ListingService::create()` |
| Listing update + image changes | `DB::transaction()` in `ListingService::update()` |
| Admin approve/reject + notification dispatch | `DB::transaction()` in `AdminService::approve()` / `reject()` |
| Report action (delete listing + resolve report) | `DB::transaction()` in `AdminService::actionReport()` |

Notification dispatch is called **after** the transaction commits to avoid holding the lock while the queue job is enqueued.

---

## 10. Caching Strategy

| Data | Cache key | TTL | Invalidated when |
|------|-----------|-----|-----------------|
| Single listing (API) | `listing:{slug}` | 300 s | Updated, deleted, approved, rejected |
| Categories + subcategories | `categories:all` | Forever | Any category/subcategory write |
| Categories (web index/create) | `categories.all.web` | Forever | Any category/subcategory write |

Rules:
- Always use `Cache::remember()` — never manual `get` then `put`
- Bust via `Cache::forget(key)` in the Service after every write
- Rate-limit counters managed automatically by Laravel's `throttle` middleware (Redis)

---

## 11. Email Notification Flow

All emails are queued — never sent synchronously.

| Trigger | Notification class | Recipient |
|---------|--------------------|-----------|
| User registers | `MustVerifyEmail::sendEmailVerificationNotification()` | New user |
| Listing approved | `ListingApprovedNotification` | Listing owner |
| Listing rejected | `ListingRejectedNotification` | Listing owner |
| Listing expires in 7 days | `ListingExpiryWarningNotification` | Listing owner |
| Listing removed by moderator | `ListingRemovedNotification` | Listing owner |

- Dispatched from `NotificationService` (called by `AdminService` or scheduler)
- Queue driver: Redis; max retries: 3
- Notification failure is caught and logged — never surfaces to the user

---

## 12. Admin Authorization

### Roles
| Role | Access |
|------|--------|
| `user` | Own listings, profile, favorites |
| `moderator` | Reports queue + listing approval |
| `admin` | Full access — all of the above + users + categories |

### Flow
```
Request → auth:sanctum → EnsureAdmin / EnsureModerator → Controller → Policy (if applicable)
```

- `ListingPolicy::before()` returns `true` for admin role — bypasses all ownership checks
- `ReportPolicy` prevents users from reporting their own listing
- Non-admin ownership checks live in policies, not controllers

---

## 13. Database Relationships

| Model | Key Relationships |
|-------|------------------|
| `User` | hasMany Listings, hasMany Favorites, hasMany Reports (as reporter) |
| `Listing` | belongsTo User, belongsTo Category, belongsTo Subcategory, hasMany ListingImages, hasMany Favorites, hasMany Reports |
| `Category` | hasMany Subcategories, hasMany Listings |
| `Subcategory` | belongsTo Category, hasMany Listings |
| `ListingImage` | belongsTo Listing |
| `Favorite` | belongsTo User, belongsTo Listing |
| `Message` | belongsTo Listing, belongsTo User×2 (sender, receiver) — schema only |
| `Report` | belongsTo Listing, belongsTo User (reporter) |

**Soft deletes**: `Listing` model only (`deleted_at` column). All other models use hard delete.

---

## 14. Phase 1 vs Phase 2 Scope

### Phase 1 — Implemented
- User registration, email verification, login with rate limiting, password reset
- Listing CRUD with image upload (up to 10 images per listing)
- Full-text + multi-filter search with pagination (keyword, category, subcategory, city, state, country, price, listing type)
- Favorites (toggle + list)
- Reports (submit + admin queue)
- Admin: listing approval/rejection/feature, report action/dismiss, user deactivation, category + subcategory management
- Email notifications (queued): verification, approved, rejected, expiry warning, removed
- Scheduler: listing expiry, expiry warning, unfeature
- Redis caching: listings by slug (300s), categories (forever)
- REST API under `/api/v1` with standard JSON envelope + OpenAPI spec

### Phase 2 — Deferred (not implemented)
| Feature | Notes |
|---------|-------|
| In-app messaging | `messages` table schema exists; no API or UI |
| Map / geolocation search | `location_lat` / `location_lng` columns exist but unused |
| In-app notification dropdown | No `notifications` table |
| Payment gateway (Razorpay) | Not started |
| SMS / OTP phone verification | Phone field exists; no OTP flow |
| Social login (Google OAuth) | Not implemented |
| Dark mode | Not implemented |
| Full responsive / Lighthouse audit | Basic Bootstrap 5 responsiveness only |
