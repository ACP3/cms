<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\ViewProviders;

use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class AdminThemesViewProvider
{
    public function __construct(private SettingsInterface $settings, private ThemePathInterface $theme)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $currentTheme = $this->settings->getSettings(Schema::MODULE_NAME)['design'];

        return [
            'designs' => array_map(
                static fn (array $theme) => array_merge(
                    $theme,
                    [
                        'selected' => $theme['internal_name'] === $currentTheme,
                    ]
                ),
                array_filter($this->theme->getAvailableThemes(), static fn (array $theme) => $theme['internal_name'] !== 'acp3-installer')
            ),
        ];
    }
}
