---
description: Review any file or feature in this Laravel project against the project's coding standards, API rules, database rules, error handling rules, and project structure conventions. Invoke with /review <filepath> or /review to review the last changed file.
---

# Code Review Skill

You are reviewing code for the Lokabuy Laravel project. The project rules live in `.claude/rules/`. Read each relevant rule file before reviewing, then audit the provided file(s) against them.

## Input

`$ARGUMENTS` is either:
- A file path (e.g. `app/Services/ListingService.php`)
- A directory path (e.g. `app/Http/Controllers/Api/V1/`)
- Empty — in which case ask the user which file to review

## Step 1 — Read the Relevant Rules

Always read these before reviewing:
- `.claude/rules/coding-standards.md`
- `.claude/rules/error-handling.md`

Then read additional rule files based on the file type:

| File type | Also read |
|-----------|-----------|
| Controller (`Controllers/Api/`) | `.claude/rules/api-rules.md` |
| Controller (`Controllers/Web/`) | `.claude/rules/coding-standards.md` |
| Migration | `.claude/rules/database-rules.md` |
| Model | `.claude/rules/database-rules.md` |
| Service | `.claude/rules/coding-standards.md` |
| Form Request | `.claude/rules/api-rules.md` |
| API Resource | `.claude/rules/api-rules.md` |
| Blade view | `.claude/rules/coding-standards.md` |
| Seeder / Factory | `.claude/rules/database-rules.md` |
| Test file | `.claude/rules/coding-standards.md` |

## Step 2 — Read the File(s)

Read the file(s) provided in `$ARGUMENTS` fully before making any judgements.

## Step 3 — Audit Against Each Rule Category

Check every item below that applies to the file type. Skip sections that are clearly not relevant.

### PHP & Architecture
- [ ] `declare(strict_types=1)` is the first statement after `<?php`
- [ ] No business logic in controllers — logic is in a Service class
- [ ] Validation is in a Form Request class, not in the controller
- [ ] JSON responses use an API Resource class, not `->toArray()` or raw arrays
- [ ] Route model binding used instead of manual `findOrFail()`
- [ ] Authorization via a Policy, not inline role checks
- [ ] No `dd()`, `dump()`, `var_dump()` present
- [ ] No `env()` called directly in application code — uses `config()` instead
- [ ] No secrets or credentials hardcoded
- [ ] PSR-4 naming: class name matches filename, one class per file

### API Rules (Controllers/Api/ only)
- [ ] Response uses the correct envelope: `{ "success", "data", "error" }`
- [ ] Correct HTTP status codes used (201 for create, 204 for delete, 422 for validation, etc.)
- [ ] Rate limiting middleware applied where required
- [ ] Auth middleware (`auth:sanctum`) present on protected routes
- [ ] Admin/moderator routes use `EnsureAdmin` or `EnsureModerator` middleware
- [ ] No session state used in API routes

### Database (Models / Migrations only)
- [ ] `$fillable` is defined — `$guarded = []` is not used
- [ ] `$casts` defined for booleans, enums, decimals, timestamps
- [ ] Relationships defined in both directions
- [ ] Local scopes used for frequently reused query conditions (`scopeActive()`, etc.)
- [ ] `SoftDeletes` trait present on `Listing` model only
- [ ] Migration has a working `down()` method
- [ ] FULLTEXT index present on `listings.title` and `listings.description` (in listings migration)
- [ ] No raw SQL used for data mutations

### Error Handling
- [ ] `ModelNotFoundException` handled (via route model binding or Handler)
- [ ] `AuthorizationException` handled (via Policy)
- [ ] No raw exception messages exposed to the client
- [ ] `Log::error()` used (not `error_log()` or `echo`) for unexpected errors
- [ ] No PII (email, phone) logged — only user ID
- [ ] Queue failures do not break the primary request cycle

### Blade / Frontend (views only)
- [ ] Extends the correct layout (`layouts.guest`, `layouts.app`, or `layouts.admin`)
- [ ] Repeated HTML extracted into `partials/`
- [ ] No inline `<style>` blocks
- [ ] Images served via `Storage::url()`, not hardcoded paths
- [ ] Alpine.js used for interactivity, not jQuery or other libraries

### Naming Conventions
- [ ] File and class names match the convention table in `coding-standards.md`
- [ ] Route names follow dot-separated convention (`listings.show`, `admin.listings.approve`)
- [ ] Table names are plural snake_case; foreign keys are `{singular}_id`

### Phase Scope
- [ ] No Phase 2 features implemented (messaging API, map/geo, in-app notifications, payment gateway, SMS/OTP)

## Step 4 — Report

Structure your output exactly like this:

---

## Review: `<filepath>`

### Summary
One sentence on the overall quality and whether it's ready to merge.

### Issues

List every violation found. Group by severity:

**Critical** — must fix before merging (broken logic, security issue, wrong pattern)
- `line X`: description of the issue and what the rule says

**Warning** — should fix (style, minor pattern violation, missing best practice)
- `line X`: description

**Suggestion** — optional improvement (readability, minor refactor)
- `line X`: description

### Passed Checks
List the rule categories that were fully satisfied (to confirm what was checked).

### Verdict
`APPROVED` / `NEEDS CHANGES` / `BLOCKED`

---

If there are zero issues, say so clearly and give the `APPROVED` verdict.
If `$ARGUMENTS` is empty, ask: "Which file would you like me to review?"
