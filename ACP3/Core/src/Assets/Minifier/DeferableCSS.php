<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Minifier;

class DeferableCSS extends CSS
{
    protected function getAssetGroup(): string
    {
        return 'css_deferable';
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
        foreach ($this->assets->getLibraries() as $library) {
            if ($library->isEnabled() === false || !$library->getCss() || !$library->isDeferableCss()) {
                continue;
            }

            foreach ($library->getCss() as $stylesheet) {
                $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
                    $library->getModuleName() ?: static::SYSTEM_MODULE_NAME,
                    static::ASSETS_PATH_CSS,
                    $stylesheet
                );
            }
        }
    }
}
