# API Reference

## Base URL

```
http://localhost:8000/api/v1
```

All endpoints are prefixed with `/api/v1`.

---

## Authentication

Protected endpoints require a Bearer token obtained from `POST /auth/login` or `POST /auth/register`.

```
Authorization: Bearer <token>
```

Tokens have no expiry and are revoked on logout or password reset.

---

## Response Envelope

Every response — success or error — uses the same shape:

```json
{
  "success": true,
  "data": {},
  "error": null
}
```

| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | `true` for 2xx responses, `false` for all errors |
| `data` | object \| array \| null | Response payload |
| `error` | string \| null | Human-readable error message, `null` on success |

---

## Error Codes

| HTTP | `error` value | When |
|------|--------------|------|
| 400 | `"Bad request"` | Malformed request (e.g. self-message) |
| 401 | `"Unauthorized"` | Missing, invalid, or expired token |
| 403 | `"Forbidden"` | Authenticated but not permitted |
| 404 | `"Not found"` | Resource or route not found |
| 409 | `"Email already registered"` | Duplicate registration |
| 422 | `"Validation failed"` | Input validation errors |
| 429 | `"Too many requests"` | Rate limit exceeded |
| 500 | `"Internal server error"` | Unhandled server error |

### Validation error example (422)

```json
{
  "success": false,
  "data": {
    "errors": {
      "email": ["The email field is required."],
      "password": ["The password must be at least 10 characters."]
    }
  },
  "error": "Validation failed"
}
```

### Rate limit error example (429)

```json
{
  "success": false,
  "data": { "retry_after": 847 },
  "error": "Too many login attempts. Please try again in 847 seconds."
}
```

---

## Rate Limits

| Route type | Limit |
|-----------|-------|
| Unauthenticated | 100 requests / minute / IP |
| Authenticated | 300 requests / minute / user |

---

## 1. Authentication

### POST `/auth/register`

Register a new user account. A verification email is sent on success.

**Request body**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+91 98765 43210",
  "password": "Secret@1234",
  "password_confirmation": "Secret@1234"
}
```

| Field | Required | Rules |
|-------|----------|-------|
| `name` | Yes | string, max 100 |
| `email` | Yes | valid email, unique |
| `phone` | No | E.164-style, 7–15 digits |
| `password` | Yes | min 10 chars, mixed case, number, symbol |
| `password_confirmation` | Yes | must match `password` |

**Response (201)**

```json
{
  "success": true,
  "data": {
    "user": { "id": 5, "name": "John Doe", "email": "john@example.com", "email_verified": false },
    "token": "5|abc123..."
  },
  "error": null
}
```

---

### POST `/auth/login`

**Request body**

```json
{
  "email": "john@example.com",
  "password": "Secret@1234",
  "remember": false
}
```

**Response (200)**

```json
{
  "success": true,
  "data": {
    "user": { "id": 5, "name": "John Doe", "email": "john@example.com" },
    "token": "6|xyz789..."
  },
  "error": null
}
```

Locked out after 3 failed attempts for 15 minutes — returns 429 with `retry_after` seconds.

---

### POST `/auth/logout` — *Auth required*

No request body. Revokes the current token.

**Response (200)**

```json
{ "success": true, "data": null, "error": null }
```

---

### POST `/auth/refresh`

Exchange a valid token for a new one.

**Request body**

```json
{ "token": "6|xyz789..." }
```

**Response (200)** — returns new token, same shape as login.

---

### POST `/auth/forgot-password`

Send a password reset link to the given email.

**Request body**

```json
{ "email": "john@example.com" }
```

**Response (200)** — always succeeds to prevent email enumeration.

---

### POST `/auth/reset-password`

**Request body**

```json
{
  "token": "<reset-token-from-email>",
  "email": "john@example.com",
  "password": "NewSecret@5678",
  "password_confirmation": "NewSecret@5678"
}
```

**Response (200)** — revokes all existing tokens on success.

---

## 2. Categories

### GET `/categories`

Returns all active categories with their subcategories.

**Response (200)**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "icon": "bi bi-phone",
      "subcategories": [
        { "id": 4, "name": "Mobile Phones", "slug": "mobile-phones" }
      ]
    }
  ],
  "error": null
}
```

### GET `/categories/{slug}`

Returns a single category with its subcategories.

---

## 3. Listings

### GET `/listings`

Browse and search listings. All query parameters are optional and combinable.

**Query parameters**

| Parameter | Type | Description |
|-----------|------|-------------|
| `q` | string | Keyword — searches title, description, category name, subcategory name |
| `category` | string | Category slug (e.g. `electronics`) |
| `subcategory` | string | Subcategory slug (e.g. `mobile-phones`) |
| `city` | string | Partial match on `location_city` |
| `state` | string | Partial match on `location_state` |
| `country` | string | Partial match on `location_country` |
| `price_min` | number | Minimum price (inclusive) |
| `price_max` | number | Maximum price (inclusive) |
| `listing_type` | string | `buy`, `sell`, or `rent` |
| `sort` | string | `newest` (default), `oldest`, `price_asc`, `price_desc` |
| `page` | integer | Page number (20 results per page) |

**Example**

```
GET /api/v1/listings?q=iphone&category=electronics&city=Mumbai&price_max=80000&sort=price_asc
```

**Response (200)** — paginated

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 42,
        "title": "iPhone 14 Pro 256GB",
        "slug": "iphone-14-pro-256gb-a1b2c",
        "price": 75000.00,
        "currency": "INR",
        "location_city": "Mumbai",
        "location_state": "Maharashtra",
        "location_country": "India",
        "is_featured": false,
        "view_count": 128,
        "created_at": "2025-04-01T10:30:00+00:00",
        "category": { "id": 3, "name": "Electronics", "slug": "electronics" },
        "images": [{ "id": 11, "url": "http://localhost:8000/storage/listings/42/photo.jpg" }]
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 54
  },
  "error": null
}
```

---

### GET `/listings/autocomplete?q=iph`

Returns up to 10 title suggestions for the search bar.

**Response (200)**

```json
{
  "success": true,
  "data": [
    { "title": "iPhone 14 Pro 256GB", "slug": "iphone-14-pro-256gb-a1b2c" },
    { "title": "iPhone 13 Mini", "slug": "iphone-13-mini-d3e4f" }
  ],
  "error": null
}
```

---

### GET `/listings/{slug}`

Returns a single listing with full details. Increments `view_count`.

**Response (200)** — full `ListingResource` including `user`, `category`, `subcategory`, `images`.

---

### POST `/listings` — *Auth + verified email required*

**Request body** (`multipart/form-data` if uploading images, otherwise `application/json`)

```json
{
  "category_id": 3,
  "subcategory_id": 7,
  "title": "iPhone 14 Pro 256GB",
  "description": "Excellent condition, barely used. Comes with original box and accessories.",
  "price": 75000,
  "listing_type": null,
  "location_city": "Mumbai",
  "location_state": "Maharashtra",
  "location_country": "India"
}
```

| Field | Required | Rules |
|-------|----------|-------|
| `category_id` | Yes | must exist in categories |
| `subcategory_id` | No | must belong to the selected category |
| `title` | Yes | string, 3–100 chars |
| `description` | Yes | string, 20–4000 chars |
| `price` | Yes | number ≥ 0 |
| `listing_type` | Conditional | required (`buy`/`rent`) for Real Estate category |
| `location_city` | Yes | string, max 100 |
| `location_state` | Yes | string, max 100 |
| `location_country` | Yes | string, max 100 |
| `images[]` | No | up to 10 files, JPEG/PNG/WebP, max 5 MB each |

**Response (201)** — full `ListingResource`.

---

### PUT `/listings/{slug}` — *Auth required (owner only)*

Same fields as POST. Resets listing to `pending/inactive` for re-approval.

---

### DELETE `/listings/{slug}` — *Auth required (owner or admin)*

Soft-deletes the listing.

**Response (204)** — no body.

---

### PATCH `/listings/{slug}/sold` — *Auth required (owner only)*

Marks the listing as sold.

**Response (200)** — updated `ListingResource`.

---

### PATCH `/listings/{slug}/renew` — *Auth required (owner only)*

Resets `expires_at` to 60 days from now and sets `status = active`.

**Response (200)** — updated `ListingResource`.

---

## 4. Listing Images

### POST `/listings/{listing}/images` — *Auth required (owner only)*

Upload additional images to an existing listing (multipart/form-data).

| Field | Rules |
|-------|-------|
| `images[]` | JPEG/PNG/WebP, max 5 MB each, total cap 10 per listing |

**Response (201)** — array of new `ListingImageResource` objects.

---

### DELETE `/listings/{listing}/images/{image}` — *Auth required (owner only)*

Deletes a single image file and its database record.

**Response (204)** — no body.

---

## 5. Favorites

### GET `/favorites` — *Auth required*

Returns a paginated list of the authenticated user's favorited listings.

**Response (200)** — paginated `ListingResource` collection.

---

### POST `/favorites/{listing}` — *Auth required*

Toggles the favorite state for the given listing slug.

**Response (200)**

```json
{
  "success": true,
  "data": { "favorited": true },
  "error": null
}
```

---

## 6. Reports

### POST `/listings/{listing}/report` — *Auth required*

Report a listing. Cannot report your own listing.

**Request body**

```json
{ "reason": "fraud" }
```

| `reason` values | |
|----------------|--|
| `spam` | Spam |
| `fraud` | Fraud / Scam |
| `prohibited` | Prohibited item |
| `duplicate` | Duplicate listing |
| `other` | Other |

**Response (200)**

```json
{ "success": true, "data": null, "error": null }
```

---

## 7. Profile & Dashboard

### GET `/profile` — *Auth required*

Returns the authenticated user's profile.

---

### PUT `/profile` — *Auth required*

**Request body**

```json
{
  "name": "John Doe",
  "phone": "+91 98765 43210"
}
```

---

### POST `/profile/avatar` — *Auth required*

Upload a profile photo (multipart/form-data).

| Field | Rules |
|-------|-------|
| `avatar` | JPEG/PNG/WebP, max 2 MB |

---

### GET `/my/dashboard` — *Auth required*

Returns dashboard statistics.

**Response (200)**

```json
{
  "success": true,
  "data": {
    "total_listings": 12,
    "active_listings": 8,
    "total_views": 430,
    "favorites_count": 5
  },
  "error": null
}
```

---

### GET `/my/listings` — *Auth required*

Returns all listings belonging to the authenticated user (paginated, all statuses).

---

## 8. Admin — Listings

All admin endpoints require `role = admin`.

### GET `/admin/dashboard`

Returns platform-wide statistics.

---

### GET `/admin/listings/pending`

Returns paginated list of listings with `approval_status = pending`.

---

### PATCH `/admin/listings/{id}/approve`

Approves a listing — sets `approval_status = approved`, `status = active`. Sends email to owner.

**Response (200)** — updated `ListingResource`.

---

### PATCH `/admin/listings/{id}/reject`

**Request body**

```json
{ "reason": "This listing violates our prohibited items policy." }
```

Sets `approval_status = rejected`. Sends rejection email with reason to owner.

---

### PATCH `/admin/listings/{id}/feature`

**Request body**

```json
{ "is_featured": true }
```

Toggles featured status. Sets or clears `featured_until`.

---

## 9. Admin — Reports

Requires `role = admin` or `role = moderator`.

### GET `/admin/reports`

Returns paginated pending reports with listing and reporter details.

---

### PATCH `/admin/reports/{id}/action`

Removes the reported listing (soft delete) and marks the report as actioned.

---

### PATCH `/admin/reports/{id}/dismiss`

Marks the report as dismissed without removing the listing.

---

## 10. Admin — Users

### GET `/admin/users`

Returns all users (paginated).

---

### PATCH `/admin/users/{id}/deactivate`

Sets `is_active = false` on the user and revokes all their tokens.

---

## 11. Admin — Categories

### GET `/admin/categories`

Returns all categories with subcategory counts.

---

### POST `/admin/categories`

**Request body** (`multipart/form-data`)

| Field | Required | Rules |
|-------|----------|-------|
| `name` | Yes | string, max 100 |
| `slug` | No | auto-generated from name if omitted |
| `icon` | No | SVG or PNG file, max 100 KB |
| `sort_order` | No | integer ≥ 0 |

---

### PUT `/admin/categories/{id}`

Same fields as POST — all optional (partial update).
