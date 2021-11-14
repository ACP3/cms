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
use ACP3\Modules\ACP3\Gallerycomments\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryLayoutSettingsEventListener implements EventSubscriberInterface
{
    public function __construct(private Modules $modules, private View $view, private SettingsInterface $settings, private Forms $formsHelper)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isInstalled(CommentsSchema::MODULE_NAME) || !$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->view->assign(
            'comments',
            $this->formsHelper->yesNoCheckboxGenerator('comments', $settings['comments'])
        );

        $event->addContent($this->view->fetchTemplate('Gallerycomments/Partials/gallery_layout_settings.tpl'));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.layout.settings' => '__invoke',
        ];
    }
}
