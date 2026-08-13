# Database Rules

## Engine & ORM
- **MySQL 8** is the only database engine
- **Laravel Eloquent** for all queries — no raw SQL for data mutations
- `DB::select()` is acceptable for read-only reporting queries only, with a comment explaining why
- Never use `DB::statement()` or `DB::unprepared()` for data writes

## Migrations
- One migration file per table or schema change
- Apply in sequence — migration files are the single source of truth for schema
- Never modify an already-run migration — create a new one instead
- Every migration must have a working `down()` method
- Run order: users → categories → subcategories → listings → listing_images → favorites → messages → reports

## Schema — Table Definitions

### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| name | string(100) | |
| email | string | unique |
| phone | string(20) | nullable |
| password | string | bcrypt, cost 12 |
| avatar_url | string | nullable |
| is_verified | boolean | default false |
| is_active | boolean | default true |
| role | enum(user,moderator,admin) | default user |
| created_at / updated_at | timestamps | |

### `categories`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | |
| name | string(100) | |
| slug | string | unique |
| icon | string | nullable |
| sort_order | unsignedInteger | default 0 |
| is_active | boolean | default true |
| created_at / updated_at | timestamps | |

### `subcategories`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | |
| category_id | foreignId | FK → categories |
| name | string(100) | |
| slug | string | unique |
| sort_order | unsignedInteger | default 0 |
| is_active | boolean | default true |
| created_at / updated_at | timestamps | |

### `listings`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | |
| user_id | foreignId | FK → users |
| category_id | foreignId | FK → categories |
| subcategory_id | foreignId | nullable, FK → subcategories |
| title | string(100) | |
| slug | string | unique |
| description | text | |
| price | decimal(12,2) | |
| currency | string(3) | default INR |
| listing_type | enum(buy,sell,rent) | nullable — required for Real Estate only |
| location_city | string(100) | |
| location_state | string(100) | |
| location_lat | decimal(10,7) | nullable — Phase 2 |
| location_lng | decimal(10,7) | nullable — Phase 2 |
| status | enum(active,sold,expired,deleted,inactive) | default inactive |
| approval_status | enum(pending,approved,rejected) | default pending |
| is_featured | boolean | default false |
| featured_until | timestamp | nullable |
| view_count | unsignedInteger | default 0 |
| expires_at | timestamp | default now + 60 days |
| deleted_at | timestamp | nullable — soft delete |
| created_at / updated_at | timestamps | |

**Indexes on `listings`:**
- FULLTEXT on (`title`, `description`)
- Index on (`status`, `approval_status`)
- Index on (`category_id`, `status`)
- Index on (`user_id`)
- Index on (`expires_at`)

### `listing_images`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | |
| listing_id | foreignId | FK → listings, cascade delete |
| url | string | publicly accessible URL |
| order | unsignedTinyInteger | default 0 |
| created_at | timestamp | |

### `favorites`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | |
| user_id | foreignId | FK → users |
| listing_id | foreignId | FK → listings |
| created_at | timestamp | |

**Unique index**: (`user_id`, `listing_id`)

### `messages` *(schema only — no API in Phase 1)*
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | |
| listing_id | foreignId | FK → listings |
| sender_id | foreignId | FK → users |
| receiver_id | foreignId | FK → users |
| body | text | max 1000 chars enforced at app level |
| is_read | boolean | default false |
| created_at | timestamp | |

### `reports`
| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | |
| listing_id | foreignId | FK → listings |
| reporter_id | foreignId | FK → users |
| reason | enum(spam,fraud,prohibited,duplicate,other) | |
| status | enum(pending,actioned,dismissed) | default pending |
| created_at | timestamp | |

### `password_resets` (Laravel default)
email, token, created_at

## Seeders (run order)
1. `CategorySeeder` — 10 top-level categories
2. `SubcategorySeeder` — subcategories per category
3. `AdminUserSeeder` — one admin user from `.env` values
4. `DatabaseSeeder` — calls all of the above in order

## Eloquent Conventions
- Define `$fillable` on every model — never use `$guarded = []`
- Define `$casts` for booleans, enums, decimals, and timestamps
- Soft deletes (`SoftDeletes` trait) on `Listing` model only
- Define relationships in both directions (e.g., `Listing::images()` and `ListingImage::listing()`)
- Scope frequently used query conditions as Eloquent local scopes (e.g., `scopeActive()`, `scopeApproved()`)

## Redis Caching Rules
- **Listing by slug**: cache for 300 seconds on read; bust on update/delete
- **Categories with subcategories**: cache indefinitely; bust only when a category/subcategory changes
- **Rate limit counters**: managed automatically by Laravel throttle middleware
- Cache keys follow the pattern: `listing:{slug}`, `categories:all`
- Always use `Cache::remember()` — never manually `get` then `put`

## Naming Conventions
- Table names: plural snake_case (`listing_images`, not `listingImages`)
- Foreign keys: `{singular_table}_id` (`listing_id`, `user_id`)
- Pivot/junction tables: alphabetical snake_case (`listing_user` if needed)
- Indexes: descriptive (`listings_status_approval_status_index`)
