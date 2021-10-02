<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Date;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class DateConfigurator
{
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    public function configure(Date $date): void
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $date
            ->setDateFormatLong($settings['date_format_long'])
            ->setDateFormatShort($settings['date_format_short'])
            ->setDateTimeZone(new \DateTimeZone($settings['date_time_zone']));
    }
}
