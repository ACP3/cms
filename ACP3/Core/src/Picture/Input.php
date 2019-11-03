<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture;

class Input
{
    /**
     * @var bool
     */
    private $enableCache = false;
    /**
     * @var string
     */
    private $cacheDir = '';
    /**
     * @var string
     */
    private $cachePrefix = '';
    /**
     * @var int
     */
    private $maxWidth = 0;
    /**
     * @var int
     */
    private $maxHeight = 0;
    /**
     * @var int
     */
    private $jpgQuality = 85;
    /**
     * @var bool
     */
    private $preferWidth = false;
    /**
     * @var bool
     */
    private $preferHeight = true;
    /**
     * @var string
     */
    private $file = '';
    /**
     * @var bool
     */
    private $forceResample = false;

    public function isEnableCache(): bool
    {
        return $this->enableCache;
    }

    /**
     * @return $this
     */
    public function setEnableCache(bool $enableCache): self
    {
        $this->enableCache = $enableCache;

        return $this;
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * @return $this
     */
    public function setCacheDir(string $cacheDir): self
    {
        $this->cacheDir = $cacheDir;

        return $this;
    }

    public function getCachePrefix(): string
    {
        return $this->cachePrefix;
    }

    /**
     * @return $this
     */
    public function setCachePrefix(string $cachePrefix): self
    {
        $this->cachePrefix = $cachePrefix;

        return $this;
    }

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    /**
     * @return $this
     */
    public function setMaxWidth(int $maxWidth): self
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * @return $this
     */
    public function setMaxHeight(int $maxHeight): self
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    public function getJpgQuality(): int
    {
        return $this->jpgQuality;
    }

    /**
     * @return $this
     */
    public function setJpgQuality(int $jpgQuality): self
    {
        $this->jpgQuality = $jpgQuality;

        return $this;
    }

    public function isPreferWidth(): bool
    {
        return $this->preferWidth;
    }

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function isForceResample(): bool
    {
        return $this->forceResample;
    }

    /**
     * @return $this
     */
    public function setForceResample(bool $forceResample): self
    {
        $this->forceResample = $forceResample;

        return $this;
    }
}
