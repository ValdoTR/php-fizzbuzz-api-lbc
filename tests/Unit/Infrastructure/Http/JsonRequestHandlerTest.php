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
            content: '{"name":"Valdo","age":33}',
            server: ['CONTENT_TYPE' => 'application/json']
        );

        $data = $this->handler->decode($request);

        self::assertSame(['name' => 'Valdo', 'age' => 33], $data);
    }

    public function testThrowsOnNonJsonContentType(): void
    {
        $request = new Request(
            content: '{"name":"Valdo"}',
            server: ['CONTENT_TYPE' => 'text/plain']
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Content-Type must be application/json.');

        $this->handler->decode($request);
    }

    public function testThrowsOnEmptyBody(): void
    {
        $request = new Request(
            content: '',
            server: ['CONTENT_TYPE' => 'application/json']
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Empty request body.');

        $this->handler->decode($request);
    }

    public function testThrowsOnInvalidJson(): void
    {
        $request = new Request(
            content: '{"invalid_json": "missing_end"',
            server: ['CONTENT_TYPE' => 'application/json']
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Invalid JSON:');

        $this->handler->decode($request);
    }

    public function testThrowsWhenJsonIsNotAnObject(): void
    {
        $request = new Request(
            content: '"a simple string"',
            server: ['CONTENT_TYPE' => 'application/json']
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('JSON must represent an object.');

        $this->handler->decode($request);
    }
}
