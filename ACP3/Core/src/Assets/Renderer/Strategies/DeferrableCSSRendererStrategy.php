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
     * @var string[]|null
     */
    private ?array $stylesheets = null;

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
                $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
                    $library->getModuleName(),
                    static::ASSETS_PATH_CSS,
                    $stylesheet
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function renderHtmlElement(): string
    {
        if ($this->stylesheets === null) {
            $this->initialize();
        }

        $deferrableStylesheets = '';
        $deferrableStylesheetsNoScript = '';

        foreach ($this->stylesheets as $stylesheet) {
            if ($stylesheet === '') {
                continue;
            }

            $deferrableStylesheets .= '<link rel="stylesheet" href="' . $stylesheet . '" media="print" onload="this.media=\'all\'; this.onload=null;">' . "\n";
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

        $this->stylesheets = [];

        $this->fetchLibraries();
    }
}
