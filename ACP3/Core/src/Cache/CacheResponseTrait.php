<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Response;

trait CacheResponseTrait
{
    abstract protected function getRequest(): RequestInterface;

    abstract protected function getApplicationMode(): string;

    /**
     * @param int $lifetime Cache TTL in seconds
     */
    public function setCacheResponseCacheable(Response $response, int $lifetime = 60): void
    {
        if (!$this->canUsePageCache()) {
            return;
        }

        $varyHeaderName = 'X-User-Context-Hash';

        $response
            ->setVary([$varyHeaderName, 'X-Requested-With'])
            ->setSharedMaxAge(1)
            ->headers->add([
                $varyHeaderName => $this->getRequest()->getSymfonyRequest()->headers->get($varyHeaderName),
                'X-Reverse-Proxy-TTL' => $lifetime,
            ]);
    }

    protected function canUsePageCache(): bool
    {
        return $this->getApplicationMode() !== ApplicationMode::DEVELOPMENT;
    }
}
