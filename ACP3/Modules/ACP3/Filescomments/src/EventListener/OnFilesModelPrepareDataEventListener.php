<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\EventListener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Model\DataProcessor\ColumnType\BooleanColumnType;
use ACP3\Core\Model\Event\ModelSavePrepareDataEvent;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Filescomments\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnFilesModelPrepareDataEventListener implements EventSubscriberInterface
{
    public function __construct(private Modules $modules, private RequestInterface $request, private SettingsInterface $settings)
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

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'files.model.files.prepare_data' => '__invoke',
        ];
    }
}
