<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newscomments\Event\Listener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Model\DataProcessor\ColumnTypes;
use ACP3\Core\Model\Event\ModelSavePrepareDataEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Newscomments\Installer\Schema;

class OnNewsModelPrepareDataEventListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        RequestInterface $request,
        SettingsInterface $settings
    ) {
        $this->request = $request;
        $this->settings = $settings;
    }

    public function __invoke(ModelSavePrepareDataEvent $event)
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($settings['comments'] == 1 && $this->request->getPost()->has('comments')) {
            $event->addAllowedColumn('comments', ColumnTypes::COLUMN_TYPE_BOOLEAN);
            $event->addRawData('comments', $this->request->getPost()->get('comments'));
        }
    }
}
