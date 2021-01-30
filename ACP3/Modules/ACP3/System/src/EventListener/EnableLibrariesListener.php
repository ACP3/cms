<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\Assets\Libraries;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EnableLibrariesListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\Assets\Libraries
     */
    private $libraries;

    public function __construct(Libraries $libraries)
    {
        $this->libraries = $libraries;
    }

    public function __invoke(): void
    {
        $this->libraries->enableLibraries(['polyfill', 'jquery', 'font-awesome']);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'layout.content_before' => ['__invoke', 255],
        ];
    }
}
