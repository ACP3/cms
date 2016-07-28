<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Cache\Event\Listener;


use ACP3\Core\Cache\Purge;
use ACP3\Core\Environment\ApplicationPath;

class OnModelAfterSaveListener
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;

    /**
     * OnModelAfterSaveListener constructor.
     * @param ApplicationPath $applicationPath
     */
    public function __construct(ApplicationPath $applicationPath)
    {
        $this->applicationPath = $applicationPath;
    }

    public function purgeHttpCache()
    {
        Purge::doPurge($this->applicationPath->getCacheDir() . 'http');
    }
}
