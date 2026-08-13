# Features

## Phase 1 — Implemented

| # | Feature | Description | Status |
|---|---------|-------------|--------|
| 1 | User Registration | Email + password signup with name and optional phone. Password requires min 10 chars, mixed case, number, and symbol. | ✅ Done |
| 2 | Email Verification | Signed verification link sent on registration. Listing creation blocked until email is verified. | ✅ Done |
| 3 | Login & Logout | Session-based login for web; Bearer token (Sanctum) for API. Remember-me cookie supported on web. | ✅ Done |
| 4 | Login Rate Limiting | Max 3 failed attempts per email+IP combination. 15-minute lockout with `retry_after` returned in 429 response. | ✅ Done |
| 5 | Password Reset | Signed reset link emailed (60-minute TTL). All existing tokens revoked on reset. | ✅ Done |
| 6 | User Profile | View and update name and phone. Upload avatar (JPEG/PNG/WebP, max 2 MB). | ✅ Done |
| 7 | Categories & Subcategories | 10 seeded top-level categories, each with subcategories. Public API and browse page. Admin can create, rename, and toggle active state. | ✅ Done |
| 8 | Post a Listing | Create classified ad with title, description, price, category, subcategory, location (city/state/country), listing type, and up to 10 images. | ✅ Done |
| 9 | Edit a Listing | Owner can update any field. Listing returns to pending/inactive for re-approval after edit. | ✅ Done |
| 10 | Delete a Listing | Soft delete. Admin can delete any listing. | ✅ Done |
| 11 | Image Upload | Up to 10 images per listing (JPEG/PNG/WebP, max 5 MB each). Individual image deletion supported. Preview counter shown in the UI. | ✅ Done |
| 12 | Listing Detail Page | Full listing view with image gallery (slider + thumbnails), seller info, contact button, related ads, and breadcrumb navigation. | ✅ Done |
| 13 | View Counter | Unique-ish view count incremented on every listing detail page load. Displayed on the listing card and detail page. | ✅ Done |
| 14 | Browse & Search | Filter by keyword (searches title, description, category name, subcategory name), category, subcategory, city, state, country, price range, and listing type. Full-text MySQL FULLTEXT index used for keyword search. | ✅ Done |
| 15 | Active Filter Chips | All applied filters shown as dismissible chips above search results. | ✅ Done |
| 16 | Sort Results | Sort by newest, oldest, price ascending, price descending. Featured listings always surface first. | ✅ Done |
| 17 | Search Autocomplete | Debounced dropdown showing up to 10 matching listing titles as the user types. | ✅ Done |
| 18 | Favorites | Toggle any listing as a favourite. View all favourited listings. Shown on listing cards for logged-in users. | ✅ Done |
| 19 | Mark as Sold | Owner can mark an active listing as sold. Sold badge shown on listing cards. | ✅ Done |
| 20 | Renew Listing | Owner can renew an expired listing — resets expiry to 60 days from now. | ✅ Done |
| 21 | Report a Listing | Authenticated users can report a listing with a reason (spam, fraud, prohibited, duplicate, other). Cannot report own listing. | ✅ Done |
| 22 | Admin: Listing Approval | Admin reviews pending listings and approves or rejects with a reason. Approval email sent to owner. | ✅ Done |
| 23 | Admin: Listing Detail Popup | Admin can view full listing details (images, description, seller, location) directly in the approval queue without leaving the page. | ✅ Done |
| 24 | Admin: Featured Listings | Admin can toggle `is_featured` on any approved listing. Featured listings appear first in all search results. | ✅ Done |
| 25 | Admin: Reports Queue | Moderators review submitted reports, remove the offending listing (with owner notification), or dismiss the report. | ✅ Done |
| 26 | Admin: User Management | Admin can deactivate or reactivate any user account. Deactivation revokes all tokens. | ✅ Done |
| 27 | Admin: Category Management | Admin can create categories, upload an icon (SVG/PNG), and toggle active state. | ✅ Done |
| 28 | Admin: Subcategory Management | Admin can add subcategories to any category, rename them, and toggle active state — all from a modal in the admin panel. | ✅ Done |
| 29 | Admin Dashboard | Platform-wide stats: total users, active listings, pending approvals, total views. | ✅ Done |
| 30 | User Dashboard | Per-user stats: total listings, active listings, total views, favourites count. | ✅ Done |
| 31 | My Listings | Paginated list of all the authenticated user's listings across all statuses. | ✅ Done |
| 32 | Listing Expiry | Scheduler runs daily — listings expire 60 days after posting or last renewal. | ✅ Done |
| 33 | Expiry Warning Email | Owner notified by email 7 days before a listing expires. | ✅ Done |
| 34 | Email Notifications | Queued emails for: email verification, listing approved, listing rejected (with reason), expiry warning, listing removed by moderator. | ✅ Done |
| 35 | Redis Caching | Listings cached by slug (300s TTL). Categories cached indefinitely. Cache busted on every write. | ✅ Done |
| 36 | XSS Prevention | `strip_tags()` applied in Form Requests on all free-text user input (name, title, description) before validation. | ✅ Done |
| 37 | REST API | Full versioned API under `/api/v1` with standard JSON envelope. OpenAPI 3.0 spec at `docs/openapi.yaml`. | ✅ Done |

---

## Phase 2 — Deferred

These features are intentionally not implemented. The schema and/or placeholder UI exists where noted.

| Feature | Rationale for deferral | Schema / placeholder |
|---------|------------------------|----------------------|
| In-app messaging | Requires real-time infrastructure (WebSockets or polling) beyond Phase 1 scope | `messages` table schema exists; "coming soon" shown in contact modal |
| Map / geolocation search | Requires a Maps API key and frontend map library | `location_lat` / `location_lng` columns exist (nullable) |
| In-app notification dropdown | Requires `notifications` table and polling/WebSocket | No table created |
| Payment gateway (Razorpay) | Requires merchant account and payment flow design | Not started |
| SMS / OTP phone verification | Requires SMS provider (Twilio / MSG91) | Phone field exists; no OTP flow |
| Social login (Google OAuth) | Requires Google OAuth credentials and Socialite setup | Not started |
| Dark mode | UI-only work deferred for Phase 2 responsive audit | Not started |
| Full responsive / Lighthouse audit | Basic Bootstrap 5 grid used; detailed audit deferred | Not started |

---

## Req 17 — QA Improvements (Implemented)

These items were identified during QA testing after the initial build and addressed as Requirement 17.

| Ref | Area | Improvement |
|-----|------|-------------|
| 17.1 | Security | XSS prevention: `strip_tags()` in `StoreListingRequest`, `UpdateListingRequest`, `RegisterRequest`, `UpdateProfileRequest` |
| 17.2 | Security | Password strength enforced: `Password::min(10)->mixedCase()->numbers()->symbols()` on register and password reset |
| 17.3 | Security | Login rate limiting: 3 attempts per `email\|ip`, 900-second lockout, 429 with `retry_after` |
| 17.4 | Security | Email verification required before a user can post a listing (web + API) |
| 17.5 | Data integrity | Country field added to listings (`location_country`, defaults to India) — shown in create, edit, show, and API output |
| 17.6 | Search | Country filter added to search sidebar and `SearchService`; keyword search extended to match category and subcategory names; inactive-category listings excluded from all public queries; subcategory dropdown in search sidebar populated by selected category |
| 17.7 | UI | Listing detail page: removed Subcategory and Ad ID rows from Ad Details panel |
| 17.8 | UI | Image upload: photo counter ("X / 10") shown; picker label changes to "Maximum photos reached" at cap; input disabled at 10 |
| 17.9 | UI | Footer: removed placeholder "Safety Tips" link; Contact Us → `mailto:` link; Privacy Policy / Terms → `title="Coming soon"` tooltip |
| 17.10 | Admin | Listing detail popup in admin approval queue: full description, images gallery, seller info, and action buttons (Approve / Reject / Feature / Delete) |
| 17.11 | Admin | Category icon changed from text class input to file upload (SVG/PNG, 100 KB); subcategory management panel added per category (add / rename / toggle active) |
