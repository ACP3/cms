<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Event\Listener;

use ACP3\Core\Assets\Dto\LibraryDto;
use ACP3\Core\Assets\Event\AddLibraryEvent;

class AddShariffAssetsListener
{
    public function __invoke(AddLibraryEvent $event): void
    {
        $event->addLibrary(new LibraryDto(
            'shariff',
            false,
            false,
            ['font-awesome'],
            ['shariff.min.css'],
            ['shariff.min.js'],
            'share',
            true
        ));
    }
}
