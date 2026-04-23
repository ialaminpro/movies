# Contributing

## Setup

Use PHP 8.2+, Composer 2, Node.js 22.18+, and Docker Compose v2. Run `make setup` for a complete local environment.

## Quality checks

Before opening a pull request, run:

```bash
composer validate --strict
composer audit
composer check
npm audit
npm run build
php bin/console lint:container
php bin/console lint:twig templates
php bin/console lint:yaml config
```

Add tests at the narrowest useful layer. Unit tests must not require infrastructure. Functional tests should use the isolated test database and replace external providers where practical.

## Pull requests

- Keep changes focused and explain the engineering tradeoff.
- Add an additive migration for schema changes; do not edit released migration history.
- Never commit secrets, `vendor`, `node_modules`, generated assets, or local environment files.
- Preserve POST/CSRF requirements and authorization for state-changing routes.
- Update architecture documentation when an infrastructure responsibility changes.
