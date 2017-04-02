<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Modules\ACP3\System;
use Composer\Semver\Comparator;

/**
 * Class UpdateCheck
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Maintenance
 */
class UpdateCheck extends Core\Controller\AbstractAdminAction
{
    /**
     * @return array
     */
    public function execute()
    {
        $update = [];
        $file = @file_get_contents('https://acp3.github.io/update.txt');
        if ($file !== false) {
            list($latestVersion, $url) = explode('||', $file);
            $update = [
                'installed_version' => Core\Application\BootstrapInterface::VERSION,
                'latest_version' => $latestVersion,
                'is_latest' => $this->isLatestVersion($latestVersion),
                'url' => $url
            ];
        }

        return [
            'update' => $update
        ];
    }

    /**
     * @param string $latestVersion
     * @return bool
     */
    protected function isLatestVersion($latestVersion)
    {
        return Comparator::greaterThanOrEqualTo(
            Core\Application\BootstrapInterface::VERSION,
            $latestVersion
        );
    }
}
