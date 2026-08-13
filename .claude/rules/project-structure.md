# Project Structure

## Implementation Order
Always follow this sequence when building any feature — never skip steps:

```
Migration → Model → Form Request → Service → Controller → API Resource → Blade View
```

## Directory Map

```
lokabuy/
│
├── app/
│   ├── Exceptions/
│   │   └── Handler.php                  ← Custom exception-to-HTTP mapping
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── V1/
│   │   │   │       ├── Auth/
│   │   │   │       │   ├── AuthController.php
│   │   │   │       │   └── PasswordResetController.php
│   │   │   │       ├── Listing/
│   │   │   │       │   ├── ListingController.php
│   │   │   │       │   └── ListingImageController.php
│   │   │   │       ├── User/
│   │   │   │       │   ├── ProfileController.php
│   │   │   │       │   ├── DashboardController.php
│   │   │   │       │   └── FavoriteController.php
│   │   │   │       └── Admin/
│   │   │   │           ├── AdminDashboardController.php
│   │   │   │           ├── AdminListingController.php
│   │   │   │           ├── AdminReportController.php
│   │   │   │           ├── AdminUserController.php
│   │   │   │           └── AdminCategoryController.php
│   │   │   │
│   │   │   └── Web/                     ← Blade-facing controllers (mirror API)
│   │   │       ├── Auth/
│   │   │       ├── Listing/
│   │   │       ├── User/
│   │   │       └── Admin/
│   │   │
│   │   ├── Middleware/
│   │   │   ├── EnsureAdmin.php          ← role = admin
│   │   │   └── EnsureModerator.php      ← role = admin OR moderator
│   │   │
│   │   ├── Requests/                    ← One FormRequest per action
│   │   │   ├── Auth/
│   │   │   │   ├── RegisterRequest.php
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── ResetPasswordRequest.php
│   │   │   ├── Listing/
│   │   │   │   ├── StoreListingRequest.php
│   │   │   │   ├── UpdateListingRequest.php
│   │   │   │   └── StoreListingImageRequest.php
│   │   │   ├── User/
│   │   │   │   ├── UpdateProfileRequest.php
│   │   │   │   └── UpdateAvatarRequest.php
│   │   │   └── Admin/
│   │   │       ├── RejectListingRequest.php
│   │   │       └── StoreCategoryRequest.php
│   │   │
│   │   └── Resources/                   ← API Resource transformers
│   │       ├── ListingResource.php
│   │       ├── ListingCollection.php
│   │       ├── UserResource.php
│   │       ├── CategoryResource.php
│   │       ├── ReportResource.php
│   │       └── FavoriteResource.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Listing.php                  ← SoftDeletes trait
│   │   ├── Category.php
│   │   ├── Subcategory.php
│   │   ├── ListingImage.php
│   │   ├── Favorite.php
│   │   ├── Message.php                  ← Schema only, Phase 1
│   │   └── Report.php
│   │
│   ├── Notifications/                   ← Laravel Notification classes (email)
│   │   ├── ListingApprovedNotification.php
│   │   ├── ListingRejectedNotification.php
│   │   ├── ListingExpiryWarningNotification.php
│   │   └── ListingRemovedNotification.php
│   │
│   ├── Policies/
│   │   ├── ListingPolicy.php
│   │   └── ReportPolicy.php
│   │
│   └── Services/
│       ├── ListingService.php
│       ├── ImageService.php
│       ├── SearchService.php
│       ├── NotificationService.php
│       └── AdminService.php
│
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   └── ListingFactory.php
│   ├── migrations/                      ← One file per table/change, applied in order
│   └── seeders/
│       ├── DatabaseSeeder.php           ← Calls all seeders in order
│       ├── CategorySeeder.php
│       ├── SubcategorySeeder.php
│       └── AdminUserSeeder.php
│
├── docs/
│   └── openapi.yaml                     ← OpenAPI 3.0 spec
│
├── resources/
│   ├── css/
│   │   └── app.css                      ← Bootstrap 5 overrides only
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php            ← Authenticated user layout
│       │   ├── guest.blade.php          ← Public layout
│       │   └── admin.blade.php          ← Admin panel layout
│       ├── partials/
│       │   ├── _navbar.blade.php
│       │   ├── _footer.blade.php
│       │   ├── _listing-card.blade.php
│       │   ├── _filters.blade.php
│       │   ├── _pagination.blade.php
│       │   └── _alert.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── passwords/
│       │       ├── email.blade.php
│       │       └── reset.blade.php
│       ├── listings/
│       │   ├── index.blade.php          ← Search results / browse
│       │   ├── show.blade.php           ← Listing detail
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── my-listings.blade.php
│       ├── dashboard/
│       │   └── index.blade.php          ← User dashboard
│       ├── profile/
│       │   └── edit.blade.php
│       ├── favorites/
│       │   └── index.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── listings/
│       │   │   └── index.blade.php      ← Approval queue
│       │   ├── reports/
│       │   │   └── index.blade.php
│       │   ├── users/
│       │   │   └── index.blade.php
│       │   └── categories/
│       │       └── index.blade.php
│       └── errors/
│           ├── 403.blade.php
│           ├── 404.blade.php
│           └── 500.blade.php
│
├── routes/
│   ├── api.php                          ← All /api/v1 routes
│   └── web.php                          ← All Blade routes
│
├── tests/
│   ├── Feature/
│   │   └── Api/
│   │       └── V1/
│   │           ├── AuthTest.php
│   │           ├── ListingTest.php
│   │           ├── SearchTest.php
│   │           ├── FavoriteTest.php
│   │           ├── ReportTest.php
│   │           └── AdminTest.php
│   └── Unit/
│       └── Services/
│           ├── ListingServiceTest.php
│           ├── SearchServiceTest.php
│           └── ImageServiceTest.php
│
├── .claude/
│   └── rules/
│       ├── tech-stack.md
│       ├── coding-standards.md
│       ├── api-rules.md
│       ├── database-rules.md
│       ├── error-handling.md
│       └── project-structure.md        ← this file
│
├── .github/
│   └── workflows/
│       └── ci.yml                       ← Lint + test on PRs to develop and main
│
├── CLAUDE.md                            ← Project overview + phase scope + rule references
├── README.md
├── pint.json                            ← Laravel Pint code style config
├── .env.example
└── .gitignore
```

## Routing Conventions

### `routes/api.php`
- Group all routes under `prefix('v1')` and `name('api.v1.')`
- Auth routes: no middleware
- Authenticated routes: `auth:sanctum` middleware
- Admin routes: `auth:sanctum` + `EnsureAdmin` middleware
- Moderator routes: `auth:sanctum` + `EnsureModerator` middleware

### `routes/web.php`
- Public routes: no middleware
- Authenticated web routes: `auth` middleware
- Admin web routes: `auth` + `EnsureAdmin` middleware

## File Naming Rules
- Controllers: `PascalCase` + `Controller.php`
- Models: `PascalCase` singular — `Listing.php`, not `Listings.php`
- Blade views: `kebab-case.blade.php`
- Partials: `_kebab-case.blade.php` (leading underscore)
- Migrations: Laravel default timestamp prefix — `2024_01_01_000001_create_listings_table.php`
