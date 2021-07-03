<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Modules\ACP3\Seo\ModuleRegistration;

ComponentRegistry::add(
    new ComponentDataDto(
        ComponentTypeEnum::MODULE,
        'seo',
        __DIR__,
        ['core', 'system'],
        new ModuleRegistration()
    )
);
