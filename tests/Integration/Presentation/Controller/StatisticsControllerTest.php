<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class StatisticsControllerTest extends WebTestCase
{
    private const CACHE_KEY = 'fizzbuzz_statistics';
    private string $statsFile;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        if (isset($this->statsFile) && \file_exists($this->statsFile)) {
            \Safe\unlink($this->statsFile);
        }

        parent::tearDown();
    }

    public function testStatsEndpointReturnsNoContentInitially(): void
    {
        $client = self::createClient();

        // Start with fresh stats
        $this->clearStatistics($client);

        $client->request('GET', '/api/fizzbuzz/stats');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testStatsEndpointReturnsMostFrequent(): void
    {
        $client = self::createClient();

        // Start with fresh stats
        $this->clearStatistics($client);

        // Make same request twice
        $payload = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 100,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        for ($i = 0; $i < 2; ++$i) {
            $client->request(
                'POST',
                '/api/fizzbuzz',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                \Safe\json_encode($payload)
            );

            $this->assertResponseIsSuccessful();
        }

        // Check stats
        $client->request('GET', '/api/fizzbuzz/stats');

        $this->assertResponseIsSuccessful();
        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('parameters', $content);
        $this->assertArrayHasKey('count', $content);
        $this->assertEquals(2, $content['count']);
        $this->assertEquals($payload, $content['parameters']);
    }

    public function testStatsTrackMultipleDifferentRequests(): void
    {
        $client = self::createClient();

        // Start with fresh stats
        $this->clearStatistics($client);

        // Make first request 2 times
        $payload1 = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 50,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        for ($i = 0; $i < 2; ++$i) {
            $client->request(
                'POST',
                '/api/fizzbuzz',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                \Safe\json_encode($payload1)
            );
        }

        // Make second request 1 time
        $payload2 = [
            'int1' => 2,
            'int2' => 7,
            'limit' => 20,
            'str1' => 'foo',
            'str2' => 'bar',
        ];

        $client->request(
            'POST',
            '/api/fizzbuzz',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \Safe\json_encode($payload2)
        );

        // Check stats (should return most frequent: payload1 with 2 hits)
        $client->request('GET', '/api/fizzbuzz/stats');

        $this->assertResponseIsSuccessful();
        $content = \Safe\json_decode((string) $client->getResponse()->getContent(), true);

        $this->assertEquals(2, $content['count']);
        $this->assertEquals($payload1, $content['parameters']);
    }

    /**
     * Clear statistics file and cache
     *
     * @param \Symfony\Bundle\FrameworkBundle\KernelBrowser $client
     */
    private function clearStatistics($client): void
    {
        // Get stats file path from container
        $param = $client->getContainer()->getParameter('statistics_file_path');
        // we know (by design) that 'statistics_file_path' is always a string
        \assert(\is_string($param));
        $this->statsFile = $param;

        // Ensure test directory exists
        $dir = \dirname($this->statsFile);
        if (!\is_dir($dir)) {
            \Safe\mkdir($dir, 0777, true);
        }

        // Delete stats file if exists
        if (\file_exists($this->statsFile)) {
            \Safe\unlink($this->statsFile);
        }

        // Clear cache
        /** @var \Psr\Cache\CacheItemPoolInterface $cache */
        $cache = $client->getContainer()->get('cache.app');
        $cache->deleteItem(self::CACHE_KEY);
    }
}
