<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;

class RichFileManager extends AbstractFrontendAction
{
    /**
     * @throws \Exception
     */
    public function execute(): void
    {
        $app = new \RFM\Application();

        $local = new \RFM\Repository\Local\Storage($this->getFileManagerConfig());
        $local->setRoot($this->appPath->getUploadsDir(), true, false);

        $app->setStorage($local);

        $app->api = new \RFM\Api\LocalApi();

        $app->run();
    }

    /**
     * @return array
     */
    private function getFileManagerConfig(): array
    {
        return [
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
