<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\EventListener;

use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Gallery\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallerycomments\Installer\Schema as GalleryCommentsSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryLayoutDetailsAfterEventListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly View $view, private readonly SettingsInterface $settings, private readonly GalleryRepository $galleryRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isInstalled(CommentsSchema::MODULE_NAME) || !$this->modules->isInstalled(GalleryCommentsSchema::MODULE_NAME)) {
            return;
        }

        $galleryCommentsSettings = $this->settings->getSettings(GalleryCommentsSchema::MODULE_NAME);
        $galleryPicture = $event->getParameters()['gallery_picture'];
        $gallery = $this->galleryRepository->getOneById($galleryPicture['gallery_id']);

        if (!$galleryCommentsSettings['comments'] || !$gallery['comments']) {
            return;
        }

        $this->view->assign('gallery_picture', $galleryPicture);

        $event->addContent($this->view->fetchTemplate('Gallerycomments/Partials/gallery_layout_details_after.tpl'));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.layout.details_after' => '__invoke',
        ];
    }
}
