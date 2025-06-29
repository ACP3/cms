<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Assets\Libraries;

class DeferrableCSSRendererStrategy implements CSSRendererStrategyInterface
{
    protected const ASSETS_PATH_CSS = 'Assets/css';

    /**
     * @var string[]
     */
    private array $stylesheets = [];

    public function __construct(private readonly Assets $assets, private readonly Libraries $libraries, private readonly FileResolver $fileResolver)
    {
    }

    /**
     * Fetch all stylesheets of the enabled frontend frameworks/libraries.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function fetchLibraries(): void
    {
        foreach ($this->libraries->getEnabledLibraries() as $library) {
            if (!$library->getCss() || !$library->isDeferrableCss()) {
                continue;
            }

            foreach ($library->getCss() as $stylesheet) {
                $this->addFiles(
                    $this->fileResolver->getWebStaticAssetPath(
                        $library->getModuleName(),
                        static::ASSETS_PATH_CSS,
                        $stylesheet
                    ),
                );
            }
        }
    }

    public function addFiles(array $files): void
    {
        foreach ($files as $file) {
            if (!empty($file) && !\in_array($file, $this->stylesheets, true)) {
                $this->stylesheets[] = $file;
            }
        }
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function renderHtmlElement(): string
    {
        $this->initialize();

        $deferrableStylesheets = '';
        $deferrableStylesheetsNoScript = '';

        foreach ($this->stylesheets as $stylesheet) {
            if (empty($stylesheet)) {
                continue;
            }

            $deferrableStylesheets .= '<link rel="preload" href="' . $stylesheet . '" as="style" onload="this.onload=null; this.rel=\'stylesheet\'">' . "\n";
            $deferrableStylesheetsNoScript .= '<link rel="stylesheet" href="' . $stylesheet . '">' . "\n";
        }

        return $deferrableStylesheets . "<noscript>\n" . $deferrableStylesheetsNoScript . '</noscript>';
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function initialize(): void
    {
        $this->assets->initializeTheme();

        $backup = $this->stylesheets;
        $this->stylesheets = [];

        $this->fetchLibraries();

        $this->addFiles($backup);
    }
}
