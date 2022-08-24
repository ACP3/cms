<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture;

final class Input
{
    private bool $enableCache = false;

    private string $cacheDir = '';

    private string $cachePrefix = '';

    private int $maxWidth = 0;

    private int $maxHeight = 0;

    private int $jpgQuality = 85;

    private bool $preferWidth = false;

    private bool $preferHeight = true;

    private string $file = '';

    private bool $forceResample = false;

    public function isEnableCache(): bool
    {
        return $this->enableCache;
    }

    public function setEnableCache(bool $enableCache): self
    {
        $this->enableCache = $enableCache;

        return $this;
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    public function setCacheDir(string $cacheDir): self
    {
        $this->cacheDir = $cacheDir . (str_ends_with($cacheDir, '/') ? '' : '/');

        return $this;
    }

    public function getCachePrefix(): string
    {
        return $this->cachePrefix;
    }

    public function setCachePrefix(string $cachePrefix): self
    {
        $this->cachePrefix = $cachePrefix;

        return $this;
    }

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function setMaxWidth(int $maxWidth): self
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    public function setMaxHeight(int $maxHeight): self
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    public function getJpgQuality(): int
    {
        return $this->jpgQuality;
    }

    public function setJpgQuality(int $jpgQuality): self
    {
        $this->jpgQuality = $jpgQuality;

        return $this;
    }

    public function isPreferWidth(): bool
    {
        return $this->preferWidth;
    }

    public function setPreferWidth(bool $preferWidth): self
    {
        $this->preferWidth = $preferWidth;
        $this->preferHeight = !$preferWidth;

        return $this;
    }

    public function isPreferHeight(): bool
    {
        return $this->preferHeight;
    }

    public function setPreferHeight(bool $preferHeight): self
    {
        $this->preferHeight = $preferHeight;
        $this->preferWidth = !$preferHeight;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function isForceResample(): bool
    {
        return $this->forceResample;
    }

    public function setForceResample(bool $forceResample): self
    {
        $this->forceResample = $forceResample;

        return $this;
    }

    /**
     * Returns the name of a possibly cached picture.
     */
    public function getCacheFileName(): string
    {
        return $this->getCacheDir() . $this->getCacheName();
    }

    /**
     * Generates the file name of the picture to be cached.
     */
    public function getCacheName(): string
    {
        return $this->getCachePrefix() . substr($this->getFile(), strrpos($this->getFile(), '/') + 1);
    }
}
