<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\XML;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Theme
{
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

    /**
     * Design constructor.
     *
     * @param \ACP3\Core\Settings\SettingsInterface $settings
     * @param \ACP3\Core\XML                        $xml
     */
    public function __construct(
        SettingsInterface $settings,
        XML $xml
    ) {
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
        $designs = \glob(ACP3_ROOT_DIR . '/designs/*/info.xml');

        if ($designs === false) {
            return;
        }

        foreach ($designs as $design) {
            $designInfo = $this->xml->parseXmlFile($design, '/design');
            if (!empty($designInfo)) {
                $identifier = $this->getThemeInternalName($design);
                $this->availableThemes[$identifier] = $designInfo;
            }
        }
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function getThemeInternalName(string $file): string
    {
        $path = \dirname($file);
        $pathParts = \explode('/', $path);

        return $pathParts[\count($pathParts) - 1];
    }

    public function getCurrentTheme(): string
    {
        return $this->settings->getSettings(Schema::MODULE_NAME)['design'];
    }

    /**
     * @return array
     */
    public function getCurrentThemeDependencies(): array
    {
        return $this->getThemeDependencies($this->getCurrentTheme());
    }

    /**
     * @param string $themeName
     *
     * @return array
     */
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
}
