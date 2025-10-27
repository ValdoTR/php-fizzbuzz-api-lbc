# Development Documentation

Complete guide for setting up, developing, and maintaining the FizzBuzz REST API.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Pre-commit Checklist](#pre-commit-checklist)
- [Installation](#installation)
- [Development Workflow](#development-workflow)
- [Debugging](#debugging)
- [Good practices](#good-practices)
- [Resources](#resources)

## Prerequisites

**Choose a way of booting the app:**

- **Option A:** [Docker](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/)
- **Option B:** [PHP](https://www.php.net/downloads.php) 8.2+, [Composer](https://getcomposer.org/download/) 2+ and [Symfony CLI](https://symfony.com/download)

If you choose option B, check [Symfony's technical requirements](https://symfony.com/doc/current/setup.html#technical-requirements).

## Pre-commit Checklist

Before committing code:

- [ ] All tests pass
- [ ] PHPStan passes
- [ ] Code style fixed
- [ ] New tests added for new features
- [ ] Documentation updated if needed

## Installation

```bash
git clone https://github.com/ValdoTR/php-fizzbuzz-api-lbc
cd php-fizzbuzz-api-lbc

# Using Docker
docker-compose up -d

# Using Symfony CLI
composer install
symfony server:start -d
```

## Development Workflow

### 1. Make Changes

Edit files in `src/` `config/` or `tests/`.

### 2. Run Tests

```bash
# Run all tests
composer test

# Unit tests only
composer test:unit

# Integration tests only
composer test:int

# Specific test file
vendor/bin/phpunit tests/Unit/Domain/FizzBuzzAlgorithmTest.php

# Specific test method
vendor/bin/phpunit --filter testClassicFizzBuzz
```

### 3. Check Code Quality

```bash
# Static analysis (PHPStan level 8)
composer code:analyse

# Fix code style automatically
composer code:lint

# Check code style
composer code:lint:check

# Refactor automatically
composer code:refactor

# Check refactoring
composer code:refactor:check

# Apply Safe-PHP Refactoring refactoring
composer code:refactor:safe
```

### 4. View API Documentation

Open `http://localhost:8000/api/doc`.

> See the full [API Documentation](docs/API.md).

### 5. View Logs

```bash
# Application logs
docker-compose logs -f php

# Nginx access logs
docker-compose logs -f nginx

# Using Symfony CLI
symfony server:log
```

### 6. Stop the Application

```bash
# Using Docker
docker-compose down

# Using Symfony CLI
symfony server:stop
```

## Debugging

### Enable Symfony Profiler (dev only)

Access at:

```shell
http://localhost:8000/_profiler
```

### View Debug Information

In `dev` environment, exceptions show full stack traces in JSON responses.

### Database Statistics File

```bash
# View current statistics
cat data/statistics.json | jq .

# Clear statistics
rm data/statistics.json
```

## Good practices

### Naming Conventions

- **Classes**: `PascalCase`, suffix interfaces with `Interface`
- **Methods**: `camelCase`, verb-based
- **Variables**: `camelCase`, descriptive
- **Constants**: `UPPER_SNAKE_CASE`

### Documentation

Document your decisions about patterns:

- Programming Paradigms
- Architectural Patterns
- Design Patterns
- Presentation Patterns

Document your classes, methods and variables **only if needed**.

### Type Hints

Always use type hints:

```php
public function process(int $number): string
```

### Readonly Properties

Use `readonly` for immutable data:

```php
public function __construct(
    private readonly MyService $service
) {}
```

### Strict Types

Every file must start with:

```php
<?php
declare(strict_types=1);
```

### Final Classes

Mark classes `final` unless designed for extension:

```php
final class MyService
```

## Resources

- [Symfony Documentation](https://symfony.com/doc)
- [PHP The Right Way](https://phptherightway.com/)
- [PHP-FIG](https://www.php-fig.org/)
- [PHPStan Documentation](https://phpstan.org/)
- [OpenAPI Specification](https://swagger.io/specification/)
- [Gitlab's version-control best practices](https://about.gitlab.com/topics/version-control/version-control-best-practices/)
