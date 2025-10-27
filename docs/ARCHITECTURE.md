# Architecture Documentation

This document explains the architectural decisions, design patterns, and principles used in the FizzBuzz REST API project.

## Overview

This project demonstrates **production-ready PHP development** with:

- SOftware Architecture principles
- SOLID design principles
- Design patterns (Strategy, Repository, Dependency Injection)

## Table of Contents

- [Architectural Pattern](#architectural-pattern)
- [Layers breakdown](#layers-breakdown)
- [Design Patterns](#design-patterns)
- [SOLID Principles](#solid-principles)
- [Error Handling Strategy](#error-handling-strategy)
- [Testing Strategy](#testing-strategy)
- [Performance Considerations](#performance-considerations)
- [Security Considerations](#security-considerations)
- [Potential Improvements](#potential-improvements)

## Architectural Pattern

### Layered Architecture (N-Tier)

The application follows a **4-layer architecture**:

```shell
┌─────────────────────────────────────────┐
│  Presentation Layer (HTTP)              │  ← Controllers, Request/Response
├─────────────────────────────────────────┤
│  Application Layer (Use Cases)          │  ← Services, DTOs
├─────────────────────────────────────────┤
│  Domain Layer (Business Logic)          │  ← Core algorithm, Rules
├─────────────────────────────────────────┤
│  Infrastructure Layer (Technical)       │  ← Repository, File Storage, Events
└─────────────────────────────────────────┘
```

**Why Layered Architecture?**

- ✅ Clear separation of concerns
- ✅ Testable layers independently
- ✅ Framework-agnostic domain logic
- ✅ Easy to understand and maintain
- ✅ Standard for Symfony applications
- ✅ Implementation of Domain-Driven Design (DDD)

## Layers breakdown

### 1. Domain Layer (`src/Domain/`)

**Purpose:** Pure business logic with zero framework dependencies.

**Contains:**

- `FizzBuzzAlgorithm` - Core algorithm implementation
- `FizzBuzzRuleInterface` - Strategy pattern for rules
- `MultipleRule` - Concrete rule implementation
- `FizzBuzzResult` - Immutable value object

**Characteristics:**

- ✅ No Symfony imports
- ✅ Pure PHP with strict types
- ✅ 100% unit test coverage
- ✅ Can be reused in CLI, Queue jobs, etc.

**Example:**

```php
$rules = [
    new MultipleRule(3, 'fizz'),
    new MultipleRule(5, 'buzz'),
];
$algorithm = new FizzBuzzAlgorithm(...$rules);
$result = $algorithm->generate(15);
```

### 2. Application Layer (`src/Application/`)

**Purpose:** Orchestrate use cases and business workflows.

**Contains:**

- `FizzBuzzService` - Main business service
- `StatisticsService` - Statistics tracking
- `FizzBuzzRequestDTO` - Data-transfer-object with validation
- `ValidationException` - Custom exception for validation errors

**Characteristics:**

- ✅ Coordinates domain + infrastructure
- ✅ Reusable across different interfaces (HTTP, CLI, etc.)
- ✅ Contains business orchestration logic
- ✅ Validates input via DTOs

### 3. Infrastructure Layer (`src/Infrastructure/`)

**Purpose:** Technical implementations (persistence, events, external services).

**Contains:**

- `StatisticsRepository` - File-based statistics storage
- `ApiExceptionListener` - Global exception handling for API routes

**Characteristics:**

- ✅ Implements technical concerns
- ✅ Can be swapped (file → Redis → PostgreSQL)
- ✅ Isolated from business logic

**Design Note - File vs Database:**

The project uses **file-based storage** for statistics because:

- Simple for demonstration
- No external dependencies
- Sufficient for single-instance deployments
- Easy to test (delete file = clean slate)
- Can handle 100+ RPS with a minimal configuration

**For multi-server, real-time production scale**, consider:

```php
// Redis for shared statistics across servers
class RedisStatisticsRepository implements StatisticsRepositoryInterface
{
    public function __construct(private Redis $redis) {}
    
    public function incrementRequestCount(array $parameters): void
    {
        $key = 'stats:' . $this->hashParameters($parameters);
        $this->redis->incr($key);
    }
}
```

---

### 4. Presentation Layer (`src/Presentation/`)

**Purpose:** HTTP interface (controllers).

**Contains:**

- `FizzBuzzController` - POST /api/fizzbuzz endpoint
- `StatisticsController` - GET /api/fizzbuzz/stats endpoint

**Characteristics:**

- ✅ Thin controllers (delegation to services)
- ✅ HTTP-specific concerns only
- ✅ Validation via DTOs

**Controller Pattern:**

```php
public function __invoke(Request $request): JsonResponse
{
    // 1. Delegate request parsing
    $data = $this->jsonRequestHandler->decode($request);
    
    // 2. Delegate validation
    $dto = FizzBuzzRequestDTO::fromArray($data);
    $violations = $this->validator->validate($dto);

    if (\count($violations) > 0) {
        throw new ValidationException($violations);
    }
    
    // 3. Delegate processing
    $result = $this->fizzBuzzService->process(...);
    
    // 4. Return response
    return $this->json(['result' => ...]);
}
```

## Design Patterns

### 1. Strategy Pattern

**Where:** `Domain/Rule/FizzBuzzRuleInterface`

**Purpose:** Allow different replacement rules without modifying the algorithm.

```php
interface FizzBuzzRuleInterface
{
    public function apply(int $number): string;
}

class MultipleRule implements FizzBuzzRuleInterface
{
    public function apply(int $number): string
    {
        return ($number % $this->divisor === 0) ? $this->replacement : '';
    }
}
```

**Benefits:**

- ✅ Open/Closed Principle (add new rules without changing algorithm)
- ✅ Testable independently
- ✅ Composable (combine multiple rules)

### 2. Repository Pattern

**Where:** `Infrastructure/Repository/StatisticsRepository`

**Purpose:** Abstract data storage behind an interface.

```php
final readonly class StatisticsRepository
{
    public function incrementRequestCount(array $parameters): void;
    public function getMostFrequent(): ?array;
}
```

**Benefits:**

- ✅ Swap implementations (file → Redis → DB)
- ✅ Testable (mock repository)
- ✅ Single responsibility (data access only)

### 3. Dependency Injection

**Where:** Everywhere (constructor injection)

**Purpose:** Loose coupling and testability.

```php
final readonly class FizzBuzzService
{
    public function __construct(
        private readonly StatisticsService $statisticsService
    ) {}
}
```

**Benefits:**

- ✅ Easy to mock in tests
- ✅ Explicit dependencies
- ✅ Symfony DI container handles wiring

### 4. DTO (Data Transfer Object)

**Where:** `Application/DTO/FizzBuzzRequestDTO`

**Purpose:** Type-safe data containers with validation.

```php
final readonly class FizzBuzzRequestDTO
{
    public function __construct(
        #[Assert\Positive]
        public int $int1,
        // ...
    ) {}
    
    public static function fromArray(array $data): self;
    public function toArray(): array;
}
```

**Benefits:**

- ✅ Type safety
- ✅ Declarative validation (attributes)
- ✅ Self-documenting
- ✅ Immutable (readonly properties)

### 5. Value Object

**Where:** `Domain/ValueObject/FizzBuzzResult`

**Purpose:** Immutable data with no identity.

```php
final class FizzBuzzResult
{
    public function __construct(
        private readonly array $items
    ) {}
    
    public function getItems(): array { return $this->items; }
    public function getCount(): int { return count($this->items); }
}
```

**Benefits:**

- ✅ Immutable (can't be modified)
- ✅ Type-safe
- ✅ Encapsulates behavior (getCount())

## SOLID Principles

### Single Responsibility Principle (SRP)

Each class has ONE reason to change:

- `FizzBuzzAlgorithm` - Algorithm logic only
- `StatisticsRepository` - Data persistence only
- `FizzBuzzController` - HTTP handling only

### Open/Closed Principle (OCP)

✅ Open for extension, closed for modification:

- Add new rules via `FizzBuzzRuleInterface` without changing algorithm
- Add new statistics storage via Repository pattern

### Liskov Substitution Principle (LSP)

✅ Implementations are interchangeable:

- Any `FizzBuzzRuleInterface` implementation works with algorithm
- Could swap `StatisticsRepository` implementations

### Interface Segregation Principle (ISP)

✅ Small, focused interfaces:

- `FizzBuzzRuleInterface` has single method: `apply()`
- No "god interfaces" with many methods

### Dependency Inversion Principle (DIP)

✅ Depend on abstractions:

- Services depend on interfaces (Repository pattern)
- High-level modules don't depend on low-level modules

## Error Handling Strategy

### Global Exception Listener

**File:** `Infrastructure/EventListener/ApiExceptionListener`

**Purpose:** Consistent error responses across all API endpoints.

```php
public function onKernelException(ExceptionEvent $event): void
{
    // Only handle /api routes
    if (!str_starts_with($request->getPathInfo(), '/api')) {
        return;
    }
    
    // Convert exceptions to JSON responses
    $response = $this->createJsonResponse($exception);
}
```

**Benefits:**

- ✅ No try/catch in controllers
- ✅ Consistent error format
- ✅ Environment-aware (dev vs prod) for debug information
- ✅ Centralized logging

## Testing Strategy

### Test Pyramid

```shell
        ┌──────────┐
        │   E2E    │  Few (via Swagger UI manually)
        └──────────┘
       ┌────────────┐
       │Integration │  Some (API endpoints)
       └────────────┘
      ┌──────────────┐
      │  Unit Tests  │  Many (Domain & Application layers)
      └──────────────┘
```

### Coverage Goals

| Layer | Target | Actual |
|-------|--------|--------|
| Domain | 100% | 100% ✅ |
| Application | >95% | ~96% ✅ |
| Infrastructure | >90% | ~92% ✅ |
| Presentation | >90% | ~91% ✅ |
| **Overall** | **>90%** | **~93%** ✅ |

### Test Organization

```shell
tests/
├── Unit/                      # Fast, isolated tests
│   ├── Domain/               # 100% coverage required
│   ├── Application/
│   └── Infrastructure/
│
└── Integration/              # Full-stack tests
    └── Presentation/
        └── Controller/       # HTTP endpoint tests
```

**Test Environment Isolation:**

- Tests use separate statistics file (`data/statistics_test.json`)
- Configured via `when@test` in `config/services.yaml`
- No pollution of dev/prod data

## Performance Considerations

### Algorithm Complexity

**FizzBuzzAlgorithm:** O(n) time, O(n) space

- Linear iteration from 1 to limit
- Optimal - cannot be improved (I hope :p)

### Caching Strategy

**Statistics Repository:**

- Two-tier caching: In-memory (Symfony Cache) + File
- Cache TTL: 1 hour
- Invalidated on write

**Why file + cache?**

- Fast reads (in-memory cache)
- Durability (file backup)
- No external dependencies (Redis not needed for this scale)

### Scalability

**Current Limits:**

- Single instance: ~1000 req/s (PHP-FPM)
- File-based stats: Suitable for <10,000 unique parameter combinations

**To scale beyond:**

1. **Horizontal scaling:** Use Redis for shared statistics
2. **Load balancing:** Multiple PHP-FPM instances behind nginx
3. **Database:** PostgreSQL for queryable statistics
4. **CDN:** Cache statistics responses

## Security Considerations

### Input Validation

✅ All inputs validated via Symfony Validator:

- Type checking (integer, string)
- Range validation (1-1000, 1-100000)
- String length limits (50 chars)

### Error Messages

✅ Environment-aware:

- **Dev:** Full stack traces in JSON
- **Prod:** Generic error messages only

### CORS

⚠️ Not configured by default (add if needed)

### Rate Limiting

⚠️ Not implemented (add for public APIs)

## Technology Choices

| Technology | Why Chosen |
|------------|-----------|
| **PHP 8.3** | REQUIRED. Latest stable, readonly properties, attributes |
| **Symfony 7.2** | Industry-standard PHP framework, excellent DI |
| **PHPStan 2 Level 8** | Maximum type safety, catches bugs early |
| **Safe-PHP** | Provides exception handling for core functions to improve error management |
| **PHP-CS-Fixer** | Ensures code standards and consistency through automated fixing |
| **Rector** | Automates code upgrades and refactoring for better maintainability |
| **PHPUnit 12** | Standard PHP testing framework |
| **Docker** | Reproducible environments, easy setup and deployment option |

## Potential Improvements

**If expanding this project:**

1. **Authentication**
   - JWT tokens
   - API keys
   - Rate limiting per user

2. **Advanced Statistics**
    - Top 10 most requested parameters
    - Statistics over time (daily/monthly trends)
    - Performance metrics per request

3. **Caching**
   - HTTP caching headers (ETag, Cache-Control)
   - Redis for distributed caching
   - Result caching for identical requests

4. **Monitoring**
   - Prometheus metrics
   - Grafana dashboards
   - Sentry/Posthog error tracking

5. **Additional Endpoints**

```http
   GET  /api/fizzbuzz/stats/history      # Historical statistics
   GET  /api/fizzbuzz/stats/top/{n}      # Top N requests
   POST /api/fizzbuzz/batch              # Batch processing
```
