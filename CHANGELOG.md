# Changelog

## Unreleased

### Added

- Admin-only movie mutations with functional authorization coverage
- CSRF-protected POST deletion
- Isolated image storage and Elasticsearch search services
- PHPUnit unit/functional suite, PHPStan level 8, PHP CS Fixer, and CI
- Reproducible MySQL/Elasticsearch Docker services and deterministic fixtures

### Changed

- Upgraded the application to PHP 8.2 and Symfony 7.4 LTS
- Made MySQL the explicit canonical data store and Elasticsearch a derived index
- Replaced controller-level lookups and infrastructure logic with typed boundaries
- Modernized the local frontend build and removed runtime CDN dependencies

### Fixed

- Prevented GET requests from deleting movies
- Added upload MIME/size validation and generic user-facing storage errors
- Wired authentication to ORM users with modern password hashing and login CSRF

### Security

- Removed committed secret values from the default environment configuration
- Added mutation authorization and per-resource CSRF validation
- Removed unsafe rendering of search suggestions as HTML

### Removed

- Tracked `vendor` and `.DS_Store` artifacts
- Unjustified MongoDB/ODM user persistence
- Unused Redis/Predis, Doctrine annotations/cache, and dead suggestion code
