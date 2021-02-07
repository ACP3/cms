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
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface
     */
    private $theme;

    public function __construct(SettingsInterface $settings, ThemePathInterface $theme)
    {
        $this->settings = $settings;
        $this->theme = $theme;
    }

    public function __invoke(): array
    {
        $currentTheme = $this->settings->getSettings(Schema::MODULE_NAME)['design'];

        return [
            'designs' => \array_map(
                static function (array $theme) use ($currentTheme) {
                    return \array_merge(
                        $theme,
                        [
                            'selected' => $theme['internal_name'] === $currentTheme,
                        ]
                    );
                },
                \array_filter($this->theme->getAvailableThemes(), static function (array $theme) {
                    return $theme['internal_name'] !== 'acp3-installer';
                })
            ),
        ];
    }
}
