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
    public function __construct(private readonly Libraries $libraries)
    {
    }

    public function __invoke(): void
    {
        $this->libraries->enableLibraries(['polyfill']);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'layout.content_before' => ['__invoke', 255],
        ];
    }
}
