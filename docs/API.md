# API Documentation

Complete reference for all FizzBuzz REST API endpoints.

## Accessing the API Documentation

Once the application is running, you can access:

### Swagger UI

```shell
http://localhost:8000/api/doc
```

### OpenAPI JSON Specification

```shell
http://localhost:8000/api/doc.json
```

Raw OpenAPI 3.0 specification in JSON format.

Importing into Postman:

  1. In Postman, click "Import"
  2. Enter the OpenAPI JSON file URL
  3. All endpoints will be imported as a collection

## Endpoint examples

### POST /api/fizzbuzz

Generate a FizzBuzz sequence with custom parameters.

**Request:**

```http
POST /api/fizzbuzz
Content-Type: application/json

{
  "int1": 3,
  "int2": 5,
  "limit": 15,
  "str1": "fizz",
  "str2": "buzz"
}
```

**Parameters:**

| Field | Type | Required | Constraints | Description |
|-------|------|----------|-------------|-------------|
| `int1` | integer | ✅ | 1-1000 | First divisor |
| `int2` | integer | ✅ | 1-1000 | Second divisor |
| `limit` | integer | ✅ | 1-100000 | Upper bound of sequence |
| `str1` | string | ✅ | 1-50 chars | Replacement for int1 multiples |
| `str2` | string | ✅ | 1-50 chars | Replacement for int2 multiples |

**Success Response (200):**

```json
{
  "result": [
    "1", "2", "fizz", "4", "buzz", "fizz", "7", "8",
    "fizz", "buzz", "11", "fizz", "13", "14", "fizzbuzz"
  ],
  "count": 15
}
```

**Error Response (400):**

```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "int1": "must be an integer",
    "limit": "must be between 1 and 100000"
  }
}
```

**Error Response (500):**

```json
{
  "status": "error",
  "message": "Internal server error"
}
```

**Note:** In development mode (`APP_ENV=dev`), error responses include debug information with stack traces.

### GET /api/fizzbuzz/stats

Get statistics about the most frequently requested parameters.

**Request:**

```http
GET /api/fizzbuzz/stats
```

**Success Response (200):**

```json
{
    "parameters": {
        "int1": 3,
        "int2": 5,
        "limit": 100,
        "str1": "fizz",
        "str2": "buzz"
    },
    "count": 42
}
```

**No Statistics Response (204):**

```shell
204 No Content
```
