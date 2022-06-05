<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Helper;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Upload;
use ACP3\Core\Picture\Input;
use ACP3\Core\Picture\Output;
use ACP3\Core\Picture\Picture;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;

class ThumbnailGenerator
{
    public function __construct(private readonly ApplicationPath $appPath, private readonly SettingsInterface $settings, private readonly Picture $picture, private readonly Upload $galleryUploadHelper)
    {
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function generateThumbnail(string $fileName, string $action): Output
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $input = $this->createInput($fileName, $action)
            ->setMaxWidth($settings[$action . 'width'])
            ->setMaxHeight($settings[$action . 'height'])
            ->setPreferHeight($action === 'thumb');

        return $this->picture->process($input);
    }

    public function removePictureFromFilesystem(string $fileName): void
    {
        $this->galleryUploadHelper->removeUploadedFile($fileName);

        foreach (['thumb', ''] as $action) {
            $input = $this->createInput($fileName, $action);

            if (is_file($input->getCacheFileName())) {
                unlink($input->getCacheFileName());
            }
        }
    }

    private function createInput(string $fileName, string $action): Input
    {
        return (new Input())
            ->setEnableCache(true)
            ->setCachePrefix(Schema::MODULE_NAME . '_' . (!empty($action) ? $action . '_' : ''))
            ->setCacheDir($this->appPath->getUploadsDir() . Schema::MODULE_NAME . '/cache/')
            ->setFile($this->appPath->getUploadsDir() . Schema::MODULE_NAME . '/' . $fileName);
    }
}
