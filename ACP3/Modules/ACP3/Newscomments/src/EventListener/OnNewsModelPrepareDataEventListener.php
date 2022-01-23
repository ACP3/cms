<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newscomments\EventListener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Model\DataProcessor\ColumnTypes;
use ACP3\Core\Model\Event\ModelSavePrepareDataEvent;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Newscomments\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnNewsModelPrepareDataEventListener implements EventSubscriberInterface
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
            $event->addAllowedColumn('comments', ColumnTypes::COLUMN_TYPE_BOOLEAN);
            $event->addRawData('comments', $this->request->getPost()->get('comments'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'news.model.news.prepare_data' => '__invoke',
        ];
    }
}
