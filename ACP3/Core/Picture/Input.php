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
    private $preferHeight = false;
    /**
     * @var string
     */
    private $file = '';
    /**
     * @var bool
     */
    private $forceResample = false;

    /**
     * @return bool
     */
    public function isEnableCache(): bool
    {
        return $this->enableCache;
    }

    /**
     * @param bool $enableCache
     *
     * @return $this
     */
    public function setEnableCache(bool $enableCache): self
    {
        $this->enableCache = $enableCache;

        return $this;
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     *
     * @return $this
     */
    public function setCacheDir(string $cacheDir): self
    {
        $this->cacheDir = $cacheDir;

        return $this;
    }

    /**
     * @return string
     */
    public function getCachePrefix(): string
    {
        return $this->cachePrefix;
    }

    /**
     * @param string $cachePrefix
     *
     * @return $this
     */
    public function setCachePrefix(string $cachePrefix): self
    {
        $this->cachePrefix = $cachePrefix;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    /**
     * @param int $maxWidth
     *
     * @return $this
     */
    public function setMaxWidth(int $maxWidth): self
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * @param int $maxHeight
     *
     * @return $this
     */
    public function setMaxHeight(int $maxHeight): self
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    /**
     * @return int
     */
    public function getJpgQuality(): int
    {
        return $this->jpgQuality;
    }

    /**
     * @param int $jpgQuality
     *
     * @return $this
     */
    public function setJpgQuality(int $jpgQuality): self
    {
        $this->jpgQuality = $jpgQuality;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPreferWidth(): bool
    {
        return $this->preferWidth;
    }

    /**
     * @param bool $preferWidth
     *
     * @return $this
     */
    public function setPreferWidth(bool $preferWidth): self
    {
        $this->preferWidth = $preferWidth;
        $this->preferHeight = !$preferWidth;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPreferHeight(): bool
    {
        return $this->preferHeight;
    }

    /**
     * @param bool $preferHeight
     *
     * @return $this
     */
    public function setPreferHeight(bool $preferHeight): self
    {
        $this->preferHeight = $preferHeight;
        $this->preferWidth = !$preferHeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return bool
     */
    public function isForceResample(): bool
    {
        return $this->forceResample;
    }

    /**
     * @param bool $forceResample
     *
     * @return $this
     */
    public function setForceResample(bool $forceResample): self
    {
        $this->forceResample = $forceResample;

        return $this;
    }
}
