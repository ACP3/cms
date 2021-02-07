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
     * @var array|null
     */
    private $additionalThemeCssFiles;
    /**
     * @var array|null
     */
    private $additionalThemeJsFiles;
    /**
     * @var Libraries
     */
    private $libraries;
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface
     */
    private $theme;

    public function __construct(ThemePathInterface $theme, Libraries $libraries)
    {
        $this->theme = $theme;
        $this->libraries = $libraries;

        $this->libraries->dispatchAddLibraryEvent();
    }

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

        $themeConfig = \simplexml_load_string(\file_get_contents($this->theme->getDesignPathInternal() . DIRECTORY_SEPARATOR . 'info.xml'));

        if (isset($themeConfig->libraries)) {
            foreach ($themeConfig->libraries->item as $libraryName) {
                $this->libraries->enableLibraries([(string) $libraryName]);
            }
        }

        /* @deprecated To be removed this version 6.0.0. Use the <libraries><item>library-name</item></libraries> within the info.xml instead! */
        if (isset($themeConfig->use_bootstrap) && (string) $themeConfig->use_bootstrap === 'true') {
            $this->libraries->enableLibraries(['bootstrap']);
        }

        $this->additionalThemeCssFiles = [];

        if (isset($themeConfig->css)) {
            foreach ($themeConfig->css->item as $file) {
                $this->addCssFile($file);
            }
        }

        $this->additionalThemeJsFiles = [];

        if (isset($themeConfig->js)) {
            foreach ($themeConfig->js->item as $file) {
                $this->addJsFile($file);
            }
        }
    }

    /**
     * @return $this
     */
    private function addCssFile(string $file): self
    {
        $this->additionalThemeCssFiles[] = $file;

        return $this;
    }

    public function fetchAdditionalThemeJsFiles(): array
    {
        if ($this->additionalThemeJsFiles === null) {
            throw new \RuntimeException('The theme hasn\'t been initialized. Please call "' . __CLASS__ . '::initializeTheme() first!');
        }

        return $this->additionalThemeJsFiles;
    }

    /**
     * @return $this
     */
    private function addJsFile(string $file): self
    {
        $this->additionalThemeJsFiles[] = $file;

        return $this;
    }

    /**
     * @deprecated To be removed with version 6.0.0. Use ACP3\Core\Assets\Libraries::getLibraries() instead.
     *
     * @return Array<string, \ACP3\Core\Assets\Entity\LibraryEntity>
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getLibraries(): array
    {
        return $this->libraries->getLibraries();
    }

    /**
     * @deprecated To be removed with version 6.0.0. Use ACP3\Core\Assets\Libraries::getEnabledLibrariesAsString() instead.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getEnabledLibrariesAsString(): string
    {
        return $this->libraries->getEnabledLibrariesAsString();
    }
}
