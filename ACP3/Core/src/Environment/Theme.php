<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\XML;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Theme implements ThemePathInterface
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\XML
     */
    private $xml;

    /**
     * @var array
     */
    private $availableThemes = [];
    /**
     * @var array
     */
    private $sortedThemeDependencies = [];

    public function __construct(
        ApplicationPath $appPath,
        SettingsInterface $settings,
        XML $xml
    ) {
        $this->appPath = $appPath;
        $this->settings = $settings;
        $this->xml = $xml;
    }

    public function getAvailableThemes(): array
    {
        if (empty($this->availableThemes)) {
            $this->setAvailableThemes();
        }

        return $this->availableThemes;
    }

    private function setAvailableThemes(): void
    {
        $vendoredThemes = ComponentRegistry::filterByType(ComponentRegistry::all(), [ComponentTypeEnum::THEME]);
        $unvendoredThemes = \glob(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR . 'designs' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'info.xml');

        $filteredThemes = \array_unique(
            \array_merge(
                \array_map(static function (ComponentDataDto $vendoredTheme) {
                    return $vendoredTheme->getPath();
                }, $vendoredThemes),
                \array_map(static function (string $unvendoredTheme) {
                    return \dirname($unvendoredTheme);
                }, $unvendoredThemes)
            )
        );

        foreach ($filteredThemes as $themePath) {
            $designInfo = $this->xml->parseXmlFile($themePath . DIRECTORY_SEPARATOR . 'info.xml', '/design');
            if (!empty($designInfo)) {
                $themeName = $this->getThemeInternalName($themePath);
                $this->availableThemes[$themeName] = \array_merge(
                    $designInfo,
                    [
                        'internal_name' => $themeName,
                        'path' => $themePath,
                        'web_path' => $this->appPath->getWebRoot() . \str_replace([ACP3_ROOT_DIR, '\\'], ['', '/'], $themeName),
                    ]
                );
            }
        }
    }

    private function getThemeInternalName(string $themePath): string
    {
        return \str_replace([\dirname($themePath), DIRECTORY_SEPARATOR], '', $themePath);
    }

    public function has(string $themeName): bool
    {
        return \array_key_exists($themeName, $this->getAvailableThemes());
    }

    public function getCurrentTheme(): string
    {
        return $this->settings->getSettings(Schema::MODULE_NAME)['design'];
    }

    public function getCurrentThemeDependencies(): array
    {
        return $this->getThemeDependencies($this->getCurrentTheme());
    }

    public function getThemeDependencies(string $themeName): array
    {
        if (!isset($this->sortedThemeDependencies[$themeName])) {
            $this->setThemeDependencies($themeName);
        }

        return $this->sortedThemeDependencies[$themeName];
    }

    private function setThemeDependencies(string $themeName): void
    {
        $availableThemes = $this->getAvailableThemes();

        if (!isset($availableThemes[$themeName]['parent'])) {
            $this->sortedThemeDependencies[$themeName] = [$themeName];

            return;
        }

        $currentTheme = $themeName;

        $parents = [$themeName];
        do {
            $currentTheme = $availableThemes[$currentTheme]['parent'];
            $parents[] = $currentTheme;
        } while (isset($availableThemes[$currentTheme]['parent']));

        $this->sortedThemeDependencies[$themeName] = $parents;
    }

    /**
     * {@inheritDoc}
     */
    public function getDesignPathInternal(?string $themeName = null): string
    {
        return $this->getAvailableThemes()[$themeName ?? $this->getCurrentTheme()]['path'];
    }

    /**
     * {@inheritDoc}
     */
    public function getDesignPathWeb(?string $themeName = null): string
    {
        return $this->getAvailableThemes()[$themeName ?? $this->getCurrentTheme()]['web_path'];
    }
}
