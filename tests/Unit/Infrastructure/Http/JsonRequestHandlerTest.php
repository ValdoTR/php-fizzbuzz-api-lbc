<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Http;

use App\Infrastructure\Http\JsonRequestHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class JsonRequestHandlerTest extends TestCase
{
    private JsonRequestHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new JsonRequestHandler();
    }

    public function testDecodeValidJson(): void
    {
        $request = new Request(
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{"name":"Valdo","age":33}'
        );

        $data = $this->handler->decode($request);

        self::assertSame(['name' => 'Valdo', 'age' => 33], $data);
    }

    public function testThrowsOnNonJsonContentType(): void
    {
        $request = new Request(
            server: ['CONTENT_TYPE' => 'text/plain'],
            content: '{"name":"Valdo"}'
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Content-Type must be application/json.');

        $this->handler->decode($request);
    }

    public function testThrowsOnEmptyBody(): void
    {
        $request = new Request(
            server: ['CONTENT_TYPE' => 'application/json'],
            content: ''
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Empty request body.');

        $this->handler->decode($request);
    }

    public function testThrowsOnInvalidJson(): void
    {
        $request = new Request(
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{"invalid_json": "missing_end"'
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Invalid JSON:');

        $this->handler->decode($request);
    }

    public function testThrowsWhenJsonIsNotAnObject(): void
    {
        $request = new Request(
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '"a simple string"'
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('JSON must represent an object.');

        $this->handler->decode($request);
    }
}
