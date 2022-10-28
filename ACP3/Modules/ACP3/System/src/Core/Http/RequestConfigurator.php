<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Http;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class RequestConfigurator
{
    public function __construct(private readonly SettingsInterface $config)
    {
    }

    public function configure(RequestInterface $request): void
    {
        $request->setHomepage($this->config->getSettings(Schema::MODULE_NAME)['homepage']);
    }
}
