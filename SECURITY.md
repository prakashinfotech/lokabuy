# Security Policy

## Supported Versions

Security fixes are currently applied to the master branch. Please use the latest commit when reproducing a possible vulnerability.

## Reporting a Vulnerability

Please do not report security vulnerabilities through a public GitHub issue.

Report vulnerabilities privately through the repository's configured security contact or GitHub Private Vulnerability Reporting. Include:

- A clear description of the vulnerability
- The affected route, component, or file
- Reproduction steps or a proof of concept
- The potential impact
- Any suggested mitigation, if available

Please avoid including real passwords, access tokens, personal data, or production credentials in a report. Use test accounts and redact sensitive values.

## Security Expectations

Contributors and maintainers must:

- Never commit `.env` files, credentials, tokens, logs, cache files, or uploaded user data.
- Validate user input through Laravel Form Request classes.
- Use policies and middleware for authorization; do not rely on controller-only role checks.
- Keep API responses within the documented `/api/v1` response envelope.
- Avoid exposing stack traces or raw exception messages to API or web users.
- Never log passwords, tokens, or personally identifiable information.
- Keep dependencies current and review Composer changes before committing.

For local setup, use `.env.example` as a template and separate test credentials and databases.
