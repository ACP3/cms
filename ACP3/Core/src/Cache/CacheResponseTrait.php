<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

trait CacheResponseTrait
{
    abstract protected function getRequest(): RequestInterface;

    /**
     * @return Response
     */
    abstract protected function getResponse();

    /**
     * @return string
     */
    abstract protected function getApplicationMode();

    /**
     * @return SettingsInterface
     */
    abstract protected function getSettings();

    /**
     * @param int $lifetime Cache TTL in seconds
     */
    public function setCacheResponseCacheable(int $lifetime = 60, ?Response $response = null): void
    {
        if ($this->canUsePageCache()) {
            $varyHeaderName = 'X-User-Context-Hash';

            ($response ?? $this->getResponse())
                ->setVary($varyHeaderName)
                ->setSharedMaxAge(1)
                ->headers->add([
                    $varyHeaderName => $this->getRequest()->getSymfonyRequest()->headers->get($varyHeaderName),
                    'X-Reverse-Proxy-TTL' => $lifetime,
                ]);
        }
    }

    protected function canUsePageCache(): bool
    {
        $systemSettings = $this->getSettings()->getSettings(Schema::MODULE_NAME);

        return $this->getApplicationMode() !== ApplicationMode::DEVELOPMENT
            && $systemSettings['page_cache_is_enabled'] == 1;
    }
}
