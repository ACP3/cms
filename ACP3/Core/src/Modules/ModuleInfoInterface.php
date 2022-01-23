<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

interface ModuleInfoInterface
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getModulesInfo(): array;
}
