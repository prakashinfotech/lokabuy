# Contributing to Lokabuy

Thank you for contributing to Lokabuy. Please keep changes focused, tested, and consistent with the project's Laravel and API conventions.

## Before You Start

1. Read `CLAUDE.md` and the applicable files in `.claude/rules/`.
2. Check existing issues and documentation before starting work.
3. For security vulnerabilities, follow `SECURITY.md` instead of opening a public issue.

## Development Setup

Requirements and setup steps are documented in `README.md`.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

Use a local `.env` file. Never commit `.env`, credentials, tokens, logs, cache files, uploaded files, or the `vendor/` directory.

## Branches and Commits

Create feature branches from `master` using the project convention:

```text
feature/<short-name>
fix/<short-name>
chore/<short-name>
```

Use a concise conventional commit message:

```text
feat: add listing renewal endpoint
fix: prevent duplicate listing slugs
chore: update development documentation
```

Do not commit directly to `master` unless the repository maintainers explicitly request it.

## Implementation Guidelines

Follow the project's feature order where applicable:

```text
Migration -> Model -> Form Request -> Service -> Controller -> API Resource -> Blade View
```

- Keep controllers thin; put business logic in services.
- Use Eloquent for database access and Form Requests for validation.
- Use policies and middleware for authorization.
- Use API Resources for JSON responses.
- Keep API routes under `/api/v1` and follow the documented response envelope.
- Use Bootstrap 5 and Alpine.js according to the existing frontend conventions.
- Avoid unrelated refactors and new dependencies. Ask before adding a Composer package that is not already approved.

## Testing and Quality Checks

Run the relevant tests while developing, then run the full test suite when practical:

```bash
php artisan test
```

Run Laravel Pint before submitting changes:

```bash
./vendor/bin/pint
./vendor/bin/pint --test
```

When changing an API endpoint, update `docs/openapi.yaml` and add or update focused feature tests. When changing database structure, include a reversible migration and update related models, factories, seeders, or tests as needed.

## Pull Requests

A pull request should include:

- A clear summary of the change and its motivation
- Tests and quality checks that were run
- Any migration, configuration, API, or documentation impact
- Screenshots or request/response examples for user-facing changes when useful
- Notes about known limitations or follow-up work

Keep pull requests reviewable. Resolve review feedback with additional commits or a clean follow-up, according to the maintainer's preference.
