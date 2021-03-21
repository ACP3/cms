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
     * @param \ACP3\Core\Picture\Input $input
     *
     * @return \ACP3\Core\Picture\Output
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function process(Input $input): Output
    {
        if (\is_file($input->getFile()) === true) {
            $cacheFile = $this->getCacheFileName($input);

            $picInfo = $this->getPictureInfo($input->getFile());

            $output = new Output($this->appPath, $input->getFile(), $picInfo['type']);
            $output->setSrcWidth($picInfo['width']);
            $output->setSrcHeight($picInfo['height']);

            // Direct output of the picture, if it is already cached
            if ($input->isEnableCache() === true && \is_file($cacheFile) === true) {
                $this->calcDestDimensions($input, $output);
                $output->setDestFile($cacheFile);
            } elseif ($this->resamplingIsNecessary($input, $output)) { // Resize the picture
                $this->createCacheDir($input);
                $this->resample($input, $output);

                $output->setDestFile($cacheFile);
            }

            return $output;
        }

        throw new PictureGenerateException(\sprintf('Could not find picture: %s', $input->getFile()));
    }

    /**
     * Returns the name of a possibly cached picture.
     *
     * @param \ACP3\Core\Picture\Input $input
     */
    private function getCacheFileName(Input $input): string
    {
        return $input->getCacheDir() . $this->getCacheName($input);
    }

    /**
     * Generates the file name of the picture to be cached.
     *
     * @param \ACP3\Core\Picture\Input $input
     */
    private function getCacheName(Input $input): string
    {
        return $input->getCachePrefix() . \substr($input->getFile(), \strrpos($input->getFile(), '/') + 1);
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    private function getPictureInfo(string $fileName): array
    {
        $picInfo = $this->fastImageSize->getImageSize($fileName);

        // If fastImageSize fails, try it with PHP's standard getimagesize() function
        if ($picInfo === false) {
            $info = \getimagesize($fileName);

            if ($info === false) {
                throw new PictureGenerateException(\sprintf('Could not get image size information for picture <%s>!', $fileName));
            }

            $picInfo = [
                'width' => $info[0],
                'height' => $info[1],
                'type' => $info[2],
            ];
        }

        return $picInfo;
    }

    /**
     * @param \ACP3\Core\Picture\Input  $input
     * @param \ACP3\Core\Picture\Output $output
     */
    private function resamplingIsNecessary(Input $input, Output $output): bool
    {
        return ($input->isForceResample() || $this->hasNecessaryResamplingDimensions($input, $output))
            && \in_array($output->getType(), [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true);
    }

    private function hasNecessaryResamplingDimensions(Input $input, Output $output): bool
    {
        return $output->getSrcWidth() > $input->getMaxWidth() || $output->getSrcHeight() > $input->getMaxHeight();
    }

    /**
     * Calculates the targeted picture dimensions.
     *
     * @param \ACP3\Core\Picture\Input  $input
     * @param \ACP3\Core\Picture\Output $output
     */
    private function calcDestDimensions(Input $input, Output $output): void
    {
        if ($input->isPreferHeight() === false && ($output->getSrcWidth() >= $output->getSrcHeight() || $input->isPreferWidth() === true)) {
            $newWidth = $input->getMaxWidth();
            $newHeight = (int) ($output->getSrcHeight() * $newWidth / $output->getSrcWidth());
        } else {
            $newHeight = $input->getMaxHeight();
            $newWidth = (int) ($output->getSrcWidth() * $newHeight / $output->getSrcHeight());
        }

        $output->setDestWidth($newWidth);
        $output->setDestHeight($newHeight);
    }

    /**
     * Creates the cache directory if it's not already present.
     *
     * @param \ACP3\Core\Picture\Input $input
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    private function createCacheDir(Input $input): void
    {
        if (\is_dir($input->getCacheDir())) {
            return;
        }

        if (!\mkdir($concurrentDirectory = $input->getCacheDir()) && !\is_dir($concurrentDirectory)) {
            throw new PictureGenerateException(\sprintf('Could not create cache dir: %s', $input->getCacheDir()));
        }
    }

    /**
     * Resamples the picture to the given values.
     *
     * @param \ACP3\Core\Picture\Input  $input
     * @param \ACP3\Core\Picture\Output $output
     */
    private function resample(Input $input, Output $output): void
    {
        $this->calcDestDimensions($input, $output);

        $this->image = \imagecreatetruecolor($output->getDestWidth(), $output->getDestHeight());

        switch ($output->getType()) {
            case IMAGETYPE_GIF:
                $origPicture = \imagecreatefromgif($input->getFile());
                $this->scalePicture($output, $origPicture);
                \imagegif($this->image, $this->getCacheFileName($input));

                break;
            case IMAGETYPE_JPEG:
                $origPicture = \imagecreatefromjpeg($input->getFile());
                $this->scalePicture($output, $origPicture);
                \imagejpeg($this->image, $this->getCacheFileName($input), $input->getJpgQuality());

                break;
            case IMAGETYPE_PNG:
                \imagealphablending($this->image, false);
                $origPicture = \imagecreatefrompng($input->getFile());
                $this->scalePicture($output, $origPicture);
                \imagesavealpha($this->image, true);
                \imagepng($this->image, $this->getCacheFileName($input), 9);

                break;
        }

        $this->freeMemory();
    }

    /**
     * @param \ACP3\Core\Picture\Output $output
     * @param resource                  $srcImage
     */
    private function scalePicture(Output $output, $srcImage): void
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
