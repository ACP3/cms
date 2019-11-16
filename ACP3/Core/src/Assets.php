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
    protected $additionalThemeCssFiles = [];
    /**
     * @var array
     */
    protected $additionalThemeJsFiles = [];
    /**
     * @var string
     */
    protected $enabledLibraries = '';
    /**
     * @var \SimpleXMLElement
     */
    protected $designXml;
    /**
     * @var Libraries
     */
    protected $libraries;

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

    /**
     * @return array
     */
    public function fetchAdditionalThemeCssFiles()
    {
        if (isset($this->designXml->css) && empty($this->additionalThemeCssFiles)) {
            foreach ($this->designXml->css->item as $file) {
                $this->addCssFile($file);
            }
        }

        return $this->additionalThemeCssFiles;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function addCssFile($file)
    {
        $this->additionalThemeCssFiles[] = $file;

        return $this;
    }

    /**
     * @return array
     */
    public function fetchAdditionalThemeJsFiles()
    {
        if (isset($this->designXml->js) && empty($this->additionalThemeJsFiles)) {
            foreach ($this->designXml->js->item as $file) {
                $this->addJsFile($file);
            }
        }

        return $this->additionalThemeJsFiles;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function addJsFile($file)
    {
        $this->additionalThemeJsFiles[] = $file;

        return $this;
    }

    /**
     * Activates frontend libraries.
     *
     * @return $this
     */
    public function enableLibraries(array $libraries)
    {
        $this->libraries->enableLibraries($libraries);

        return $this;
    }

    /**
     * @return array
     */
    public function getLibraries()
    {
        return $this->libraries->getLibraries();
    }

    /**
     * @return string
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getEnabledLibrariesAsString()
    {
        if (empty($this->enabledLibraries)) {
            $this->enabledLibraries = \implode(',', $this->libraries->getEnabledLibraries());
        }

        return $this->enabledLibraries;
    }
}
