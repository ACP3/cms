<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Helper;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Picture\Input;
use ACP3\Core\Picture\Output;
use ACP3\Core\Picture\Picture;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;

class ThumbnailGenerator
{
    /**
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Picture\Picture
     */
    private $picture;

    public function __construct(ApplicationPath $appPath, SettingsInterface $settings, Picture $picture)
    {
        $this->appPath = $appPath;
        $this->settings = $settings;
        $this->picture = $picture;
    }

    /**
     * @param string $fileName
     * @param string $action
     *
     * @return \ACP3\Core\Picture\Output
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function generateThumbnail(string $fileName, string $action): Output
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $input = (new Input())
            ->setEnableCache(true)
            ->setCachePrefix(Schema::MODULE_NAME . '_' . (!empty($action) ? $action . '_' : ''))
            ->setCacheDir($this->appPath->getUploadsDir() . Schema::MODULE_NAME . '/cache/')
            ->setMaxWidth($settings[$action . 'width'])
            ->setMaxHeight($settings[$action . 'height'])
            ->setFile($this->appPath->getUploadsDir() . Schema::MODULE_NAME . '/' . $fileName)
            ->setPreferHeight($action === 'thumb');

        return $this->picture->process($input);
    }
}
