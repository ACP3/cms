<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CacheResponseTrait
 * @package ACP3\Core\Cache
 */
trait CacheResponseTrait
{
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
     * @param int|null $lifetime Cache TTL in seconds
     */
    public function setCacheResponseCacheable($lifetime = null)
    {
        $response = $this->getResponse();

        $systemSettings = $this->getSettings()->getSettings(Schema::MODULE_NAME);

        if ($this->disallowPageCache($systemSettings)) {
            $response->setPrivate();
            $lifetime = null;
        } else {
            $response->setPublic();
            if ($lifetime === null) {
                $lifetime = $systemSettings['cache_lifetime'];
            }
        }

        $response
            ->setVary('X-User-Context-Hash')
            ->setMaxAge($lifetime)
            ->setSharedMaxAge($lifetime);
    }

    /**
     * @param array $systemSettings
     * @return bool
     */
    protected function disallowPageCache(array $systemSettings)
    {
        return $this->getApplicationMode() === ApplicationMode::DEVELOPMENT || $systemSettings['page_cache_is_enabled'] == 0;
    }
}
