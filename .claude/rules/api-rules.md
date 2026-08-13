# API Rules

## Base Path
All API endpoints are under `/api/v1`.

## Response Envelope
Every API response — success or failure — uses this exact envelope:

```json
{
  "success": true,
  "data": {},
  "error": null
}
```

- `success`: boolean — `true` for 2xx, `false` for all errors
- `data`: the response payload (object, array, or `null`)
- `error`: human-readable error string, or `null` on success
- Validation errors (HTTP 422) put field errors inside `data.errors`:

```json
{
  "success": false,
  "data": {
    "errors": {
      "title": ["The title field is required."],
      "price": ["The price must be a number."]
    }
  },
  "error": "Validation failed"
}
```

## HTTP Status Codes

| Situation | Code |
|-----------|------|
| Success (read) | 200 |
| Created | 201 |
| No content (delete) | 204 |
| Validation failed | 422 |
| Unauthenticated / token invalid | 401 |
| Authorized but forbidden | 403 |
| Resource not found | 404 |
| Route not found | 404 |
| Conflict (e.g. duplicate email) | 409 |
| Server error | 500 |

## Authentication
- Laravel Sanctum — Bearer token in `Authorization` header
- Token lifetime: 24 hours (JWT access token), 30 days (refresh token)
- Tokens invalidated on logout and on password change
- Protected routes use `auth:sanctum` middleware
- `WHEN a JWT token is expired or invalid → HTTP 401`

## Rate Limiting
- Unauthenticated routes: **100 requests / minute / IP** (`throttle:100,1`)
- Authenticated routes: **300 requests / minute / user** (`throttle:300,1`)
- Applied via Laravel's built-in throttle middleware in `routes/api.php`

## CORS
- Allowed origins from `CORS_ALLOWED_ORIGINS` env variable
- Configured in `config/cors.php` — never hardcoded in code

## Validation
- Every endpoint with user input has a dedicated **Form Request** class in `app/Http/Requests/`
- Never call `$request->validate()` inside a controller
- Form Requests handle both validation rules and authorization

## API Resources
- Every JSON response uses an **API Resource** class from `app/Http/Resources/`
- Never return `$model->toArray()` or raw arrays from controllers
- Resource collections use `ResourceClass::collection($paginator)`
- Paginated responses include Laravel's default pagination meta

## Endpoint Conventions
- Use **slugs** as route parameters for public listing endpoints: `/api/v1/listings/{slug}`
- Use **IDs** for admin endpoints: `/api/v1/admin/listings/{listing}`
- Use route model binding — define `getRouteKeyName()` on models where needed
- Prefer `PATCH` for partial updates (status changes), `PUT` for full resource updates
- Listing images uploaded via separate endpoint: `POST /api/v1/listings/{listing}/images`

## Stateless
- The API is fully stateless — no session state on API routes
- Web (Blade) routes use session-based auth; API routes use Sanctum tokens

## OpenAPI
- An OpenAPI 3.0 specification file lives at `docs/openapi.yaml`
- Every new endpoint must be documented in `openapi.yaml` before the task is considered done

## Endpoint Table (Phase 1)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/register` | No | Register |
| POST | `/auth/login` | No | Login → token |
| POST | `/auth/logout` | Yes | Invalidate token |
| POST | `/auth/refresh` | No | Refresh token |
| POST | `/auth/forgot-password` | No | Send reset email |
| POST | `/auth/reset-password` | No | Reset password |
| GET | `/profile` | Yes | Get my profile |
| PUT | `/profile` | Yes | Update profile |
| POST | `/profile/avatar` | Yes | Upload avatar |
| GET | `/listings` | No | List / search |
| POST | `/listings` | Yes | Create listing |
| GET | `/listings/{slug}` | No | Listing detail |
| PUT | `/listings/{slug}` | Yes | Update listing |
| DELETE | `/listings/{slug}` | Yes | Delete listing |
| PATCH | `/listings/{slug}/sold` | Yes | Mark as sold |
| PATCH | `/listings/{slug}/renew` | Yes | Renew listing |
| POST | `/listings/{listing}/images` | Yes | Upload images |
| DELETE | `/listings/{listing}/images/{image}` | Yes | Delete image |
| POST | `/listings/{listing}/report` | Yes | Report listing |
| GET | `/my/listings` | Yes | My listings |
| GET | `/my/dashboard` | Yes | Dashboard stats |
| GET | `/favorites` | Yes | My favorites |
| POST | `/favorites/{listing}` | Yes | Toggle favorite |
| GET | `/categories` | No | All categories |
| GET | `/categories/{slug}/listings` | No | By category |
| GET | `/subcategories/{slug}/listings` | No | By subcategory |
| GET | `/admin/dashboard` | Admin | Admin stats |
| GET | `/admin/listings/pending` | Admin | Approval queue |
| PATCH | `/admin/listings/{listing}/approve` | Admin | Approve |
| PATCH | `/admin/listings/{listing}/reject` | Admin | Reject |
| PATCH | `/admin/listings/{listing}/feature` | Admin | Set featured |
| GET | `/admin/reports` | Moderator | Reports queue |
| PATCH | `/admin/reports/{report}/action` | Moderator | Remove + resolve |
| PATCH | `/admin/reports/{report}/dismiss` | Moderator | Dismiss |
| GET | `/admin/users` | Admin | All users |
| PATCH | `/admin/users/{user}/deactivate` | Admin | Deactivate user |
| GET | `/admin/categories` | Admin | List categories |
| POST | `/admin/categories` | Admin | Create category |
| PUT | `/admin/categories/{category}` | Admin | Update category |
