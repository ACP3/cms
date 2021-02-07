<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Component;

use ACP3\Core\Enum\BaseEnum;

class ComponentTypeEnum extends BaseEnum
{
    public const CORE = 'core';
    public const INSTALLER = 'installer';
    public const MODULE = 'module';
    public const THEME = 'theme';
}
