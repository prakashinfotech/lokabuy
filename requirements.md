# Requirements Document

## Introduction

This document defines the requirements for a fully functional classified ads web portal. The portal enables users to post, browse, search, and respond to classified advertisements across multiple categories such as vehicles, real estate, electronics, furniture, jobs, and more. The system supports user account management, location-based filtering, image uploads, in-app messaging, and an admin dashboard.

The portal is built using the following technology stack:
- **Frontend**: HTML, CSS, JavaScript (Blade templating via Laravel)
- **Backend**: Laravel 8 (PHP 8.1) — RESTful API exposing all endpoints under `/api/v1`
- **Database**: MySQL
- **Cache**: Redis (for session tokens, rate limiting, and frequently accessed data)

---

## Glossary

- **Portal**: The classified ads web application as a whole.
- **User**: A registered individual who can post ads, browse listings, and communicate with other users.
- **Guest**: An unauthenticated visitor who can browse and search listings but cannot post or message.
- **Listing**: A classified advertisement posted by a User, containing title, description, price, images, category, and location.
- **Category**: A top-level classification for listings (e.g., Vehicles, Real Estate, Electronics, Furniture, Jobs, Services).
- **Subcategory**: A second-level classification nested under a Category (e.g., Cars under Vehicles).
- **Search_Engine**: The component responsible for querying and returning listings based on user-provided criteria.
- **Auth_Service**: The component responsible for user registration, login, logout, and session/token management.
- **Listing_Service**: The component responsible for creating, updating, retrieving, and deleting listings.
- **Image_Service**: The component responsible for uploading, storing, and serving listing images.
- **Messaging_Service**: The component responsible for in-app chat between buyers and sellers.
- **Notification_Service**: The component responsible for sending email and in-app notifications to users.
- **Admin_Dashboard**: The administrative interface for managing users, listings, categories, and reported content.
- **Moderator**: An admin user with permissions to review and act on reported listings or users.
- **Featured_Listing**: A paid or promoted listing displayed with higher visibility in search results and category pages.
- **Slug**: A URL-friendly string derived from a listing title used in the listing's permalink.
- **JWT**: JSON Web Token used for stateless authentication.
- **OTP**: One-Time Password used for phone number verification.
- **CDN**: Content Delivery Network used to serve static assets and images.
- **API**: The RESTful backend interface consumed by the frontend.
- **Listing_Type**: The transaction intent for a Real Estate listing — one of `buy`, `sell`, or `rent`.
- **Repository**: The Git version control repository containing all source code.

---

## Requirements

### Requirement 1: Project Setup and Version Control

**User Story:** As a developer, I want a properly configured project repository and development environment, so that the team can collaborate effectively and the codebase is maintainable.

#### Acceptance Criteria

1. THE Repository SHALL contain a root-level `README.md` describing the project, setup steps, environment variables, and how to run the application locally.
2. THE Repository SHALL contain a `.gitignore` file that excludes `vendor`, build artifacts, `.env` files, and OS-specific files.
3. THE Repository SHALL use a branching strategy with `main` as the production branch and `develop` as the integration branch.
4. THE Repository SHALL contain a `.env.example` file listing all required environment variables with placeholder values and inline comments.
5. THE Repository SHALL contain a PHP CS Fixer or Laravel Pint configuration file enforcing a consistent code style across all PHP source files.
6. WHEN a developer runs `composer install` followed by `php artisan migrate --seed`, THE Portal SHALL set up the complete database schema and seed data without errors.
7. THE Repository SHALL contain `docs/ARCHITECTURE.md` covering system layers, request lifecycle, auth flow, listing lifecycle, and caching strategy.
8. THE Repository SHALL contain `docs/API.md` with a full endpoint reference, example request/response bodies, and an error code guide — readable without an OpenAPI viewer.
9. THE Repository SHALL contain `docs/FEATURES.md` listing all Phase 1 features by completion status and all deferred Phase 2 items with rationale.
10. THE Repository SHALL contain `docs/TESTING.md` covering test environment setup, how to run the test suite, coverage targets, and a scenario table of critical test cases.

---

### Requirement 2: Database Design and Integration

**User Story:** As a developer, I want a well-structured relational database, so that all portal data is stored consistently and can be queried efficiently.

#### Acceptance Criteria

1. THE Portal SHALL use MySQL as the primary relational database.
2. THE Portal SHALL use Laravel Eloquent ORM to define and manage all database schemas.
3. THE Portal SHALL include Laravel migration files that can be applied in sequence to build the complete schema from scratch.
4. WHEN migrations are run on a fresh database, THE Portal SHALL create all required tables, indexes, and foreign key constraints without errors.
5. THE Portal SHALL define a `users` table with fields: `id`, `name`, `email`, `phone`, `password`, `avatar_url`, `is_verified`, `is_active`, `role`, `created_at`, `updated_at`.
6. THE Portal SHALL define a `listings` table with fields: `id`, `title`, `slug`, `description`, `price`, `currency`, `category_id`, `subcategory_id`, `user_id`, `listing_type` (for Real Estate: `buy`, `sell`, or `rent`), `location_city`, `location_state`, `location_lat`, `location_lng`, `status`, `approval_status`, `is_featured`, `view_count`, `created_at`, `updated_at`, `expires_at`.
7. THE Portal SHALL define a `categories` table and a `subcategories` table with a foreign key relationship between them.
8. THE Portal SHALL define a `listing_images` table with fields: `id`, `listing_id`, `url`, `order`, `created_at`.
9. THE Portal SHALL define a `messages` table with fields: `id`, `listing_id`, `sender_id`, `receiver_id`, `body`, `is_read`, `created_at`.
10. THE Portal SHALL define a `reports` table with fields: `id`, `listing_id`, `reporter_id`, `reason`, `status`, `created_at`.
11. THE Portal SHALL use Redis as a caching layer for session tokens, rate limiting counters, and frequently accessed category/listing data.
12. WHEN a listing is retrieved by its `id` or `slug`, THE Listing_Service SHALL check the Redis cache before querying MySQL, and cache the result for 300 seconds on a cache miss.

---

### Requirement 3: API Setup and Architecture

**User Story:** As a developer, I want a well-structured RESTful API, so that the frontend and any third-party clients can interact with portal data reliably.

#### Acceptance Criteria

1. THE API SHALL be built with Laravel 8 (PHP 8.1) and expose all endpoints under the `/api/v1` base path.
2. THE API SHALL return all responses in JSON format with a consistent envelope: `{ "success": boolean, "data": any, "error": string | null }`.
3. WHEN a request is made to an undefined route, THE API SHALL return HTTP 404 with `{ "success": false, "error": "Route not found" }`.
4. WHEN an unhandled server error occurs, THE API SHALL return HTTP 500 with `{ "success": false, "error": "Internal server error" }` and log the full error stack to the Laravel log.
5. THE API SHALL implement request rate limiting of 100 requests per minute per IP address for unauthenticated routes using Laravel's built-in throttle middleware.
6. THE API SHALL implement CORS, allowing requests only from the configured frontend origin(s) specified in environment variables.
7. THE API SHALL validate all incoming request bodies using Laravel Form Requests, and return HTTP 422 with field-level error details when validation fails.
8. THE API SHALL include an OpenAPI (Swagger) specification file documenting all endpoints, request schemas, and response schemas.
9. THE API SHALL be stateless, using Laravel Sanctum or Passport JWT Bearer tokens for authenticating protected endpoints.
10. WHEN a JWT token is expired or invalid, THE API SHALL return HTTP 401 with `{ "success": false, "error": "Unauthorized" }`.
11. ALL list and search endpoints that return listings SHALL eager-load related models (`user`, `category`, `subcategory`, `images`) in a single query using Eloquent `with()` to prevent N+1 query problems.
12. ALL API endpoints that return a single listing or a collection of listings SHALL use an API Resource class — never a raw model array or `toArray()` call.

---

### Requirement 4: User Authentication and Account Management

**User Story:** As a visitor, I want to register and log in to the portal, so that I can post listings and communicate with other users.

#### Acceptance Criteria

1. WHEN a visitor submits a registration form with a unique email, valid phone number, name, and password of at least 8 characters, THE Auth_Service SHALL create a new User account and return a JWT access token.
2. WHEN a visitor submits a registration form with an email that already exists, THE Auth_Service SHALL return HTTP 409 with the error message "Email already registered".
3. WHEN a registered User submits valid credentials (email and password), THE Auth_Service SHALL return a JWT access token valid for 24 hours and a refresh token valid for 30 days.
4. WHEN a User submits an invalid email or incorrect password, THE Auth_Service SHALL return HTTP 401 with the error message "Invalid credentials".
5. WHEN a User submits a valid refresh token, THE Auth_Service SHALL return a new JWT access token and invalidate the old refresh token.
6. WHEN a User requests phone verification, THE Auth_Service SHALL send an OTP to the registered phone number via SMS, valid for 10 minutes.
7. WHEN a User submits the correct OTP within the validity window, THE Auth_Service SHALL mark the User's `is_verified` field as `true`.
8. WHEN a User requests a password reset, THE Auth_Service SHALL send a password reset link to the registered email address, valid for 1 hour.
9. WHEN a User submits a valid password reset token and a new password of at least 8 characters, THE Auth_Service SHALL update the password hash and invalidate all existing refresh tokens for that User.
10. WHEN a logged-in User updates their profile (name, avatar, phone), THE Auth_Service SHALL persist the changes and return the updated User object.
11. THE Auth_Service SHALL hash all passwords using Laravel's built-in bcrypt hashing (cost factor 12) before storing them.
12. WHEN a User logs out, THE Auth_Service SHALL invalidate the token in the database/Redis so it cannot be reused.

---

### Requirement 5: Listing Creation and Management

**User Story:** As a registered User, I want to create, edit, and manage my classified ads, so that I can sell or offer items and services to other users.

#### Acceptance Criteria

1. WHEN a verified User submits a new listing with a title (3–100 characters), description (20–4000 characters), price, category, subcategory, city, and at least one image, THE Listing_Service SHALL create the listing with `approval_status` set to `pending` and `status` set to `inactive`, and notify the User that the listing is awaiting admin approval.
2. WHEN an admin approves a pending listing, THE Listing_Service SHALL set the listing's `approval_status` to `approved` and `status` to `active`, making it visible in public search results and category pages.
3. WHEN an admin rejects a pending listing, THE Listing_Service SHALL set the listing's `approval_status` to `rejected` and notify the User with the rejection reason.
4. WHEN a User submits a listing without a required field, THE Listing_Service SHALL return HTTP 422 with field-level validation errors.
5. THE Listing_Service SHALL generate a unique URL-friendly `slug` from the listing title and a short unique suffix (e.g., `honda-city-2020-abc123`).
6. WHEN a User uploads images for a listing, THE Image_Service SHALL accept up to 10 images per listing, each no larger than 5 MB, in JPEG, PNG, or WebP format.
7. WHEN an uploaded image exceeds 5 MB or is not in an accepted format, THE Image_Service SHALL return HTTP 422 with the error "Invalid image: must be JPEG, PNG, or WebP and under 5 MB".
8. THE Image_Service SHALL store uploaded images in the server's local storage or cloud object storage (e.g., AWS S3) and return a publicly accessible URL for each image.
9. WHEN a User edits their own listing, THE Listing_Service SHALL update the listing fields, reset `approval_status` to `pending`, set `status` back to `inactive`, and notify the admin of the updated listing awaiting re-approval.
10. WHEN a User attempts to edit a listing they do not own, THE Listing_Service SHALL return HTTP 403 with the error "Forbidden".
11. WHEN a User marks their listing as sold, THE Listing_Service SHALL set the listing status to `sold` and remove it from active search results.
12. WHEN a User deletes their own listing, THE Listing_Service SHALL set the listing status to `deleted` (soft delete) and remove it from all public views.
13. THE Listing_Service SHALL automatically set a listing's status to `expired` when the current timestamp exceeds the listing's `expires_at` value (default 60 days from creation).
14. WHEN a User views their own listings dashboard, THE Listing_Service SHALL return all listings owned by that User, including those with `active`, `sold`, `expired`, `deleted`, and `pending` statuses, paginated at 20 per page.
15. WHEN a User submits a new listing with images, THE Listing_Service SHALL wrap the listing record creation and all image uploads inside a single database transaction, so that a failed image upload rolls back the listing record and no partial data is persisted.
16. WHEN a User edits a listing, THE Listing_Service SHALL wrap the listing update and any image changes inside a single database transaction.

---

### Requirement 6: Categories, Subcategories, and Listing Types

**User Story:** As a User, I want to browse listings organized by category and subcategory, so that I can quickly find what I am looking for.

#### Acceptance Criteria

1. THE Portal SHALL support the following top-level categories: Vehicles, Real Estate, Electronics, Furniture & Home, Fashion, Books & Sports, Jobs, Services, Pets, and Agriculture.
2. WHEN a User posts or browses a listing under the **Real Estate** category, THE Portal SHALL require the User to select a `listing_type` of either `buy`, `sell`, or `rent` to indicate the nature of the property transaction.
3. WHEN a Guest or User filters Real Estate listings by `listing_type`, THE Search_Engine SHALL return only listings matching the selected type (`buy`, `sell`, or `rent`).
4. WHEN a Guest or User requests the category list, THE Portal SHALL return all active categories with their subcategories in a single API response.
5. THE Portal SHALL seed the database with all categories and subcategories during initial setup via a Laravel seeder.
6. WHEN a User navigates to a category page, THE Portal SHALL display all approved active listings in that category, sorted by `created_at` descending, paginated at 20 per page.
7. WHEN a User navigates to a subcategory page, THE Portal SHALL display only approved listings matching that subcategory, sorted by `created_at` descending, paginated at 20 per page.
8. WHERE a category has Featured_Listings, THE Portal SHALL display Featured_Listings at the top of the category page before organic results.

---

### Requirement 7: Search and Filtering

**User Story:** As a User, I want to search and filter listings by keyword, location, price, and category, so that I can find relevant ads quickly.

#### Acceptance Criteria

1. WHEN a User submits a search query with a keyword, THE Search_Engine SHALL return all active listings whose title or description contains the keyword (case-insensitive), paginated at 20 per page.
2. WHEN a User applies a location filter (city or state), THE Search_Engine SHALL return only listings matching the specified location.
3. WHEN a User applies a price range filter (min and/or max), THE Search_Engine SHALL return only listings with a price within the specified range.
4. WHEN a User applies a category filter, THE Search_Engine SHALL return only listings belonging to the specified category or subcategory.
5. WHEN a User applies multiple filters simultaneously, THE Search_Engine SHALL return listings matching ALL applied filters (AND logic).
6. WHEN a search query returns no results, THE Search_Engine SHALL return an empty `data` array and a `total` count of 0.
7. THE Search_Engine SHALL support sorting results by: `newest` (default), `price_asc`, `price_desc`, and `most_relevant`.
8. WHEN a User searches by keyword, THE Search_Engine SHALL return results within 500ms for datasets up to 100,000 active listings.
9. THE Portal SHALL implement full-text search using MySQL FULLTEXT indexes on the `title` and `description` columns of the `listings` table.
10. WHEN a User types in the search bar, THE Portal frontend SHALL display autocomplete suggestions based on recent popular searches and matching listing titles, with a debounce of 300ms.

---

### Requirement 8: Listing Detail Page

**User Story:** As a User, I want to view the full details of a listing, so that I can decide whether to contact the seller.

#### Acceptance Criteria

1. WHEN a Guest or User navigates to a listing's URL (by slug), THE Portal SHALL display the listing title, description, price, images, category, location, seller name, and posting date.
2. WHEN a listing is viewed, THE Listing_Service SHALL increment the listing's `view_count` by 1.
3. THE Portal SHALL display a gallery of listing images with the ability to navigate between images.
4. WHEN a Guest clicks "Contact Seller", THE Portal SHALL redirect the Guest to the login page with a return URL to the listing.
5. WHEN a logged-in User clicks "Contact Seller", THE Portal SHALL open the in-app messaging interface pre-populated with the listing context.
6. THE Portal SHALL display the seller's name, member-since date, and total active listing count on the listing detail page.
7. WHEN a User clicks "Report Ad", THE Portal SHALL display a form with predefined report reasons (Spam, Fraud, Prohibited item, Duplicate, Other) and submit the report to the `reports` table.
8. THE Portal SHALL display up to 6 related listings from the same subcategory at the bottom of the listing detail page.

---

### Requirement 9: In-App Messaging *(Phase 2 — Not in current scope)*

> **Phase 2 note:** Full in-app messaging with real-time WebSocket delivery is deferred to Phase 2. The `messages` table and schema will be created in Phase 1 to avoid a breaking migration later, but no messaging API endpoints, inbox UI, or WebSocket integration will be built in this phase.

**User Story:** As a User, I want to send and receive messages about listings, so that I can negotiate and coordinate with buyers or sellers without sharing personal contact details.

#### Acceptance Criteria

1. WHEN a logged-in User sends a message about a listing, THE Messaging_Service SHALL store the message in the `messages` table and deliver it to the recipient in real time using WebSockets.
2. WHEN a User opens their inbox, THE Messaging_Service SHALL return all conversation threads grouped by listing, sorted by the most recent message timestamp descending.
3. WHEN a User opens a conversation thread, THE Messaging_Service SHALL return all messages in that thread sorted by `created_at` ascending, paginated at 50 per page.
4. WHEN a message is delivered to the recipient, THE Messaging_Service SHALL mark the message as delivered and send an in-app notification to the recipient.
5. WHEN a recipient reads a message, THE Messaging_Service SHALL set the message's `is_read` field to `true`.
6. THE Messaging_Service SHALL display an unread message count badge on the inbox icon in the navigation bar.
7. IF a User attempts to send a message to themselves (sender_id equals receiver_id), THEN THE Messaging_Service SHALL return HTTP 400 with the error "Cannot send a message to yourself".
8. THE Messaging_Service SHALL limit message body length to 1000 characters and return HTTP 422 if exceeded.

---

### Requirement 10: User Dashboard and Profile

**User Story:** As a registered User, I want a personal dashboard, so that I can manage my listings, messages, and account settings in one place.

#### Acceptance Criteria

1. WHEN a logged-in User navigates to their dashboard, THE Portal SHALL display a summary of their active listings count, sold listings count, total views across all listings, and unread message count.
2. WHEN a logged-in User navigates to their listings tab, THE Portal SHALL display all their listings with status badges (Active, Sold, Expired, Deleted) and action buttons (Edit, Mark as Sold, Delete, Renew).
3. WHEN a User clicks "Renew" on an expired listing, THE Listing_Service SHALL reset the listing's `expires_at` to 60 days from the current date and set its status back to `active`.
4. WHEN a logged-in User navigates to their profile settings, THE Portal SHALL display a form pre-populated with their current name, email, phone, and avatar.
5. WHEN a User submits updated profile information, THE Auth_Service SHALL validate and persist the changes, returning the updated User object.
6. WHEN a User uploads a new avatar image, THE Image_Service SHALL accept images up to 2 MB in JPEG, PNG, or WebP format, store them in cloud storage, and update the User's `avatar_url`.

---

### Requirement 11: Location and Map Integration *(Phase 2 — Not in current scope)*

> **Phase 2 note:** Map-based location picking, geolocation detection, radius filtering, and map views on search results are deferred to Phase 2. In Phase 1, location is captured as plain text fields (`location_city`, `location_state`) and the `location_lat`/`location_lng` columns are created in the schema but left nullable and unpopulated.

**User Story:** As a User, I want to see listings on a map and filter by location, so that I can find items near me.

#### Acceptance Criteria

1. WHEN a User creates or edits a listing, THE Portal SHALL provide a location picker that allows the User to search for a city or drop a pin on a map to set `location_lat` and `location_lng`.
2. WHEN a User grants browser geolocation permission, THE Portal SHALL automatically detect and pre-fill the User's city in the location filter.
3. WHEN a User applies a radius filter (e.g., within 10 km, 25 km, 50 km) from a given coordinate, THE Search_Engine SHALL return only listings whose coordinates fall within the specified radius using the Haversine formula.
4. THE Portal SHALL display a map view on search results pages showing listing pins at their respective coordinates using an embedded map (e.g., Google Maps or Leaflet with OpenStreetMap).
5. WHEN a User clicks a listing pin on the map, THE Portal SHALL display a preview card with the listing title, price, and thumbnail image.

---

### Requirement 12: Notifications *(Email only in Phase 1 — In-app notifications deferred to Phase 2)*

> **Phase 2 note:** In-app notification dropdown, notification bell badge, and the `notifications` table are deferred to Phase 2. Phase 1 delivers transactional email notifications only via a mail service (e.g., SendGrid or AWS SES). No SMS, no push notifications, no in-app notification UI.

**User Story:** As a User, I want to receive email notifications about activity on my listings, so that I stay informed without having to check the portal constantly.

#### Acceptance Criteria

1. WHEN a listing is approved by an admin, THE Notification_Service SHALL send an email notification to the listing owner informing them that their ad is now live.
2. WHEN a listing is rejected by an admin, THE Notification_Service SHALL send an email notification to the listing owner with the rejection reason.
3. WHEN a listing is about to expire (7 days before `expires_at`), THE Notification_Service SHALL send an email notification to the listing owner with a renewal link.
4. WHEN a listing is removed following a report action by a Moderator, THE Notification_Service SHALL send an email notification to the listing owner with the outcome.
5. THE Notification_Service SHALL use a transactional email provider (e.g., SendGrid or AWS SES) configured via environment variables to send all email notifications.
6. WHEN a User opts out of email notifications in their account settings, THE Notification_Service SHALL not send email notifications to that User.

---

### Requirement 13: Admin Dashboard

**User Story:** As a Moderator or admin, I want an admin dashboard, so that I can manage users, listings, categories, reported content, and approve or reject new listings.

#### Acceptance Criteria

1. WHEN an admin User logs in and navigates to `/admin`, THE Admin_Dashboard SHALL display summary statistics: total users, total active listings, total pending listings awaiting approval, total reports pending review, and new registrations in the last 7 days.
2. WHEN an admin navigates to the listing approval queue, THE Admin_Dashboard SHALL display all listings with `approval_status` of `pending`, showing the listing title, category, seller name, and submission date.
3. WHEN an admin clicks "Approve" on a pending listing, THE Admin_Dashboard SHALL set the listing's `approval_status` to `approved` and `status` to `active`, and send a notification to the listing owner that their ad is now live.
4. WHEN an admin clicks "Reject" on a pending listing, THE Admin_Dashboard SHALL set the listing's `approval_status` to `rejected`, and prompt the admin to enter a rejection reason before sending a notification to the listing owner.
5. WHEN a Moderator navigates to the reports queue, THE Admin_Dashboard SHALL display all reports with status `pending`, showing the reported listing, reporter, reason, and submission date.
6. WHEN a Moderator clicks "Remove Listing" on a report, THE Admin_Dashboard SHALL set the listing status to `deleted`, resolve the report as `actioned`, and trigger a notification to the listing owner.
7. WHEN a Moderator clicks "Dismiss Report" on a report, THE Admin_Dashboard SHALL set the report status to `dismissed` without modifying the listing.
8. WHEN an admin navigates to the users list, THE Admin_Dashboard SHALL display all users with their registration date, listing count, and account status, paginated at 50 per page.
9. WHEN an admin deactivates a User account, THE Admin_Dashboard SHALL set the User's `is_active` field to `false`, invalidate all active sessions for that User, and prevent the User from logging in.
10. WHEN an admin navigates to the categories management page, THE Admin_Dashboard SHALL allow adding, renaming, and deactivating categories and subcategories.
11. THE Admin_Dashboard SHALL be accessible only to Users with `role` equal to `admin` or `moderator`, and THE API SHALL return HTTP 403 for any other role attempting to access admin endpoints.
12. WHEN an admin approves or rejects a listing, THE Admin_Dashboard SHALL wrap the status update and the notification dispatch inside a single database transaction, so that a notification failure does not leave the listing in an inconsistent state.

---

### Requirement 14: Featured Listings and Promotions

**User Story:** As a User, I want to promote my listing to increase its visibility, so that it reaches more potential buyers.

#### Acceptance Criteria

1. WHEN a User selects a promotion plan for their listing and completes payment, THE Listing_Service SHALL set the listing's `is_featured` field to `true` and record the promotion expiry date.
2. WHILE a listing's `is_featured` is `true` and the promotion has not expired, THE Portal SHALL display the listing with a "Featured" badge and prioritize it in search results and category pages.
3. WHEN a featured listing's promotion expires, THE Listing_Service SHALL set `is_featured` to `false` and return the listing to organic ranking.
4. THE Portal SHALL integrate with a payment gateway (e.g., Razorpay for India) to process promotion payments securely.
5. WHEN a payment fails, THE Listing_Service SHALL not set `is_featured` to `true` and THE Portal SHALL display an error message to the User.
6. WHEN a payment succeeds, THE Notification_Service SHALL send a confirmation email to the User with the promotion details and expiry date.

---

### Requirement 15: Responsive Frontend *(Phase 2 — Not in current scope)*

> **Phase 2 note:** Full responsive design polish, Lighthouse performance targets, dark mode, bottom mobile navigation bar, and lazy loading are deferred to Phase 2. Phase 1 Blade views will use Bootstrap 5 for basic layout and will be functional across common screen sizes, but no formal responsiveness audit or performance benchmarking is required in this phase.

**User Story:** As a User, I want to access the portal from any device, so that I can browse and post listings on desktop, tablet, or mobile.

#### Acceptance Criteria

1. THE Portal frontend SHALL be built with HTML, CSS, and JavaScript using Laravel Blade templating, and be fully responsive across viewport widths from 320px to 2560px.
2. THE Portal SHALL use a CSS framework (e.g., Bootstrap 5 or Tailwind CSS) to ensure consistent styling and responsive layout.
3. THE Portal SHALL achieve a Google Lighthouse performance score of at least 80 on mobile for the home page and listing detail page.
4. THE Portal SHALL display a sticky navigation bar with links to: Home, Sell (post a listing), Categories, and the User's account menu (or Login/Register if unauthenticated).
5. THE Portal home page SHALL display a hero search bar, a category grid, and sections for Featured Listings and Recently Added listings.
6. THE Portal SHALL implement lazy loading for listing images to reduce initial page load time.
7. WHEN a User is on a mobile device, THE Portal SHALL display a bottom navigation bar with icons for Home, Search, Sell, Messages, and Profile.
8. THE Portal SHALL support both light and dark mode, persisting the User's preference in `localStorage`.

---

### Requirement 16: Testing

**User Story:** As a developer, I want comprehensive automated tests, so that I can confidently make changes without introducing regressions.

#### Acceptance Criteria

1. THE Portal SHALL include unit tests for all API controllers, service classes, and utility functions using PHPUnit, achieving at least 80% code coverage on the backend.
2. THE Portal SHALL include feature (integration) tests for all API endpoints using Laravel's built-in testing helpers and a dedicated test database, verifying correct HTTP status codes, response shapes, and database state changes.
3. WHEN the test suite is run with `php artisan test`, THE Portal SHALL execute all unit and feature tests and report pass/fail results with coverage metrics.
4. THE Portal SHALL include a property-based test verifying that for all valid listing objects, serializing a listing to JSON and deserializing it back produces an equivalent listing object (round-trip property).
5. THE Portal SHALL include a property-based test verifying that for any combination of valid search filters, the Search_Engine returns only listings that satisfy ALL applied filters (filter correctness invariant).
6. THE Portal SHALL include a CI configuration file (e.g., GitHub Actions `.github/workflows/ci.yml`) that runs linting, unit tests, and feature tests on every pull request to the `develop` and `main` branches.

---

### Requirement 17: QA Findings — Bug Fixes and UX Improvements *(Phase 1 — Fix Now)*

> **Context:** These requirements were identified during a functional evaluation pass. All items below must be resolved in both the web (Blade) and API layers unless noted otherwise.

---

#### 17.1 — Input Security (XSS Prevention)

**User Story:** As a developer, I want all user-supplied text inputs to be sanitised before storage, so that the portal is protected against stored XSS attacks.

##### Acceptance Criteria

1. WHEN a User submits any open text field (listing title, listing description, user name, profile bio), THE Portal SHALL strip all HTML tags from the value before persisting it to the database.
2. THE Form Request classes (`StoreListingRequest`, `UpdateListingRequest`, `RegisterRequest`, `UpdateProfileRequest`) SHALL call `strip_tags()` on all string fields inside `prepareForValidation()`.
3. WHEN a listing description is displayed on the listing detail page, THE Portal SHALL render it using Blade's escaped `{{ }}` syntax — never `{!! !!}` — so that any residual markup is rendered as plain text.

---

#### 17.2 — Registration Validation

**User Story:** As a developer, I want stronger registration validation, so that only properly formatted and secure credentials are accepted.

##### Acceptance Criteria

1. WHEN a visitor submits the registration form, THE Auth_Service SHALL require the password to be at least **10 characters** and contain at least one uppercase letter, one lowercase letter, one digit, and one special character (`@`, `$`, `!`, `%`, `*`, `?`, `&`, `#`).
2. WHEN a visitor submits the registration form with a `phone` value, THE Auth_Service SHALL validate that it contains only digits, spaces, dashes, and an optional leading `+`, with a total length between 7 and 15 digits (E.164-compatible).
3. WHEN a visitor submits the registration form with a `phone` value that does not match the expected format, THE Auth_Service SHALL return HTTP 422 with a field-level error: `"Phone number must be a valid international format (e.g. +91 98765 43210)"`.
4. WHEN a new User account is created, THE Notification_Service SHALL send an account activation email to the registered email address containing a unique, time-limited (24-hour) verification link.
5. WHEN a User clicks the activation link within 24 hours, THE Auth_Service SHALL set `email_verified_at` on the user record.
6. WHEN an unverified User attempts to post a new listing, THE Portal SHALL redirect them to a "Please verify your email" page with an option to resend the verification email.

---

#### 17.3 — Login Security

**User Story:** As a developer, I want login attempts to be rate-limited per account, so that brute-force attacks are mitigated.

##### Acceptance Criteria

1. WHEN a User submits incorrect credentials, THE Auth_Service SHALL increment a failed-attempt counter keyed by email address and IP address, stored in Redis.
2. WHEN the failed-attempt counter for a given email reaches **3 within a 15-minute window**, THE Auth_Service SHALL return HTTP 429 with the error "Too many login attempts. Please try again in 15 minutes." and reject further login attempts for that email for 15 minutes.
3. WHEN a User successfully authenticates, THE Auth_Service SHALL reset the failed-attempt counter for that email and IP.
4. THE Portal "Remember Me" checkbox SHALL extend the Sanctum session token lifetime to **30 days** from the default 24-hour window.
5. WHEN the "Remember Me" session token expires or the User logs out, THE Auth_Service SHALL invalidate the token immediately.
6. THE forgot-password flow SHALL send a reset link to the registered email, valid for **1 hour**; the link SHALL be single-use and invalidated after use.

---

#### 17.4 — Listing Location: Country Field

**User Story:** As a User, I want to specify my country when posting a listing, so that buyers can filter by country in search.

##### Acceptance Criteria

1. THE `listings` table SHALL include a `location_country` column (`string(100)`, not nullable, default `'India'`).
2. THE listing creation and edit forms SHALL include a **Country** field positioned after the State field; it SHALL default to `"India"` but be editable.
3. THE `StoreListingRequest` and `UpdateListingRequest` SHALL validate `location_country` as required, string, max 100 characters.
4. THE `ListingResource` SHALL include `location_country` in its JSON output.
5. THE Search_Engine SHALL accept a `country` query parameter and filter listings to those matching `location_country` (case-insensitive `LIKE`).
6. THE search results sidebar SHALL include a **Country** text input filter beneath the State field.

---

#### 17.5 — Listing Price Validation

**User Story:** As a developer, I want the price field to strictly accept numeric input, so that invalid string values are rejected.

##### Acceptance Criteria

1. THE `StoreListingRequest` and `UpdateListingRequest` SHALL enforce `'numeric'` and `'min:0'` on the `price` field.
2. THE listing create and edit forms SHALL use `<input type="number" min="0" step="0.01">` for the price field to prevent non-numeric input at the browser level.
3. WHEN a non-numeric value is submitted for `price` via the API, THE API SHALL return HTTP 422 with `"The price field must be a number."`.

---

#### 17.6 — Search and Filtering Improvements

**User Story:** As a User, I want the search and filter system to return relevant results and clearly show which filters are active.

##### Acceptance Criteria

1. WHEN a User searches by keyword, THE Search_Engine SHALL also match listings whose **category name** or **subcategory name** contains the keyword (in addition to title and description FULLTEXT search).
2. THE search results sidebar SHALL include a **Subcategory** filter dropdown that is populated with subcategories belonging to the currently selected category. WHEN no category is selected, the subcategory dropdown SHALL be hidden.
3. THE search results sidebar SHALL include a **Country** text field filter.
4. The **Listing Type** filter (`buy`, `sell`, `rent`) SHALL apply correctly when selected; filtering results to only those listings whose `listing_type` matches.
5. THE search results page SHALL display all currently active filters as dismissible chips/badges — including keyword, category, subcategory, city, state, country, price range, and listing type. No active filter SHALL be silently omitted from the chips display.
6. WHEN a category or subcategory is set to `is_active = false`, THE Search_Engine SHALL exclude all listings belonging to that category or subcategory from public search results and browse pages.
7. THE listing detail page **Ad Details** panel (right sidebar) SHALL NOT display the `Subcategory` row or the `Ad ID` row.
8. The **Related Ads** section on the listing detail page SHALL display up to **6** active approved listings from the same subcategory (falling back to the same category if fewer than 6 subcategory matches exist), excluding the current listing.
9. The **Report this Ad** modal SHALL submit the selected reason to `POST /api/v1/listings/{slug}/report` and display a success confirmation message on submission. The report reason SHALL be required.

---

#### 17.7 — Image Upload Validation

**User Story:** As a User, I want clear feedback when my image upload exceeds limits, so that I know exactly what is allowed.

##### Acceptance Criteria

1. THE listing create and edit forms SHALL display the allowed image constraints prominently: "Up to 10 photos · JPEG, PNG, or WebP · Max 5 MB each".
2. THE `StoreListingImageRequest` SHALL enforce a maximum of **10 images per listing** (total across all uploads); exceeding this limit SHALL return HTTP 422.
3. THE listing create form image picker SHALL display a running count (e.g., "3 / 10 photos selected") and disable the add-photo button once 10 images are selected.

---

#### 17.8 — Footer Cleanup

**User Story:** As a User, I want all footer links to work correctly, so that I can access help and policy pages.

##### Acceptance Criteria

1. THE footer SHALL NOT display a "Safety Tips" link.
2. THE footer "Contact Us" link SHALL navigate to a valid contact page or mailto link; placeholder `#` hrefs are not permitted.
3. THE footer "Privacy Policy" and "Terms of Use" links SHALL navigate to dedicated policy pages or display a "Coming Soon" placeholder — not bare `#` hrefs.
4. All footer navigation links SHALL be verified as functional; broken or dead links SHALL be removed or replaced.

---

#### 17.9 — Admin: Listing Detail Popup

**User Story:** As an admin, I want to see the full listing details and all uploaded images inside the approval popup, so that I can make an informed approve/reject decision.

##### Acceptance Criteria

1. THE admin listings approval table SHALL include a **"View Details"** button for each listing that opens a modal showing: title, description, price, category, subcategory, location, listing type, seller name, submission date, and **all uploaded images** in a scrollable gallery.
2. THE admin listing detail modal SHALL include an **"Approve"** and **"Reject"** button so the admin can act without closing the modal.
3. THE admin listing detail modal SHALL include a **"Delete"** button that hard-deletes the listing (bypassing soft delete) for cases where the content is clearly abusive.

---

#### 17.10 — Admin: Category and Subcategory Management

**User Story:** As an admin, I want to manage category icons via file upload and manage subcategories from the admin panel, so that the category hierarchy is maintainable without code changes.

##### Acceptance Criteria

1. THE admin category create/edit form SHALL replace the free-text icon name field with a **file upload input** accepting SVG or PNG images up to **100 KB**. The currently uploaded icon SHALL be displayed as a preview.
2. THE `categories` table `icon` column SHALL store the uploaded icon file URL (path in local storage) rather than an icon class name string.
3. WHEN an uploaded category icon exceeds 100 KB or is not SVG or PNG, THE Admin_Dashboard SHALL return HTTP 422 with the error "Icon must be SVG or PNG and under 100 KB".
4. THE admin categories page SHALL include a **subcategory management section** per category: listing all existing subcategories with their `is_active` status, and buttons to add a new subcategory, rename, and toggle active/inactive.
5. WHEN a category is set to `is_active = false`, THE Portal SHALL exclude ALL listings under that category from public search results, browse pages, and the categories dropdown — treating them as if the category does not exist for non-admin users.
6. WHEN a subcategory is set to `is_active = false`, THE Portal SHALL exclude listings under that subcategory from public browse and search, while still allowing the parent category to function normally.
