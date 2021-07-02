<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Psr6CacheItemPoolFactory
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var string
     */
    private $environment;

    public function __construct(ApplicationPath $applicationPath, string $environment)
    {
        $this->applicationPath = $applicationPath;
        $this->environment = $environment;
    }

    public function __invoke(string $namespace): CacheItemPoolInterface
    {
        $cacheItemPools = [new ArrayAdapter()];
        if ($this->environment === ApplicationMode::PRODUCTION) {
            $cacheItemPools[] = new FilesystemAdapter($namespace, 0, $this->applicationPath->getCacheDir() . 'sql/');
        }

        return new ChainAdapter($cacheItemPools);
    }
}
