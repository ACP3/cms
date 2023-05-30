<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\EventListener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Model\DataProcessor\ColumnType\BooleanColumnType;
use ACP3\Core\Model\Event\ModelSavePrepareDataEvent;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallerycomments\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryModelPrepareDataEventListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly RequestInterface $request, private readonly SettingsInterface $settings)
    {
    }

    public function __invoke(ModelSavePrepareDataEvent $event): void
    {
        if (!$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($settings['comments'] == 1 && $this->request->getPost()->has('comments')) {
            $event->addAllowedColumn('comments', BooleanColumnType::class);
            $event->addRawData('comments', $this->request->getPost()->get('comments'));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.model.gallery.prepare_data' => '__invoke',
        ];
    }
}
