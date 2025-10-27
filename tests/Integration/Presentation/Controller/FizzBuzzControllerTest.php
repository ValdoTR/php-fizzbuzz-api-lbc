<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class FizzBuzzControllerTest extends WebTestCase
{
    public function testFizzBuzzEndpointSuccess(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \Safe\json_encode([
                'int1' => 3,
                'int2' => 5,
                'limit' => 15,
                'str1' => 'fizz',
                'str2' => 'buzz',
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKey('count', $content);
        $this->assertCount(15, $content['result']);
        $this->assertEquals('fizz', $content['result'][2]);
        $this->assertEquals('buzz', $content['result'][4]);
        $this->assertEquals('fizzbuzz', $content['result'][14]);
    }

    public function testCustomParametersWork(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \Safe\json_encode([
                'int1' => 2,
                'int2' => 3,
                'limit' => 6,
                'str1' => 'foo',
                'str2' => 'bar',
            ])
        );

        $this->assertResponseIsSuccessful();

        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);
        $expected = ['1', 'foo', 'bar', 'foo', '5', 'foobar'];

        $this->assertEquals($expected, $content['result']);
    }

    public function testValidationFailsForMissingParameters(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \Safe\json_encode(['int1' => 3])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $content);
        $this->assertEquals('error', $content['status']);
        $this->assertArrayHasKey('errors', $content);
    }

    public function testValidationFailsForInvalidTypes(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \Safe\json_encode([
                'int1' => 'not-a-number',
                'int2' => 5,
                'limit' => 10,
                'str1' => 'fizz',
                'str2' => 'buzz',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertEquals('error', $content['status']);
    }

    public function testValidationFailsForOutOfRangeValues(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \Safe\json_encode([
                'int1' => 1001,
                'int2' => 5,
                'limit' => 10,
                'str1' => 'fizz',
                'str2' => 'buzz',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testInvalidJsonReturns400(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json{'
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertEquals('error', $content['status']);
        $this->assertStringContainsString('Invalid JSON', $content['message']);
    }

    public function testEmptyBodyReturns400(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testGetMethodNotAllowed(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/fizzbuzz');

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testLargeLimitWorks(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \Safe\json_encode([
                'int1' => 3,
                'int2' => 5,
                'limit' => 100,
                'str1' => 'fizz',
                'str2' => 'buzz',
            ])
        );

        $this->assertResponseIsSuccessful();

        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertCount(100, $content['result']);
    }
}
