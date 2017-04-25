<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
