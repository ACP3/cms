<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;
use FastImageSize\FastImageSize;
use Symfony\Component\HttpFoundation\Response;

class Picture
{
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
    protected $preferHeight = false;
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
     * @var FastImageSize
     */
    private $fastImageSize;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var string
     */
    protected $environment = '';

    /**
     * @var resource
     */
    protected $image;

    /**
     * @param FastImageSize                              $fastImageSize
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \ACP3\Core\Environment\ApplicationPath     $appPath
     * @param string                                     $environment
     */
    public function __construct(
        FastImageSize $fastImageSize,
        Response $response,
        ApplicationPath $appPath,
        string $environment
    ) {
        $this->fastImageSize = $fastImageSize;
        $this->response = $response;
        $this->appPath = $appPath;
        $this->environment = $environment;

        $this->cacheDir = $this->appPath->getCacheDir() . 'images/';
    }

    /**
     * Gibt den während der Bearbeitung belegten Speicher wieder frei.
     */
    public function __destruct()
    {
        $this->freeMemory();
    }

    public function freeMemory(): void
    {
        if (\is_resource($this->image) === true) {
            \imagedestroy($this->image);
        }
    }

    /**
     * @param bool $enableCache
     *
     * @return $this
     */
    public function setEnableCache($enableCache)
    {
        $this->enableCache = (bool) $enableCache;

        return $this;
    }

    /**
     * @param string $cacheDir
     *
     * @return $this
     */
    public function setCacheDir($cacheDir)
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
    public function setCachePrefix($cachePrefix)
    {
        $this->cachePrefix = $cachePrefix;

        return $this;
    }

    /**
     * @param int $maxWidth
     *
     * @return $this
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = (int) $maxWidth;

        return $this;
    }

    /**
     * @param int $maxHeight
     *
     * @return $this
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = (int) $maxHeight;

        return $this;
    }

    /**
     * @param int $jpgQuality
     *
     * @return $this
     */
    public function setJpgQuality($jpgQuality)
    {
        $this->jpgQuality = (int) $jpgQuality;

        return $this;
    }

    /**
     * @param bool $preferWidth
     *
     * @return $this
     */
    public function setPreferWidth($preferWidth)
    {
        $this->preferWidth = (bool) $preferWidth;

        return $this;
    }

    /**
     * @param bool $preferHeight
     *
     * @return $this
     */
    public function setPreferHeight($preferHeight)
    {
        $this->preferHeight = (bool) $preferHeight;

        return $this;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getFileWeb(): string
    {
        return $this->appPath->getWebRoot() . \str_replace(ACP3_ROOT_DIR, '', $this->getFile());
    }

    /**
     * @param bool $forceResample
     *
     * @return $this
     */
    public function setForceResample($forceResample)
    {
        $this->forceResample = (bool) $forceResample;

        return $this;
    }

    /**
     * @return bool
     */
    public function process()
    {
        $this->type = null;

        if (\is_file($this->file) === true) {
            $cacheFile = $this->getCacheFileName();

            $picInfo = $this->fastImageSize->getImageSize($this->file);
            $width = $picInfo['width'];
            $height = $picInfo['height'];
            $this->type = $picInfo['type'];

            // Direct output of the picture, if it is already cached
            if ($this->enableCache === true && \is_file($cacheFile) === true) {
                $this->file = $cacheFile;
            } elseif ($this->resamplingIsNecessary($width, $height, $this->type)) { // Resize the picture
                $dimensions = $this->calcNewDimensions($width, $height);

                $this->createCacheDir();

                $this->resample(
                    $dimensions['width'],
                    $dimensions['height'],
                    $width,
                    $height,
                    $this->type,
                    $cacheFile
                );
                $this->file = $cacheFile;
            }

            return true;
        }

        return false;
    }

    /**
     * @param int $pictureType
     *
     * @return string
     */
    private function getMimeType($pictureType)
    {
        switch ($pictureType) {
            case IMAGETYPE_GIF:
                return 'image/gif';
            case IMAGETYPE_JPEG:
                return 'image/jpeg';
            case IMAGETYPE_PNG:
                return 'image/png';
        }

        return 'image/jpeg';
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendResponse()
    {
        $this->setHeaders($this->getMimeType($this->type));

        return $this->response->setContent($this->readFromFile());
    }

    /**
     * Get the name of a possibly cached picture.
     *
     * @return string
     */
    protected function getCacheFileName()
    {
        return $this->cacheDir . $this->getCacheName();
    }

    /**
     * Generiert den Namen des zu cachenden Bildes.
     *
     * @return string
     */
    protected function getCacheName()
    {
        return $this->cachePrefix . \substr($this->file, \strrpos($this->file, '/') + 1);
    }

    /**
     * Reads the contents of the requested picture.
     *
     * @return string
     */
    protected function readFromFile()
    {
        return \file_get_contents($this->file);
    }

    /**
     * Berechnet die neue Breite/Höhe eines Bildes.
     *
     * @param int $width  Ausgangsbreite des Bildes
     * @param int $height Ausgangshöhe des Bildes
     *
     * @return array
     */
    protected function calcNewDimensions($width, $height)
    {
        if (($width >= $height || $this->preferWidth === true) && $this->preferHeight === false) {
            $newWidth = $this->maxWidth;
            $newHeight = (int) ($height * $newWidth / $width);
        } else {
            $newHeight = $this->maxHeight;
            $newWidth = (int) ($width * $newHeight / $height);
        }

        return ['width' => $newWidth, 'height' => $newHeight];
    }

    /**
     * Resamples the picture to the given values.
     *
     * @param int    $newWidth
     * @param int    $newHeight
     * @param int    $width
     * @param int    $height
     * @param int    $type
     * @param string $cacheFile
     */
    protected function resample($newWidth, $newHeight, $width, $height, $type, $cacheFile)
    {
        $this->image = \imagecreatetruecolor($newWidth, $newHeight);
        switch ($type) {
            case IMAGETYPE_GIF:
                $origPicture = \imagecreatefromgif($this->file);
                $this->scalePicture($newWidth, $newHeight, $width, $height, $origPicture);
                \imagegif($this->image, $cacheFile);

                break;
            case IMAGETYPE_JPEG:
                $origPicture = \imagecreatefromjpeg($this->file);
                $this->scalePicture($newWidth, $newHeight, $width, $height, $origPicture);
                \imagejpeg($this->image, $cacheFile, $this->jpgQuality);

                break;
            case IMAGETYPE_PNG:
                \imagealphablending($this->image, false);
                $origPicture = \imagecreatefrompng($this->file);
                $this->scalePicture($newWidth, $newHeight, $width, $height, $origPicture);
                \imagesavealpha($this->image, true);
                \imagepng($this->image, $cacheFile, 9);

                break;
        }

        \imagedestroy($this->image);
    }

    /**
     * @param string $mimeType
     */
    protected function setHeaders($mimeType)
    {
        $this->response->headers->add([
            'Content-type' => $mimeType,
            'Cache-Control' => 'public',
            'Pragma' => 'public',
            'Last-Modified' => \gmdate('D, d M Y H:i:s', \filemtime($this->file)) . ' GMT',
            'Expires' => \gmdate('D, d M Y H:i:s', \time() + 31536000) . ' GMT',
        ]);
    }

    /**
     * @param int      $newWidth
     * @param int      $newHeight
     * @param int      $width
     * @param int      $height
     * @param resource $origPicture
     */
    protected function scalePicture($newWidth, $newHeight, $width, $height, $origPicture)
    {
        \imagecopyresampled($this->image, $origPicture, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $type
     *
     * @return bool
     */
    protected function resamplingIsNecessary($width, $height, $type)
    {
        return ($this->forceResample === true || ($width > $this->maxWidth || $height > $this->maxHeight))
            && \in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG]);
    }

    /**
     * Creates the cache directory if it's not already present.
     */
    protected function createCacheDir()
    {
        if (!\is_dir($this->cacheDir)) {
            @\mkdir($this->cacheDir);
        }
    }
}
