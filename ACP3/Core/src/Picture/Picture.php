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
    private ?\GdImage $image = null;

    public function __construct(private FastImageSize $fastImageSize, private ApplicationPath $appPath)
    {
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function process(Input $input): Output
    {
        if (is_file($input->getFile()) === true) {
            $cacheFile = $input->getCacheFileName();

            $picInfo = $this->getPictureInfo($input->getFile());

            $output = new Output($this->appPath, $input->getFile(), $picInfo['type']);
            $output->setSrcWidth($picInfo['width']);
            $output->setSrcHeight($picInfo['height']);

            // Direct output of the picture, if it is already cached
            if ($input->isEnableCache() === true && is_file($cacheFile) === true) {
                $this->calcDestDimensions($input, $output);
                $output->setDestFile($cacheFile);
            } elseif ($this->resamplingIsNecessary($input, $output)) { // Resize the picture
                $this->createCacheDir($input);
                $this->resample($input, $output);

                $output->setDestFile($cacheFile);
            }

            return $output;
        }

        throw new PictureGenerateException(sprintf('Could not find picture: %s', $input->getFile()));
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    private function getPictureInfo(string $fileName): array
    {
        $picInfo = $this->fastImageSize->getImageSize($fileName);

        // If fastImageSize fails, try it with PHP's standard getimagesize() function
        if ($picInfo === false) {
            $info = getimagesize($fileName);

            if ($info === false) {
                throw new PictureGenerateException(sprintf('Could not get image size information for picture <%s>!', $fileName));
            }

            $picInfo = [
                'width' => $info[0],
                'height' => $info[1],
                'type' => $info[2],
            ];
        }

        return $picInfo;
    }

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
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    private function createCacheDir(Input $input): void
    {
        if (is_dir($input->getCacheDir())) {
            return;
        }

        if (!mkdir($concurrentDirectory = $input->getCacheDir()) && !is_dir($concurrentDirectory)) {
            throw new PictureGenerateException(sprintf('Could not create cache dir: %s', $input->getCacheDir()));
        }
    }

    /**
     * Resamples the picture to the given values.
     */
    private function resample(Input $input, Output $output): void
    {
        $this->calcDestDimensions($input, $output);

        $this->image = imagecreatetruecolor($output->getDestWidth(), $output->getDestHeight());

        switch ($output->getType()) {
            case IMAGETYPE_GIF:
                $origPicture = imagecreatefromgif($input->getFile());
                $this->scalePicture($output, $origPicture);
                imagegif($this->image, $input->getCacheFileName());

                break;
            case IMAGETYPE_JPEG:
                $origPicture = imagecreatefromjpeg($input->getFile());
                $this->scalePicture($output, $origPicture);
                imagejpeg($this->image, $input->getCacheFileName(), $input->getJpgQuality());

                break;
            case IMAGETYPE_PNG:
                imagealphablending($this->image, false);
                $origPicture = imagecreatefrompng($input->getFile());
                $this->scalePicture($output, $origPicture);
                imagesavealpha($this->image, true);
                imagepng($this->image, $input->getCacheFileName(), 9);

                break;
        }
    }

    private function scalePicture(Output $output, \GdImage $srcImage): void
    {
        imagecopyresampled(
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
}
