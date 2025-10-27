<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ApiExceptionListenerTest extends WebTestCase
{
    public function testValidationExceptionReturnsConsistentFormat(): void
    {
        $client = self::createClient();

        // Trigger validation exception
        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \Safe\json_encode(['int1' => 'invalid'])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);

        // Check consistent error format
        $this->assertArrayHasKey('status', $content);
        $this->assertEquals('error', $content['status']);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('errors', $content);
    }

    public function testNotFoundReturnsJsonForApiRoutes(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/nonexistent');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $content = \Safe\json_decode((string) $response->getContent(), true);
        $this->assertArrayHasKey('status', $content);
        $this->assertEquals('error', $content['status']);
    }

    public function testMethodNotAllowedReturnsJson(): void
    {
        $client = self::createClient();

        $client->request('PUT', '/api/fizzbuzz');

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);

        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $content);
        $this->assertEquals('error', $content['status']);
    }

    public function testNonApiRoutesAreNotAffected(): void
    {
        $client = self::createClient();

        // Request non-API route (should get HTML 404, not JSON)
        $client->request('GET', '/nonexistent');

        // This will be handled by Symfony's default exception handler
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
