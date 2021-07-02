<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\EventListener;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClearEmoticonsCacheListener implements EventSubscriberInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $emoticonCachePool;

    public function __construct(CacheItemPoolInterface $emoticonCachePool)
    {
        $this->emoticonCachePool = $emoticonCachePool;
    }

    public function __invoke()
    {
        $this->emoticonCachePool->clear();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'emoticons.model.emoticons.after_save' => '__invoke',
            'emoticons.model.emoticons.after_delete' => '__invoke',
        ];
    }
}
