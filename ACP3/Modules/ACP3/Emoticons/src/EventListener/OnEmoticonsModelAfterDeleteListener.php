<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\EventListener;

use ACP3\Core\Cache;
use ACP3\Core\Model\Event\ModelSaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnEmoticonsModelAfterDeleteListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $emoticonCache;

    public function __construct(Cache $emoticonCache)
    {
        $this->emoticonCache = $emoticonCache;
    }

    public function __invoke(ModelSaveEvent $event): void
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        $this->emoticonCache->deleteAll();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'emoticons.model.emoticons.after_delete' => '__invoke',
        ];
    }
}
