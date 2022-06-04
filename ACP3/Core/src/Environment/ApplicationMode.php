<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

enum ApplicationMode: string
{
    case CLI = 'console';
    case DEVELOPMENT = 'dev';
    case INSTALLER = 'installer';
    case PRODUCTION = 'prod';
    case UPDATER = 'updater';
}
