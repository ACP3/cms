<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Event\Listener;

use ACP3\Core\Assets\Event\AddLibraryEvent;

class AddShariffAssetsListener
{
    public function execute(AddLibraryEvent $event): void
    {
        $event->addLibrary('shariff', [
            'enabled' => false,
            'module' => 'Share',
            'dependencies' => ['font-awesome'],
            'css' => 'shariff.min.css',
            'js' => 'shariff.min.js',
        ]);
    }
}
