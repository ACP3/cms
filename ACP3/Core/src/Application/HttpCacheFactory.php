<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HttpCacheFactory
{
    /**
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $httpKernel;
    /**
     * @var \Symfony\Component\HttpKernel\HttpCache\StoreInterface
     */
    private $store;
    /**
     * @var \Symfony\Component\HttpKernel\HttpCache\SurrogateInterface
     */
    private $surrogate;
    /**
     * @var string
     */
    private $applicationMode;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        HttpKernelInterface $httpKernel,
        StoreInterface $store,
        SurrogateInterface $surrogate,
        string $applicationMode
    ) {
        $this->httpKernel = $httpKernel;
        $this->store = $store;
        $this->surrogate = $surrogate;
        $this->applicationMode = $applicationMode;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(): BootstrapCache
    {
        return new BootstrapCache(
            $this->eventDispatcher,
            $this->httpKernel,
            $this->store,
            $this->surrogate,
            ['debug' => $this->applicationMode === ApplicationMode::DEVELOPMENT]
        );
    }
}
