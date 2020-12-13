<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\Event\Listener;

use ACP3\Core\Assets\Dto\LibraryDto;
use ACP3\Core\Assets\Event\AddLibraryEvent;

class AddConsentManagementAssetsListener
{
    public function __invoke(AddLibraryEvent $event): void
    {
        $event->addLibrary(new LibraryDto(
            'consentManager',
            false,
            false,
            [],
            ['klaro.css'],
            ['klaro-config.js', 'klaro-no-css.js'],
            'cookieconsent'
        ));
    }
}
