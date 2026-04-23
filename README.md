# Movies

A production-style Symfony portfolio application for managing and searching a movie catalogue. The project focuses on secure CRUD workflows, explicit infrastructure responsibilities, testable boundaries, and a reproducible development environment.

## Features

- Public movie listing, details, and Elasticsearch-backed suggestions
- ORM-backed registration and login with modern password hashing
- Admin-only movie creation, editing, and deletion
- CSRF-protected state changes and validated image uploads
- Deterministic demo users, movies, actors, and relationships
- Unit and functional tests that run without external services
- PHPStan level 8, PHP CS Fixer, Composer/npm audits, and GitHub Actions CI

## Architecture

```text
Browser
   │
   ▼
Symfony controllers ─────────────── Twig
   │
   ├── Doctrine repositories ───── MySQL 8.4
   │                                source of truth
   │
   ├── MovieImageStorage ───────── local public uploads
   │
   └── MovieSearch interface ───── Elasticsearch 9
                                    derived search index
```

Controllers handle HTTP concerns, forms, and authorization. Doctrine entities and repositories own canonical transactional data. `MovieImageStorage` contains filesystem behavior. `MovieSearch` prevents Elastica types and failures from leaking through the application.

## Technology choices

- **PHP 8.2 and Symfony 7.4 LTS** provide a maintained runtime through November 2029.
- **Doctrine ORM and MySQL** own users, movies, actors, and relationships.
- **Elasticsearch** is a rebuildable read model used only for movie search.
- **Twig, Webpack Encore, and Tailwind CSS** provide a server-rendered frontend with a reproducible local build.
- **Docker Compose** runs only infrastructure: MySQL and Elasticsearch.

## Engineering decisions

- **One source of truth:** MySQL owns all business data. Elasticsearch can be dropped and rebuilt with `fos:elastica:populate`.
- **MongoDB removed:** the former MongoDB user document duplicated ORM user data, included another password store, and had no document-specific workload.
- **Redis removed:** no cache, session, queue, or rate-limiting feature used it.
- **Role-based authorization:** the current domain has no movie ownership, so `ROLE_ADMIN` is clearer than an artificial voter.
- **POST plus CSRF for deletion:** safe HTTP methods never mutate state, and every deletion requires a per-resource token.
- **Uploads behind a service:** UUID filenames, storage errors, and cleanup policy are independently testable and do not clutter controllers.
- **Infrastructure-light tests:** SQLite and a disabled Elasticsearch listener keep the default suite fast; the search adapter is tested behind its interface.
- **Synchronous indexing retained:** the catalogue does not yet justify Messenger or eventual-consistency operational overhead. Async indexing is a documented future option if indexing volume grows.

## Requirements

- PHP 8.2+
- Composer 2
- Node.js 22.18+
- Docker with Docker Compose v2

## Quick start

```bash
git clone https://github.com/ialaminpro/movies.git
cd movies
cp .env .env.local
make setup
symfony server:start
```

`make setup` starts MySQL and Elasticsearch, installs backend/frontend dependencies, builds assets, runs migrations, loads fixtures, and builds the search index. Keep real credentials and `APP_SECRET` overrides in `.env.local`; that file is ignored by Git.

Open `http://127.0.0.1:8000`.

### Demo accounts

| Role | Email | Password |
|---|---|---|
| Administrator | `admin@example.com` | `ChangeMe1234!` |
| Viewer | `viewer@example.com` | `ChangeMe1234!` |

These credentials are deterministic local demo data and must never be used in a deployed environment.

## Manual setup

```bash
docker compose up -d --wait
composer install
npm ci
npm run build
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
php bin/console fos:elastica:populate
symfony server:start
```

Infrastructure endpoints:

- MySQL: `127.0.0.1:3306`
- Elasticsearch: `http://127.0.0.1:9200`

## Development commands

```bash
make test       # PHPUnit
make analyse    # PHPStan level 8
make format     # Apply PHP CS Fixer
make check      # Backend checks, npm audit, production asset build
make fixtures   # Reload demo data and rebuild search
make stop       # Stop infrastructure
```

The test suite is organized into `tests/Unit` and `tests/Functional`. Database-backed functional tests use an isolated SQLite schema; Elasticsearch behavior is replaced through `MovieSearch` except in the adapter unit tests.

## CI

GitHub Actions runs two focused jobs:

- PHP: strict Composer validation, dependency audit, style, PHPStan level 8, PHPUnit, and Symfony container/Twig/YAML linting
- Frontend: clean npm install, dependency audit, and production Webpack build

Dependabot checks Composer, npm, and GitHub Actions weekly.

## Project structure

```text
src/Controller/       HTTP orchestration
src/Entity/           canonical Doctrine entities
src/Repository/       persistence queries
src/Search/           search contract, result DTO, Elasticsearch adapter
src/Storage/          movie image storage
src/Form/             forms and upload validation
src/Security/         login authenticator
tests/Unit/           isolated domain/infrastructure tests
tests/Functional/     HTTP, security, forms, and CRUD tests
migrations/           additive database history
```

## Security

See [SECURITY.md](SECURITY.md). Do not commit production secrets; use environment variables, Symfony secrets, or an ignored `.env.local` file.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for setup, quality checks, and pull-request expectations.

## Roadmap

- Add a dedicated Elasticsearch integration job if search mappings become more complex.
- Evaluate object storage when deployments require multiple application instances.
- Consider asynchronous indexing only when measured indexing latency or volume justifies Messenger.
