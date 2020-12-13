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
     * @var string
     */
    private $enabledLibraries = '';
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

        if (isset($this->designXml->use_bootstrap) && (string) $this->designXml->use_bootstrap === 'true') {
            $this->enableLibraries(['bootstrap']);
        }

        $this->libraries->dispatchAddLibraryEvent();
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
    public function addCssFile(string $file): self
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
    public function addJsFile(string $file): self
    {
        $this->additionalThemeJsFiles[] = $file;

        return $this;
    }

    /**
     * Activates frontend libraries.
     *
     * @return $this
     */
    public function enableLibraries(array $libraries): self
    {
        $this->libraries->enableLibraries($libraries);

        return $this;
    }

    /**
     * @return Array<string, \ACP3\Core\Assets\Dto\LibraryDto>
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getLibraries(): array
    {
        return $this->libraries->getLibraries();
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getEnabledLibrariesAsString(): string
    {
        if (empty($this->enabledLibraries)) {
            $this->enabledLibraries = \implode(',', $this->libraries->getEnabledLibraries());
        }

        return $this->enabledLibraries;
    }
}
