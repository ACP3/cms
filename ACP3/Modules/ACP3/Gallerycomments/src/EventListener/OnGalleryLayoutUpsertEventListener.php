<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\EventListener;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Gallerycomments\Installer\Schema as GalleryCommentsSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryLayoutUpsertEventListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly View $view, private readonly SettingsInterface $settings, private readonly Forms $formsHelper)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isInstalled(CommentsSchema::MODULE_NAME) || !$this->modules->isInstalled(GalleryCommentsSchema::MODULE_NAME)) {
            return;
        }

        $galleryCommentsSettings = $this->settings->getSettings(GalleryCommentsSchema::MODULE_NAME);

        if (!$galleryCommentsSettings['comments']) {
            return;
        }

        $formData = $event->getParameters()['form_data'];

        $this->view->assign(
            'comments',
            $this->formsHelper->yesNoCheckboxGenerator(
                'comments',
                $formData['comments'] ?? (int) $galleryCommentsSettings['comments']
            )
        );

        $event->addContent($this->view->fetchTemplate('Gallerycomments/Partials/gallery_layout_upsert.tpl'));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.layout.upsert' => '__invoke',
        ];
    }
}
