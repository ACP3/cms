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
use ACP3\Modules\ACP3\Gallery\Installer\Schema as GallerySchema;
use ACP3\Modules\ACP3\Gallerycomments\Installer\Schema as GalleryCommentsSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryLayoutUpsertEventListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, View $view, SettingsInterface $settings, Forms $formsHelper)
    {
        $this->view = $view;
        $this->formsHelper = $formsHelper;
        $this->settings = $settings;
        $this->modules = $modules;
    }

    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isActive(CommentsSchema::MODULE_NAME) || !$this->modules->isActive(GalleryCommentsSchema::MODULE_NAME)) {
            return;
        }

        $gallerySettings = $this->settings->getSettings(GallerySchema::MODULE_NAME);
        $galleryCommentsSettings = $this->settings->getSettings(GalleryCommentsSchema::MODULE_NAME);

        if ((bool) $gallerySettings['overlay'] || !(bool) $galleryCommentsSettings['comments']) {
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

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.layout.upsert' => '__invoke',
        ];
    }
}
