<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block;

interface SettingsFormBlockInterface extends FormBlockInterface
{
    /**
     * Returns the internal name of the module
     *
     * @return string
     */
    public function getModuleName(): string;
}
