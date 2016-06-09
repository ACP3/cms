<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CacheResponseTrait
 * @package ACP3\Core\Cache
 */
trait CacheResponseTrait
{
    /**
     * @return User
     */
    abstract protected function getUser();

    /**
     * @return Response
     */
    abstract protected function getResponse();

    /**
     * @return string
     */
    abstract protected function getApplicationMode();

    /**
     * @param int $lifetime
     */
    public function setCacheResponseCacheable($lifetime = 60)
    {
        $response = $this->getResponse();

        if ($this->getUser()->isAuthenticated() || $this->getApplicationMode() === ApplicationMode::DEVELOPMENT) {
            $response->setPrivate();
            $lifetime = null;
        }

        $response
            ->setPublic()
            ->setMaxAge($lifetime)
            ->setSharedMaxAge($lifetime);
    }
}
