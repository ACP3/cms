<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff;

use ACP3\Modules\ACP3\Share\Shariff\Backend\BackendManager;
use ACP3\Modules\ACP3\Share\Shariff\Backend\ServiceFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class Backend
{
    protected BackendManager $backendManager;

    /**
     * @param array<string, mixed> $config
     *
     * @throws \JsonException
     */
    public function __construct(array $config, ClientInterface $client, CacheItemPoolInterface $servicesCacheItemPool, LoggerInterface $logger)
    {
        $domains = $config['domains'];
        // stay compatible to old configs
        if (isset($config['domain'])) {
            $domains[] = $config['domain'];
        }

        $baseCacheKey = md5(json_encode($config, JSON_THROW_ON_ERROR));

        $serviceFactory = new ServiceFactory();
        $this->backendManager = new BackendManager(
            $baseCacheKey,
            $servicesCacheItemPool,
            $client,
            $logger,
            $domains,
            $serviceFactory->getServicesByName($config['services'], $config)
        );
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \JsonException
     */
    public function get(string $url): ?array
    {
        return $this->backendManager->get($url);
    }
}
