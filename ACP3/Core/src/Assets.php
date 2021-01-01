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
     * @var array
     */
    private $additionalThemeCssFiles = [];
    /**
     * @var array
     */
    private $additionalThemeJsFiles = [];
    /**
     * @var \SimpleXMLElement
     */
    private $designXml;
    /**
     * @var Libraries
     */
    private $libraries;

    /**
     * Checks, whether the current design uses Bootstrap or not.
     */
    public function __construct(ThemePathInterface $theme, Libraries $libraries)
    {
        $this->designXml = \simplexml_load_string(\file_get_contents($theme->getDesignPathInternal() . 'info.xml'));
        $this->libraries = $libraries;

        $this->libraries->dispatchAddLibraryEvent();

        if (isset($this->designXml->use_bootstrap) && (string) $this->designXml->use_bootstrap === 'true') {
            $this->libraries->enableLibraries(['bootstrap']);
        }
    }

    public function fetchAdditionalThemeCssFiles(): array
    {
        if (isset($this->designXml->css) && empty($this->additionalThemeCssFiles)) {
            foreach ($this->designXml->css->item as $file) {
                $this->addCssFile($file);
            }
        }

        return $this->additionalThemeCssFiles;
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
        if (isset($this->designXml->js) && empty($this->additionalThemeJsFiles)) {
            foreach ($this->designXml->js->item as $file) {
                $this->addJsFile($file);
            }
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
     * @deprecated To be removed with version 6.x. Use ACP3\Core\Assets\Libraries::getLibraries() instead.
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
     * @deprecated To be removed with version 6.x. Use ACP3\Core\Assets\Libraries::getEnabledLibrariesAsString() instead.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getEnabledLibrariesAsString(): string
    {
        return $this->libraries->getEnabledLibrariesAsString();
    }
}
