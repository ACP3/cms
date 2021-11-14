<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\BootstrapCache;

use ACP3\Core\Environment\ApplicationPath;
use Toflar\Psr6HttpCacheStore\Psr6Store;

class Psr6StoreFactory
{
    public function __construct(private ApplicationPath $applicationPath)
    {
    }

    public function __invoke(): Psr6Store
    {
        return new Psr6Store([
            'cache_directory' => $this->applicationPath->getCacheDir() . 'http',
            'generate_content_digests' => false,
        ]);
    }
}
