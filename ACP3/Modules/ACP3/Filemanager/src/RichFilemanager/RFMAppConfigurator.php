<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\RichFilemanager;

use RFM\Api\LocalApi;
use RFM\Application;
use RFM\Repository\StorageInterface;

class RFMAppConfigurator
{
    public function __construct(private StorageInterface $storage)
    {
    }

    public function __invoke(Application $rfmApp): void
    {
        $rfmApp->setStorage($this->storage);

        $rfmApp->api = new LocalApi();
    }
}
