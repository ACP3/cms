<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Picture\Input;
use ACP3\Core\Picture\Output;

/**
 * @deprecated Deprecated since version 4.30.0, to be removed with version 5.0.0. Use class ACP3\Core\Picture\Picture instead
 */
class Picture
{
    /**
     * @var Picture\Picture
     */
    private $picture;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;

    /**
     * @var bool
     */
    protected $enableCache = false;
    /**
     * @var string
     */
    protected $cacheDir = '';
    /**
     * @var string
     */
    protected $cachePrefix = '';
    /**
     * @var int
     */
    protected $maxWidth = 0;
    /**
     * @var int
     */
    protected $maxHeight = 0;
    /**
     * @var int
     */
    protected $jpgQuality = 85;
    /**
     * @var bool
     */
    protected $preferWidth = false;
    /**
     * @var bool
     */
    protected $preferHeight = true;
    /**
     * @var string
     */
    protected $file = '';
    /**
     * @var bool
     */
    protected $forceResample = false;
    /**
     * @var int|null
     */
    protected $type;
    /**
     * @var \ACP3\Core\Picture\Output|null
     */
    private $output;

    public function __construct(
        Picture\Picture $picture,
        ApplicationPath $appPath
    ) {
        $this->appPath = $appPath;
        $this->picture = $picture;
        $this->cacheDir = $this->appPath->getCacheDir() . 'images/';
    }

    public function freeMemory(): void
    {
        // Intentionally omitted
    }

    /**
     * @param bool $enableCache
     *
     * @return $this
     */
    public function setEnableCache(bool $enableCache)
    {
        $this->enableCache = $enableCache;

        return $this;
    }

    /**
     * @param string $cacheDir
     *
     * @return $this
     */
    public function setCacheDir(string $cacheDir)
    {
        if (empty($cacheDir)) {
            throw new \InvalidArgumentException('The cache directory for the images must not be empty.');
        }

        $this->cacheDir = $cacheDir . (!\preg_match('=/$=', $cacheDir) ? '/' : '');

        return $this;
    }

    /**
     * @param string $cachePrefix
     *
     * @return $this
     */
    public function setCachePrefix(string $cachePrefix)
    {
        $this->cachePrefix = $cachePrefix;

        return $this;
    }

    /**
     * @param int $maxWidth
     *
     * @return $this
     */
    public function setMaxWidth(int $maxWidth)
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    /**
     * @param int $maxHeight
     *
     * @return $this
     */
    public function setMaxHeight(int $maxHeight)
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    /**
     * @param int $jpgQuality
     *
     * @return $this
     */
    public function setJpgQuality(int $jpgQuality)
    {
        $this->jpgQuality = $jpgQuality;

        return $this;
    }

    /**
     * @param bool $preferWidth
     *
     * @return $this
     */
    public function setPreferWidth(bool $preferWidth)
    {
        $this->preferWidth = $preferWidth;
        $this->preferHeight = !$preferWidth;

        return $this;
    }

    /**
     * @param bool $preferHeight
     *
     * @return $this
     */
    public function setPreferHeight(bool $preferHeight)
    {
        $this->preferHeight = $preferHeight;
        $this->preferWidth = !$preferHeight;

        return $this;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile(string $file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): string
    {
        if ($this->output instanceof Output) {
            return $this->output->getFile();
        }

        return $this->file;
    }

    public function getFileWeb(): string
    {
        if ($this->output instanceof Output) {
            return $this->output->getFileWeb();
        }

        return $this->appPath->getWebRoot() . \str_replace(ACP3_ROOT_DIR, '', $this->getFile());
    }

    /**
     * @param bool $forceResample
     *
     * @return $this
     */
    public function setForceResample(bool $forceResample)
    {
        $this->forceResample = $forceResample;

        return $this;
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function process(): void
    {
        $input = (new Input())
            ->setEnableCache($this->enableCache)
            ->setCacheDir($this->cacheDir)
            ->setCachePrefix($this->cachePrefix)
            ->setFile($this->file)
            ->setForceResample($this->forceResample)
            ->setJpgQuality($this->jpgQuality)
            ->setMaxHeight($this->maxHeight)
            ->setMaxWidth($this->maxWidth)
            ->setPreferHeight($this->preferHeight)
            ->setPreferWidth($this->preferWidth);

        $this->output = $this->picture->process($input);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \ACP3\Core\Picture\Exception\PictureResponseException
     */
    public function sendResponse()
    {
        return $this->output->sendResponse();
    }
}
