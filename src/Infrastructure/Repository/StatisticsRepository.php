<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class StatisticsRepository
{
    private const CACHE_KEY = 'fizzbuzz_statistics';
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly string $statsFilePath
    ) {}

    public function incrementRequestCount(array $parameters): void
    {
        $stats = $this->loadStats();
        $key = $this->hashParameters($parameters);
        
        if (!isset($stats[$key])) {
            $stats[$key] = [
                'parameters' => $parameters,
                'count' => 0,
            ];
        }
        
        $stats[$key]['count']++;
        
        $this->saveStats($stats);
    }

    public function getMostFrequent(): ?array
    {
        $stats = $this->loadStats();
        
        if (empty($stats)) {
            return null;
        }
        
        $maxStat = array_reduce($stats, function (?array $carry, array $stat) {
            return ($carry === null || $stat['count'] > $carry['count']) ? $stat : $carry;
        }, null);
        
        return $maxStat;
    }

    private function loadStats(): array
    {
        return $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);
            
            if (file_exists($this->statsFilePath)) {
                $content = file_get_contents($this->statsFilePath);
                return json_decode($content, true) ?? [];
            }
            
            return [];
        });
    }

    private function saveStats(array $stats): void
    {
        // Save to cache
        $this->cache->delete(self::CACHE_KEY);
        $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) use ($stats) {
            $item->expiresAfter(self::CACHE_TTL);
            return $stats;
        });
        
        // Persist to file
        $directory = dirname($this->statsFilePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents(
            $this->statsFilePath,
            json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    private function hashParameters(array $parameters): string
    {
        ksort($parameters);
        return md5(json_encode($parameters));
    }
}
