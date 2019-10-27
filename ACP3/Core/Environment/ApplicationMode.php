<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

use ACP3\Core\Enum\BaseEnum;

class ApplicationMode extends BaseEnum
{
    public const CLI = 'console';
    public const DEVELOPMENT = 'dev';
    public const INSTALLER = 'installer';
    public const PRODUCTION = 'prod';
    public const UPDATER = 'updater';
}
