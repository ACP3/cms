<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

class ApplicationMode
{
    const PRODUCTION = 'prod';
    const DEVELOPMENT = 'dev';
    const INSTALLER = 'installer';
    const UPDATER = 'updater';
}
