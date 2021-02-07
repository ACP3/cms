<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper\Dto;

class ThemeDto
{
    /**
     * @var string
     */
    private $themeName;
    /**
     * @var array
     */
    private $themeInfo;

    public function __construct(string $themeName, array $themeInfo)
    {
        $this->themeName = $themeName;
        $this->themeInfo = $themeInfo;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function getThemeInfo(): array
    {
        return $this->themeInfo;
    }
}
