<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Component;

enum ComponentTypeEnum: string
{
    case CORE = 'core';
    case INSTALLER = 'installer';
    case MODULE = 'module';
    case THEME = 'theme';
}
