<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\BootstrapCache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\InMemoryStore;
use Toflar\Psr6HttpCacheStore\Psr6Store;

class Psr6StoreFactory
{
    public function __construct(private readonly ApplicationPath $applicationPath, private readonly ApplicationMode $applicationMode)
    {
    }

    public function __invoke(): Psr6Store
    {
        if ($this->applicationMode === ApplicationMode::PRODUCTION) {
            return new Psr6Store([
                'cache_directory' => $this->applicationPath->getCacheDir() . 'http',
                'generate_content_digests' => false,
            ]);
        }

        return new Psr6Store([
            'cache' => new ArrayAdapter(),
            'generate_content_digests' => false,
            'lock_factory' => new LockFactory(new InMemoryStore()),
        ]);
    }
}
