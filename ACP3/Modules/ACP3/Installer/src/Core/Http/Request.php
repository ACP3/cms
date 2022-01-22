<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Http;

use ACP3\Core;

class Request extends Core\Http\Request
{
    /**
     * {@inheritdoc}
     */
    public function processQuery(): void
    {
        parent::processQuery();

        $this->getSymfonyRequest()->attributes->set('_area', Core\Controller\AreaEnum::AREA_INSTALL);
    }
}
