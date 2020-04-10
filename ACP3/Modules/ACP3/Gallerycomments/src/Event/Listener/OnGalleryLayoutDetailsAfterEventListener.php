<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\Event\Listener;

use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Gallery\Installer\Schema as GallerySchema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallerycomments\Installer\Schema as GalleryCommentsSchema;

class OnGalleryLayoutDetailsAfterEventListener
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;

    public function __construct(
        Modules $modules,
        View $view,
        SettingsInterface $settings,
        GalleryRepository $galleryRepository
    ) {
        $this->view = $view;
        $this->settings = $settings;
        $this->modules = $modules;
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isActive(CommentsSchema::MODULE_NAME)) {
            return;
        }

        $gallerySettings = $this->settings->getSettings(GallerySchema::MODULE_NAME);
        $galleryCommentsSettings = $this->settings->getSettings(GalleryCommentsSchema::MODULE_NAME);
        $galleryPicture = $event->getParameters()['gallery_picture'];
        $gallery = $this->galleryRepository->getOneById($galleryPicture['gallery_id']);

        if ((bool) $gallerySettings['overlay'] || !(bool) $galleryCommentsSettings['comments'] || !(bool) $gallery['comments']) {
            return;
        }

        $this->view->assign('gallery_picture', $galleryPicture);

        $this->view->displayTemplate('Gallerycomments/Partials/gallery_layout_details_after.tpl');
    }
}
