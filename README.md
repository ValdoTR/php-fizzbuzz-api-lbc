# FizzBuzz REST API

A production-ready REST API implementing a generalized FizzBuzz algorithm with request statistics tracking.

## Project Overview

**🎯 Key Features:**

- **Customizable FizzBuzz**: Generate sequences with any divisors and replacement strings
- **Statistics Tracking**: Tracks the most frequently requested parameters

**🛠️ Technology Stack:**

- **PHP 8.2+** with strict types
- **Symfony 7.3** framework
- **Docker** for containerization

**🎓 Code quality:**

- **Layered Architecture** - Domain-driven design with clear layers separation
- **SOLID Principles** - Extensible and maintainable codebase
- **Design Patterns** - Strategy, Repository, Dependency Injection
- **Type Safety** - PHPStan level 8, DTOs and strict types everywhere
- **Test Coverage** - >90% coverage with unit and integration tests
- **OpenAPI Documentation** - Interactive Swagger UI
- **Docker Ready** - One-command setup with Docker Compose
- **PSR-12 Compliant** - Industry-standard code style

## 🚀 Quick Start

### Prerequisites

> See the full [Development Documentation](docs/DEVELOPMENT.md).

Docker and Docker Compose, or PHP, Composer and Symfony CLI.

### Running the Application

1. Clone the repository:

```bash
git clone https://github.com/ValdoTR/php-fizzbuzz-api-lbc
cd php-fizzbuzz-api-lbc
```

2. Start the application:

```bash
docker-compose up -d
```

Available at <http://localhost:8000>

3. Test the API:

> See the full [API Documentation](docs/API.md).

Interactive Documentation (Swagger UI) at <http://localhost:8000/api/doc>

Quick test (cURL)

```bash
curl -X POST http://localhost:8000/api/fizzbuzz \
  -H "Content-Type: application/json" \
  -d '{"int1":3,"int2":5,"limit":15,"str1":"fizz","str2":"buzz"}'
```

4. Stop the application:

```bash
docker-compose down
```

### Running Tests and code checks

```bash
# PHPUnit unit and integration tests
composer test

# Static analysis (PHPStan level 8)
composer code:analyse

# Fix code style automatically
composer code:lint

# Refactoring
composer code:refactor
```

## 🏗️ Architecture

> See the full [Architecture Documentation](docs/ARCHITECTURE.md).

This project follows a Domain-Driven Design (DDD), a layered architecture.
This separation of concerns helps to maintain the modularity and scalability of the system.

📁 Project Structure:

```shell
├── src/
│   ├── Domain/              # Core business logic and rules
│   ├── Application/         # Business logic orchestration
│   ├── Infrastructure/      # Data persistence & external services
│   └── Presentation/        # API endpoints
├── tests/
│   ├── Unit/               # Unit tests
│   └── Integration/        # Integration tests
├── docker/                 # Docker configuration
└── docs/                   # Documentation
```
