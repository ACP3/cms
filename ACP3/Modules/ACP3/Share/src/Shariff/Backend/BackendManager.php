<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BackendManager.
 */
class BackendManager
{
    protected LoggerInterface $logger;

    /**
     * @param string[]           $domains
     * @param ServiceInterface[] $services
     */
    public function __construct(
        protected string $baseCacheKey,
        protected CacheItemPoolInterface $cache,
        protected ClientInterface $client,
        protected array $domains,
        protected array $services
    ) {
    }

    public function setLogger(LoggerInterface $logger = null): void
    {
        $this->logger = $logger;
    }

    /**
     * @return array|mixed|null
     *
     * @throws \JsonException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function get(string $url)
    {
        // Changing configuration invalidates the cache
        $cacheKey = md5($url . $this->baseCacheKey);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return json_decode($cacheItem->get(), true, 512, JSON_THROW_ON_ERROR);
        }

        if (!$this->isValidDomain($url)) {
            return null;
        }

        $requests = array_map(
            static fn ($service) => $service->getRequest($url),
            $this->services
        );

        /** @var ResponseInterface[]|TransferException[] $results */
        $results = Pool::batch($this->client, $requests);

        $counts = [];
        $i = 0;
        foreach ($this->services as $service) {
            if ($results[$i] instanceof TransferException) {
                $this->logger?->warning($results[$i]->getMessage(), ['exception' => $results[$i]]);
            } else {
                try {
                    $content = $service->filterResponse($results[$i]->getBody()->getContents());
                    $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                    $counts[$service->getName()] = \is_array($json) ? $service->extractCount($json) : 0;
                } catch (\Exception $e) {
                    $this->logger?->warning($e->getMessage(), ['exception' => $e]);
                }
            }
            ++$i;
        }

        $cacheItem->set(json_encode($counts, JSON_THROW_ON_ERROR));
        $this->cache->save($cacheItem);

        return $counts;
    }

    private function isValidDomain(string $url): bool
    {
        if (!empty($this->domains)) {
            $parsed = parse_url($url);

            return \in_array($parsed['host'], $this->domains, true);
        }

        return true;
    }
}
