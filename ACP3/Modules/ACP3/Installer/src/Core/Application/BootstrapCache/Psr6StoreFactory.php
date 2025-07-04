<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Application\BootstrapCache;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\InMemoryStore;
use Toflar\Psr6HttpCacheStore\Psr6Store;

class Psr6StoreFactory
{
    public function __invoke(): Psr6Store
    {
        return new Psr6Store([
            'cache' => new ArrayAdapter(),
            'generate_content_digests' => false,
            'lock_factory' => new LockFactory(new InMemoryStore()),
        ]);
    }
}
