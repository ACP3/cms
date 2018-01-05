<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Helper;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Picture;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\System\Installer\Schema as SystemSchema;

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
     * ThumbnailGenerator constructor.
     * @param ApplicationPath $appPath
     * @param SettingsInterface $settings
     */
    public function __construct(ApplicationPath $appPath, SettingsInterface $settings)
    {
        $this->appPath = $appPath;
        $this->settings = $settings;
    }

    /**
     * @param Picture $picture
     * @param string $action
     * @param string $fileName
     * @return Picture
     */
    public function generateThumbnail(Picture $picture, string $action, string $fileName): Picture
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return $picture
            ->setEnableCache($this->settings->getSettings(SystemSchema::MODULE_NAME)['cache_images'] == 1)
            ->setCachePrefix('gallery_' . $action)
            ->setCacheDir($this->appPath->getUploadsDir() . 'gallery/cache/')
            ->setMaxWidth($settings[$action . 'width'])
            ->setMaxHeight($settings[$action . 'height'])
            ->setFile($this->appPath->getUploadsDir() . 'gallery/' . $fileName)
            ->setPreferHeight($action === 'thumb');
    }
}
