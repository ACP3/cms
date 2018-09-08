<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Picture\Exception\PictureGenerateException;
use FastImageSize\FastImageSize;

class Picture
{
    /**
     * @var FastImageSize
     */
    private $fastImageSize;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;

    /**
     * @var resource
     */
    private $image;

    public function __construct(
        FastImageSize $fastImageSize,
        ApplicationPath $appPath
    ) {
        $this->fastImageSize = $fastImageSize;
        $this->appPath = $appPath;
    }

    /**
     * @param \ACP3\Core\Picture\InputOptions $options
     *
     * @return \ACP3\Core\Picture\PictureResponse
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function process(InputOptions $options): PictureResponse
    {
        if (\is_file($options->getFile()) === true) {
            $cacheFile = $this->getCacheFileName($options);

            $picInfo = $this->fastImageSize->getImageSize($options->getFile());

            $output = new PictureResponse($this->appPath, $options->getFile(), $picInfo['type']);
            $output->setSrcWidth($picInfo['width']);
            $output->setSrcHeight($picInfo['height']);

            // Direct output of the picture, if it is already cached
            if ($options->isEnableCache() === true && \is_file($cacheFile) === true) {
                $this->calcNewDimensions($options, $output);
                $output->setDestFile($cacheFile);
            } elseif ($this->resamplingIsNecessary($options, $output)) { // Resize the picture
                $this->createCacheDir($options);
                $this->resample($options, $output);

                $output->setDestFile($cacheFile);
            }

            return $output;
        }

        throw new PictureGenerateException(
            \sprintf('Could not find picture: %s', $options->getFile())
        );
    }

    /**
     * Get the name of a possibly cached picture.
     *
     * @param \ACP3\Core\Picture\InputOptions $options
     *
     * @return string
     */
    private function getCacheFileName(InputOptions $options): string
    {
        return $options->getCacheDir() . $this->getCacheName($options);
    }

    /**
     * Generiert den Namen des zu cachenden Bildes.
     *
     * @param \ACP3\Core\Picture\InputOptions $options
     *
     * @return string
     */
    private function getCacheName(InputOptions $options): string
    {
        return $options->getCachePrefix() . \substr($options->getFile(), \strrpos($options->getFile(), '/') + 1);
    }

    /**
     * @param \ACP3\Core\Picture\InputOptions    $options
     * @param \ACP3\Core\Picture\PictureResponse $output
     *
     * @return bool
     */
    private function resamplingIsNecessary(InputOptions $options, PictureResponse $output): bool
    {
        return ($options->isForceResample() || $this->hasNecessaryResamplingDimensions($options, $output))
            && \in_array($output->getType(), [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG]);
    }

    private function hasNecessaryResamplingDimensions(InputOptions $options, PictureResponse $output): bool
    {
        return $output->getSrcWidth() > $options->getMaxWidth() || $output->getSrcHeight() > $options->getMaxHeight();
    }

    /**
     * Berechnet die neue Breite/Höhe eines Bildes.
     *
     * @param \ACP3\Core\Picture\InputOptions    $options
     * @param \ACP3\Core\Picture\PictureResponse $output
     */
    private function calcNewDimensions(InputOptions $options, PictureResponse $output): void
    {
        if ($options->isPreferWidth() === false && ($output->getSrcWidth() >= $output->getSrcHeight() || $options->isPreferWidth() === true)) {
            $newWidth = $options->getMaxWidth();
            $newHeight = (int) ($output->getSrcHeight() * $newWidth / $output->getSrcWidth());
        } else {
            $newHeight = $options->getMaxHeight();
            $newWidth = (int) ($output->getSrcWidth() * $newHeight / $output->getSrcHeight());
        }

        $output->setDestWidth($newWidth);
        $output->setDestHeight($newHeight);
    }

    /**
     * Creates the cache directory if it's not already present.
     *
     * @param \ACP3\Core\Picture\InputOptions $options
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    private function createCacheDir(InputOptions $options)
    {
        if (\is_dir($options->getCacheDir())) {
            return;
        }

        if (!@\mkdir($options->getCacheDir())) {
            throw new PictureGenerateException(
                \sprintf('Could not create cache dir: %s', $options->getCacheDir())
            );
        }
    }

    /**
     * Resamples the picture to the given values.
     *
     * @param \ACP3\Core\Picture\InputOptions    $options
     * @param \ACP3\Core\Picture\PictureResponse $output
     */
    private function resample(InputOptions $options, PictureResponse $output): void
    {
        $this->calcNewDimensions($options, $output);

        $this->image = \imagecreatetruecolor($output->getDestWidth(), $output->getDestHeight());

        switch ($output->getType()) {
            case IMAGETYPE_GIF:
                $origPicture = \imagecreatefromgif($options->getFile());
                $this->scalePicture($output, $origPicture);
                \imagegif($this->image, $this->getCacheFileName($options));

                break;
            case IMAGETYPE_JPEG:
                $origPicture = \imagecreatefromjpeg($options->getFile());
                $this->scalePicture($output, $origPicture);
                \imagejpeg($this->image, $this->getCacheFileName($options), $options->getJpgQuality());

                break;
            case IMAGETYPE_PNG:
                \imagealphablending($this->image, false);
                $origPicture = \imagecreatefrompng($options->getFile());
                $this->scalePicture($output, $origPicture);
                \imagesavealpha($this->image, true);
                \imagepng($this->image, $this->getCacheFileName($options), 9);

                break;
        }

        $this->freeMemory();
    }

    /**
     * @param \ACP3\Core\Picture\PictureResponse $output
     * @param resource                           $srcImage
     */
    private function scalePicture(PictureResponse $output, $srcImage): void
    {
        \imagecopyresampled(
            $this->image,
            $srcImage,
            0,
            0,
            0,
            0,
            $output->getDestWidth(),
            $output->getDestHeight(),
            $output->getSrcWidth(),
            $output->getSrcHeight()
        );
    }

    private function freeMemory(): void
    {
        if (\is_resource($this->image) === true) {
            \imagedestroy($this->image);
        }
    }
}
