<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Event\Listener;

use ACP3\Core\Assets\Entity\LibraryEntity;
use ACP3\Core\Assets\Event\AddLibraryEvent;

class AddShariffAssetsListener
{
    public function __invoke(AddLibraryEvent $event): void
    {
        $event->addLibrary(new LibraryEntity(
            'shariff',
            false,
            ['font-awesome'],
            ['shariff.min.css'],
            ['shariff.min.js'],
            'share',
            true
        ));
    }
}
