# Lokabuy — Claude Code Context

## Project Overview

A fully functional classified ads web portal. Built as a Laravel 8 + Blade monolith with a versioned REST API. Full requirements are defined in `requirements.md` at the project root.

---

## Rules — Read These First

Before starting any work, read all rule files in `.claude/rules/`:

| File | Covers |
|------|--------|
| [`.claude/rules/tech-stack.md`](.claude/rules/tech-stack.md) | Framework, language, frontend, approved packages, env vars |
| [`.claude/rules/coding-standards.md`](.claude/rules/coding-standards.md) | PHP conventions, architecture rules, naming, Blade, Git |
| [`.claude/rules/api-rules.md`](.claude/rules/api-rules.md) | Response envelope, status codes, auth, rate limiting, endpoints |
| [`.claude/rules/database-rules.md`](.claude/rules/database-rules.md) | Schema definitions, Eloquent conventions, Redis caching, seeders |
| [`.claude/rules/error-handling.md`](.claude/rules/error-handling.md) | Exception handler, logging, validation errors, Blade error pages |
| [`.claude/rules/project-structure.md`](.claude/rules/project-structure.md) | Full directory map, implementation order, file naming |

---

## Absolute Rules

1. Read all `.claude/rules/` files at the start of every session before any work
2. Follow the implementation order: **Migration → Model → Form Request → Service → Controller → API Resource → Blade View**
3. Ask before adding any Composer package not listed in `tech-stack.md`
4. Every endpoint with user input must have a Form Request class
5. Every JSON response must use the standard envelope from `api-rules.md`
6. Test each completed feature before marking it done
