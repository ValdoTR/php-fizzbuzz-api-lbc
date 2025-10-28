# Development Documentation

Complete guide for setting up, developing, and maintaining the FizzBuzz REST API.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Pre-commit Checklist](#pre-commit-checklist)
- [Installation](#installation)
- [Development Workflow](#development-workflow)
- [Debugging](#debugging)
- [Good practices](#good-practices)
- [Continuous Integration](#continuous-integration)
- [Resources](#resources)

## Prerequisites

**Choose a way of booting the app:**

- **Option A:** [Docker](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/)
- **Option B:** [PHP](https://www.php.net/downloads.php) 8.2+, [Composer](https://getcomposer.org/download/) 2+ and [Symfony CLI](https://symfony.com/download)

If you choose option B, check [Symfony's technical requirements](https://symfony.com/doc/current/setup.html#technical-requirements).

## Pre-commit Checklist

Even if the project CI handles automated tests, it's always a good practice to run them locally before commiting code.

You should check that:

- [ ] PHPUnit passes
- [ ] PHPStan passes
- [ ] PHP-CS-Fixer passes
- [ ] Rector passes
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

# Run all tests with coverage
composer test:coverage

# Run all tests with coverage HTML report
composer test:coverage:html
# and open in browser
open var/coverage/index.html  # macOS
xdg-open var/coverage/index.html  # Linux

# Specific test file
vendor/bin/phpunit tests/Unit/Domain/FizzBuzzAlgorithmTest.php

# Specific test method
vendor/bin/phpunit --filter testClassicFizzBuzz
```

### 3. Check Code Quality

```bash
# Static analysis fix (PHPStan level 8)
composer code:analyse

# Analyze specific file
vendor/bin/phpstan analyse src/Domain/FizzBuzzAlgorithm.php

# Fix code style automatically
composer code:lint

# Check code style
composer code:lint:check

# Fix specific file
vendor/bin/php-cs-fixer fix src/Domain/FizzBuzzAlgorithm.php

# Refactor automatically
composer code:refactor

# Check refactoring
composer code:refactor:check

# Apply Safe-PHP Refactoring only
composer code:refactor:safe
```

### 4. View API Documentation

Open `http://localhost:8000/api/doc`.

> See the full [API Documentation](../docs/API.md).

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

### View Debug Information

In `dev` environment, exceptions show full stack traces in JSON responses.

### Database Statistics File

```bash
# View current statistics
cat data/statistics.json | jq .

# Clear statistics
rm data/statistics.json
```

### Symfony Profiler

You can access the built-in Symfony profiler at: <http://localhost:8000/_profiler>

Perform an HHTP request a reload the page, you should be able to check important information about it.

> With Xdebug enable you have much more detailed information! See next section.

### Xdebug

You need to [install Xdebug](https://xdebug.org/docs/install) in order to run tests with coverage, inspect variables during runtime, set breakpoints and perform step debugging.

For instance, on Ubuntu, run `sudo apt-get install php8.3-xdebug`.

Verify installation by running `php -v`. It should show: "with Xdebug v3.3.0".

To configure it you have to edit your *php.ini*.

Locate it: `php -i | grep php.ini` and add the following configuration:

```ini
[xdebug]
zend_extension=xdebug.so
;With mode=off, Xdebug is only loaded, but does nothing by default.
xdebug.mode=off
xdebug.start_with_request=trigger
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

## Continuous Integration

This project uses GitHub Actions to ensure code quality.

The CI workflow runs on:

- **Events**: Push to `main`, Pull Requests on `main`
- **Operating Systems**: Ubuntu (latest), macOS (latest)
- **PHP Versions**: 8.3, 8.4
- **Matrix**: 4 combinations (2 OS × 2 PHP versions)

### What Gets Tested

**1. Dependency Validation:**
    - Validates composer.json structure
    - Checks for security vulnerabilities
    - Verifies lock file is up to date

**2. Test Suite:**
    - Unit tests
    - Integration tests
    - Code coverage reporting (with CodeCov badge)

**3. Static Analysis:**
    - PHPStan level 8
    - Type safety verification
    - Dead code detection

**4. Code Style:**
    - PSR-12 compliance
    - Symfony conventions
    - PHP-CS-Fixer rules

## Resources

- [Symfony Documentation](https://symfony.com/doc)
- [PHP The Right Way](https://phptherightway.com/)
- [PHP-FIG](https://www.php-fig.org/)
- [PHPStan Documentation](https://phpstan.org/)
- [OpenAPI Specification](https://swagger.io/specification/)
- [Gitlab's version-control best practices](https://about.gitlab.com/topics/version-control/version-control-best-practices/)
