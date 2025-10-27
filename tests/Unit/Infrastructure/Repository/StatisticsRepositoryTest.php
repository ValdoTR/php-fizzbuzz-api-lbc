<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Repository;

use App\Infrastructure\Repository\StatisticsRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class StatisticsRepositoryTest extends TestCase
{
    private StatisticsRepository $repository;
    private string $statsFile;

    protected function setUp(): void
    {
        $this->statsFile = \sys_get_temp_dir().'/test_statistics_'.\uniqid().'.json';
        $cache = new ArrayAdapter();
        $this->repository = new StatisticsRepository($cache, $this->statsFile);
    }

    protected function tearDown(): void
    {
        if (\file_exists($this->statsFile)) {
            \Safe\unlink($this->statsFile);
        }
    }

    public function testIncrementRequestCountCreatesNewEntry(): void
    {
        $parameters = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        $this->repository->incrementRequestCount($parameters);

        $stats = $this->repository->getMostFrequent();

        $this->assertNotNull($stats);
        $this->assertEquals(1, $stats['count']);
        $this->assertEquals($parameters, $stats['parameters']);
    }

    public function testIncrementRequestCountIncrementsExistingEntry(): void
    {
        $parameters = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        for ($i = 0; $i < 3; ++$i) {
            $this->repository->incrementRequestCount($parameters);
        }

        $stats = $this->repository->getMostFrequent();

        $this->assertNotNull($stats);
        $this->assertEquals(3, $stats['count']);
    }

    public function testGetMostFrequentReturnsNullWhenEmpty(): void
    {
        $stats = $this->repository->getMostFrequent();

        $this->assertNull($stats);
    }

    public function testGetMostFrequentReturnsHighestCount(): void
    {
        $params1 = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        $params2 = [
            'int1' => 2,
            'int2' => 7,
            'limit' => 20,
            'str1' => 'foo',
            'str2' => 'bar',
        ];

        // Add params1 4 times
        for ($i = 0; $i < 4; ++$i) {
            $this->repository->incrementRequestCount($params1);
        }

        // Add params2 2 times
        for ($i = 0; $i < 2; ++$i) {
            $this->repository->incrementRequestCount($params2);
        }

        $stats = $this->repository->getMostFrequent();

        $this->assertNotNull($stats);
        $this->assertEquals(4, $stats['count']);
        $this->assertEquals($params1, $stats['parameters']);
    }

    public function testPersistsToFile(): void
    {
        $parameters = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        $this->repository->incrementRequestCount($parameters);

        // Verify file was created
        $this->assertFileExists($this->statsFile);

        // Verify file content
        $content = \Safe\file_get_contents($this->statsFile);
        $data = \Safe\json_decode($content, true);

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    public function testLoadsFromFileAfterRecreation(): void
    {
        $parameters = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        $this->repository->incrementRequestCount($parameters);

        // Create new repository instance (simulates app restart)
        $cache = new ArrayAdapter();
        $newRepository = new StatisticsRepository($cache, $this->statsFile);

        $stats = $newRepository->getMostFrequent();

        $this->assertNotNull($stats);
        $this->assertEquals(1, $stats['count']);
        $this->assertEquals($parameters, $stats['parameters']);
    }

    public function testHandlesMissingDirectory(): void
    {
        $nestedPath = \sys_get_temp_dir().'/nested/dir/stats_'.\uniqid().'.json';
        $cache = new ArrayAdapter();
        $repository = new StatisticsRepository($cache, $nestedPath);

        $parameters = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        // Should create directory automatically
        $repository->incrementRequestCount($parameters);

        $this->assertFileExists($nestedPath);

        // Cleanup
        \Safe\unlink($nestedPath);
        \Safe\rmdir(\dirname($nestedPath));
        \Safe\rmdir(\dirname(\dirname($nestedPath)));
    }

    public function testParameterHashingIsConsistent(): void
    {
        $params1 = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        // Same parameters, different order
        $params2 = [
            'str2' => 'buzz',
            'limit' => 15,
            'int1' => 3,
            'str1' => 'fizz',
            'int2' => 5,
        ];

        $this->repository->incrementRequestCount($params1);
        $this->repository->incrementRequestCount($params2);

        $stats = $this->repository->getMostFrequent();

        // Should be treated as same request (count = 2)
        $this->assertEquals(2, $stats['count']);
    }

    public function testDifferentParametersAreTrackedSeparately(): void
    {
        $params1 = ['int1' => 3, 'int2' => 5, 'limit' => 15, 'str1' => 'fizz', 'str2' => 'buzz'];
        $params2 = ['int1' => 2, 'int2' => 7, 'limit' => 20, 'str1' => 'foo', 'str2' => 'bar'];

        $this->repository->incrementRequestCount($params1);
        $this->repository->incrementRequestCount($params2);

        // Create new repository to force file load
        $cache = new ArrayAdapter();
        new StatisticsRepository($cache, $this->statsFile);

        $content = \Safe\file_get_contents($this->statsFile);
        $data = \Safe\json_decode($content, true);

        // Should have 2 separate entries
        $this->assertCount(2, $data);
    }
}
