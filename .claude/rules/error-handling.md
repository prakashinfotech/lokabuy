# Error Handling

## API Error Response Format
All errors follow the standard envelope — never break from this shape:

```json
{
  "success": false,
  "data": null,
  "error": "Human-readable message"
}
```

Validation errors include field details in `data`:
```json
{
  "success": false,
  "data": {
    "errors": {
      "title": ["The title field is required."],
      "price": ["The price must be a positive number."]
    }
  },
  "error": "Validation failed"
}
```

## HTTP Status Code Map

| Scenario | Code | `error` message |
|----------|------|----------------|
| Success | 200/201/204 | null |
| Validation failed | 422 | "Validation failed" |
| Unauthenticated / token invalid or expired | 401 | "Unauthorized" |
| Authenticated but not permitted | 403 | "Forbidden" |
| Resource not found (model) | 404 | "Not found" |
| Route not found | 404 | "Route not found" |
| Duplicate / conflict (e.g. email exists) | 409 | "Email already registered" |
| Self-message attempt | 400 | "Cannot send a message to yourself" |
| Internal server error | 500 | "Internal server error" |

## Exception Handler (`app/Exceptions/Handler.php`)
Override `render()` to catch and format all exceptions for API routes:

- `AuthenticationException` → 401
- `AuthorizationException` → 403
- `ModelNotFoundException` → 404
- `ValidationException` → 422 with field errors
- `RouteNotFoundException` / `NotFoundHttpException` → 404
- All other `Throwable` → 500 + log full stack trace

Always check `$request->expectsJson()` before returning JSON vs. Blade error view.

## Logging
- Use Laravel's `Log` facade — never `error_log()` or `echo`
- Log level guide:
  - `Log::error()` — unhandled exceptions, 500 errors (always with stack trace)
  - `Log::warning()` — expected but notable failures (e.g., repeated failed login attempts)
  - `Log::info()` — significant business events (e.g., listing approved, user deactivated)
  - `Log::debug()` — development-only, must not be left in committed code
- Never log passwords, tokens, or PII (email/phone) — log user ID only
- Log channel: `daily` (configured in `config/logging.php`)

## Validation Failures
- Return HTTP 422 for all Form Request validation failures
- Laravel automatically handles this when using Form Request classes
- Field errors returned in `data.errors` keyed by field name
- Never expose raw PHP exception messages to the client

## Authorization Failures
- Use Laravel Policies for all authorization
- Policy `before()` hook grants full access to `admin` role
- `deny()` in a policy returns 403 via `AuthorizationException`
- Never check roles inline in controllers

## Model Not Found
- Route model binding automatically throws `ModelNotFoundException` → returns 404
- Never return `null` from a controller when a resource is expected — let the exception propagate

## Image Upload Errors
- File too large (> 5 MB for listings, > 2 MB for avatars) → 422: "Invalid image: must be JPEG, PNG, or WebP and under 5 MB"
- Wrong MIME type → 422: same message above
- Storage failure → 500 + log the error

## Queue / Notification Failures
- Email notifications dispatched via Laravel queues — failures logged and retried (max 3 attempts)
- Never let a notification failure break the primary request/response cycle
- Wrap `Notification::send()` calls in a try/catch in the service layer; log on catch

## Web (Blade) Error Pages
- `resources/views/errors/404.blade.php` — extends `layouts.guest`
- `resources/views/errors/403.blade.php` — extends `layouts.guest`
- `resources/views/errors/500.blade.php` — extends `layouts.guest`
- Never show stack traces or debug output to end users in production
