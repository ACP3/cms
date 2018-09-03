<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

class ApplicationMode
{
    const CLI = 'console';
    const DEVELOPMENT = 'dev';
    const INSTALLER = 'installer';
    const PRODUCTION = 'prod';
    const UPDATER = 'updater';
}
