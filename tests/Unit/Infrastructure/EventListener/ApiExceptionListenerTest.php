<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\EventListener;

use App\Application\Exception\ValidationException;
use App\Infrastructure\EventListener\ApiExceptionListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class ApiExceptionListenerTest extends TestCase
{
    private LoggerInterface $logger;
    private HttpKernelInterface $kernel;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->kernel = $this->createMock(HttpKernelInterface::class);
    }

    public function testIgnoresNonApiRoutes(): void
    {
        $listener = new ApiExceptionListener($this->logger, 'prod');

        $request = new Request();
        $request->server->set('REQUEST_URI', '/non-api/route');

        $exception = new \Exception('Test exception');
        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        // Should not set response for non-API routes
        $listener->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    public function testHandlesApiRoutes(): void
    {
        $listener = new ApiExceptionListener($this->logger, 'prod');

        $request = new Request();
        $request->server->set('REQUEST_URI', '/api/fizzbuzz');

        $exception = new \Exception('Test exception');
        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener->onKernelException($event);

        $this->assertNotNull($event->getResponse());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $event->getResponse()->getStatusCode());
    }

    public function testHandlesValidationException(): void
    {
        $listener = new ApiExceptionListener($this->logger, 'prod');

        $request = new Request();
        $request->server->set('REQUEST_URI', '/api/fizzbuzz');

        $violation = new ConstraintViolation(
            'Field is required',
            null,
            [],
            null,
            'field1',
            null
        );
        $violations = new ConstraintViolationList([$violation]);
        $exception = new ValidationException($violations);

        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $content = \Safe\json_decode((string) $response->getContent(), true);
        $this->assertEquals('error', $content['status']);
        $this->assertEquals('Validation failed', $content['message']);
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('field1', $content['errors']);
    }

    public function testHandlesInvalidArgumentException(): void
    {
        $listener = new ApiExceptionListener($this->logger, 'prod');

        $request = new Request();
        $request->server->set('REQUEST_URI', '/api/fizzbuzz');

        $exception = new \InvalidArgumentException('Invalid input provided');

        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $content = \Safe\json_decode((string) $response->getContent(), true);
        $this->assertEquals('error', $content['status']);
        $this->assertEquals('Invalid input provided', $content['message']);
    }

    public function testHandlesNotFoundHttpException(): void
    {
        $listener = new ApiExceptionListener($this->logger, 'prod');

        $request = new Request();
        $request->server->set('REQUEST_URI', '/api/nonexistent');

        $exception = new NotFoundHttpException('Route not found');

        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $content = \Safe\json_decode((string) $response->getContent(), true);
        $this->assertEquals('error', $content['status']);
        $this->assertEquals('Route not found', $content['message']);
    }

    public function testHandlesMethodNotAllowedHttpException(): void
    {
        $listener = new ApiExceptionListener($this->logger, 'prod');

        $request = new Request();
        $request->server->set('REQUEST_URI', '/api/fizzbuzz');

        $exception = new MethodNotAllowedHttpException(['POST'], 'Method GET not allowed');

        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());

        $content = \Safe\json_decode((string) $response->getContent(), true);
        $this->assertEquals('error', $content['status']);
    }
}
