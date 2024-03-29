<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Breadcrumb;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class TitleConfigurator
{
    public function __construct(private readonly SettingsInterface $settings)
    {
    }

    public function configure(Title $title): void
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $title->setSiteTitle($settings['site_title']);
        $title->setSiteSubtitle($settings['site_subtitle']);
    }
}
