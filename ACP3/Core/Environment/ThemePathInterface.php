<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

interface ThemePathInterface
{
    public function getCurrentTheme(): string;

    public function getDesignPathInternal(): string;

    public function getDesignPathWeb(): string;
}
