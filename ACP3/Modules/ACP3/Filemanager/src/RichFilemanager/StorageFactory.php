<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\RichFilemanager;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use RFM\Repository\Local\Storage;
use RFM\Repository\StorageInterface;

class StorageFactory
{
    public function __construct(private ApplicationPath $applicationPath, private string $applicationMode)
    {
    }

    public function __invoke(): StorageInterface
    {
        $local = new Storage($this->getFileManagerConfig());
        $local->setRoot($this->applicationPath->getWebRoot() . 'uploads/', true, true);

        return $local;
    }

    /**
     * @return array<string, mixed>
     */
    private function getFileManagerConfig(): array
    {
        return [
            'logger' => [
                'enabled' => $this->applicationMode === ApplicationMode::DEVELOPMENT,
                'file' => $this->applicationPath->getCacheDir() . 'logs/filemanager.log',
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
