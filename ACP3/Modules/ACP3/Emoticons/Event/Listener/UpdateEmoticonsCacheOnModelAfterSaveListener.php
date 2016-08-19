<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;


use ACP3\Modules\ACP3\Emoticons\Cache;

class UpdateEmoticonsCacheOnModelAfterSaveListener
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * UpdateEmoticonsCacheOnModelAfterSaveListener constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function execute()
    {
        $this->cache->saveCache();
    }
}
