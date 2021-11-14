<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Environment\ApplicationMode;
use RFM\Api\LocalApi;
use RFM\Application;
use RFM\Repository\Local\Storage;

class RichFileManager extends AbstractWidgetAction
{
    public function __construct(WidgetContext $context, private string $applicationMode)
    {
        parent::__construct($context);
    }

    /**
     * @throws \Exception
     */
    public function __invoke(): void
    {
        $app = new Application();

        $local = new Storage($this->getFileManagerConfig());
        $local->setRoot($this->appPath->getWebRoot() . 'uploads/', true, true);

        $app->setStorage($local);

        $app->api = new LocalApi();

        $app->run();
    }

    private function getFileManagerConfig(): array
    {
        return [
            'logger' => [
                'enabled' => $this->applicationMode === ApplicationMode::DEVELOPMENT,
                'file' => $this->appPath->getCacheDir() . 'logs/filemanager.log',
            ],
            'security' => [
                'patterns' => [
                    'policy' => 'DISALLOW_LIST',
                    'ignoreCase' => true,
                    'restrictions' => [
                        '*/.htaccess',
                        '*/web.config',
                        '*/cache/*',
                        '*/assets/*',
                    ],
                ],
            ],
        ];
    }
}
