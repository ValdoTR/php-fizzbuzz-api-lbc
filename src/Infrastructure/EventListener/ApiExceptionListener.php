<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use App\Application\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Global exception listener for API routes
 *
 * Converts all exceptions to consistent JSON responses.
 * Only handles /api routes.
 */
final readonly class ApiExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private string $environment
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        // Only handle API routes
        if (!\str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $exception = $event->getThrowable();

        // Log exceptions (except validation errors which are expected)
        if (!$exception instanceof ValidationException) {
            $this->logger->error('API exception occurred', [
                'exception' => \get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        $response = $this->createJsonResponse($exception);
        $event->setResponse($response);
    }

    private function createJsonResponse(\Throwable $exception): JsonResponse
    {
        // Validation exceptions (400)
        if ($exception instanceof ValidationException) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $exception->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        // Invalid argument (400)
        if ($exception instanceof \InvalidArgumentException) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        // HTTP exceptions (404, 405, etc.)
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();

            return new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage() ?: $this->getDefaultMessage($statusCode),
            ], $statusCode);
        }

        // Generic exceptions (500)
        return $this->createInternalServerErrorResponse($exception);
    }

    private function createInternalServerErrorResponse(\Throwable $exception): JsonResponse
    {
        $data = [
            'status' => 'error',
            'message' => 'Internal server error',
        ];

        // In dev environment, include debug information
        if ('dev' === $this->environment) {
            $data['debug'] = [
                'exception' => \get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => \explode("\n", $exception->getTraceAsString()),
            ];
        }

        return new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function getDefaultMessage(int $statusCode): string
    {
        return match ($statusCode) {
            404 => 'Resource not found',
            405 => 'Method not allowed',
            default => 'An error occurred'
        };
    }
}
