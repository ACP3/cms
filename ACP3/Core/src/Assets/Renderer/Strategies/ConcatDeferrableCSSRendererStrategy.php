<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets\Entity\LibraryEntity;

class ConcatDeferrableCSSRendererStrategy extends ConcatCSSRendererStrategy
{
    protected function getAssetGroup(): string
    {
        return 'css_deferrable';
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function getEnabledLibrariesAsString(): string
    {
        return \implode(',', \array_map(static function (LibraryEntity $library) {
            return $library->getLibraryIdentifier();
        }, $this->getEnabledLibraries()));
    }

    /**
     * @return LibraryEntity[]
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function getEnabledLibraries(): array
    {
        return \array_filter($this->libraries->getEnabledLibraries(), static function (LibraryEntity $library) {
            return $library->getCss() && $library->isDeferrableCss();
        });
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function processLibraries(string $layout): array
    {
        $cacheId = $this->buildCacheId($layout);

        if ($this->systemCache->contains($cacheId) === false) {
            $this->fetchLibraries();

            $this->systemCache->save($cacheId, $this->stylesheets);
        }

        return $this->systemCache->fetch($cacheId);
    }

    /**
     * Fetch all stylesheets of the enabled frontend frameworks/libraries.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function fetchLibraries(): void
    {
        foreach ($this->getEnabledLibraries() as $library) {
            foreach ($library->getCss() as $stylesheet) {
                $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
                    $library->getModuleName(),
                    static::ASSETS_PATH_CSS,
                    $stylesheet
                );
            }
        }
    }

    public function renderHtmlElement(string $layout = 'layout'): string
    {
        $deferrableCssUri = $this->getURI($layout);

        return '<link rel="stylesheet" href="' . $deferrableCssUri . '" media="print" onload="this.media=\'all\'; this.onload=null;">' . "\n"
            . '<noscript><link rel="stylesheet" href="' . $deferrableCssUri . '"></noscript>' . "\n";
    }
}
