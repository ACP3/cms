<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Repository;

use ACP3\Core\Database\Connection;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Repository\AbstractRepository;
use ACP3\Core\Settings\Repository\SettingsAwareRepositoryInterface;

class SettingsRepository extends AbstractRepository implements SettingsAwareRepositoryInterface
{
    public const TABLE_NAME = 'settings';

    public function __construct(Connection $db, private string $environment)
    {
        parent::__construct($db);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllSettings(): array
    {
        return [
            [
                'module_name' => 'system',
                'name' => 'page_cache_purge_mode',
                'value' => 1,
            ],
            [
                'module_name' => 'system',
                'name' => 'design',
                'value' => 'acp3-installer',
            ],
            [
                'module_name' => 'system',
                'name' => 'lang',
                'value' => 'en_US',
            ],
            [
                'module_name' => 'system',
                'name' => 'maintenance_mode',
                'value' => 0,
            ],
            [
                'module_name' => 'system',
                'name' => 'mod_rewrite',
                'value' => 0,
            ],
            [
                'module_name' => 'system',
                'name' => 'homepage',
                'value' => $this->getHomepage(),
            ],
            [
                'module_name' => 'system',
                'name' => 'date_format_long',
                'value' => 'd.m.y, H:i',
            ],
            [
                'module_name' => 'system',
                'name' => 'date_format_short',
                'value' => 'd.m.y',
            ],
            [
                'module_name' => 'system',
                'name' => 'date_time_zone',
                'value' => date_default_timezone_get(),
            ],
            [
                'module_name' => 'system',
                'name' => 'site_title',
                'value' => 'ACP3 Installer',
            ],
            [
                'module_name' => 'system',
                'name' => 'site_subtitle',
                'value' => '',
            ],
            [
                'module_name' => 'system',
                'name' => 'site_subtitle_mode',
                'value' => 3,
            ],
        ];
    }

    private function getHomepage(): string
    {
        if ($this->environment === ApplicationMode::INSTALLER) {
            return 'installer/index/index/';
        }

        return 'installer/update/index/';
    }
}
