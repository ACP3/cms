<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema as GallerySchema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class GalleryListWidgetViewProvider
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        Date $date,
        GalleryRepository $galleryRepository,
        SettingsInterface $settings
    ) {
        $this->date = $date;
        $this->galleryRepository = $galleryRepository;
        $this->settings = $settings;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(GallerySchema::MODULE_NAME);

        return [
            'sidebar_galleries' => $this->galleryRepository->getAll($this->date->getCurrentDateTime(), $settings['sidebar']),
        ];
    }
}
