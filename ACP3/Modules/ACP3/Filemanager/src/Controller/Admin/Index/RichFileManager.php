<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Environment\ApplicationMode;

class RichFileManager extends AbstractFrontendAction
{
    /**
     * @var string
     */
    private $applicationMode;

    public function __construct(FrontendContext $context, string $applicationMode)
    {
        parent::__construct($context);

        $this->applicationMode = $applicationMode;
    }

    /**
     * @throws \Exception
     */
    public function execute(): void
    {
        $app = new \RFM\Application();

        $local = new \RFM\Repository\Local\Storage($this->getFileManagerConfig());
        $local->setRoot($this->appPath->getWebRoot() . 'uploads/', true, true);

        $app->setStorage($local);

        $app->api = new \RFM\Api\LocalApi();

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
