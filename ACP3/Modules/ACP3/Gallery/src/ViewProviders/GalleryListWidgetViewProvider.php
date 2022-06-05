<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema as GallerySchema;
use ACP3\Modules\ACP3\Gallery\Repository\GalleryRepository;

class GalleryListWidgetViewProvider
{
    public function __construct(private readonly Date $date, private readonly GalleryRepository $galleryRepository, private readonly SettingsInterface $settings)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(GallerySchema::MODULE_NAME);

        return [
            'sidebar_galleries' => $this->galleryRepository->getAll($this->date->getCurrentDateTime(), $settings['sidebar']),
        ];
    }
}
