<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Assets\Libraries;
use ACP3\Core\Environment\ThemePathInterface;

class Assets
{
    /**
     * @var string[]|null
     */
    private ?array $additionalThemeCssFiles = null;
    /**
     * @var string[]|null
     */
    private ?array $additionalThemeJsFiles = null;

    public function __construct(private readonly ThemePathInterface $theme, private readonly Libraries $libraries)
    {
    }

    /**
     * @return string[]
     */
    public function fetchAdditionalThemeCssFiles(): array
    {
        if ($this->additionalThemeCssFiles === null) {
            throw new \RuntimeException('The theme hasn\'t been initialized. Please call "' . __CLASS__ . '::initializeTheme() first!');
        }

        return $this->additionalThemeCssFiles;
    }

    public function initializeTheme(): void
    {
        if ($this->additionalThemeCssFiles !== null && $this->additionalThemeJsFiles !== null) {
            return;
        }

        $themeConfig = $this->theme->getCurrentThemeInfo();

        foreach ($themeConfig['libraries'] as $libraryName) {
            $this->libraries->enableLibraries([$libraryName]);
        }

        $this->additionalThemeCssFiles = [];

        foreach ($themeConfig['css'] as $file) {
            $this->addCssFile($file);
        }

        $this->additionalThemeJsFiles = [];

        foreach ($themeConfig['js'] as $file) {
            $this->addJsFile($file);
        }
    }

    private function addCssFile(string $file): void
    {
        $this->additionalThemeCssFiles[] = $file;
    }

    /**
     * @return string[]
     */
    public function fetchAdditionalThemeJsFiles(): array
    {
        if ($this->additionalThemeJsFiles === null) {
            throw new \RuntimeException('The theme hasn\'t been initialized. Please call "' . __CLASS__ . '::initializeTheme() first!');
        }

        return $this->additionalThemeJsFiles;
    }

    private function addJsFile(string $file): void
    {
        $this->additionalThemeJsFiles[] = $file;
    }
}
