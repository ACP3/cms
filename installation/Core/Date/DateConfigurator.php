<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Date;

use ACP3\Core\Date;

class DateConfigurator
{
    public function configure(Date $date)
    {
        $defaultTimeZone = \date_default_timezone_get();

        $date->setDateFormatLong('d.m.y, H:i');
        $date->setDateFormatShort('d.m.y');
        $date->setDateTimeZone(new \DateTimeZone(!empty($defaultTimeZone) ? $defaultTimeZone : 'UTC'));
    }
}
