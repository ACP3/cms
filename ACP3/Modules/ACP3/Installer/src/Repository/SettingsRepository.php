<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Repository;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Model\Repository\SettingsAwareRepositoryInterface;

class SettingsRepository implements SettingsAwareRepositoryInterface
{
    /**
     * @var string
     */
    private $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllSettings()
    {
        return [
            [
                'module_name' => 'system',
                'name' => 'page_cache_is_enabled',
                'value' => 0,
            ],
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $data)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName($tableName = '')
    {
        return $tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entryId, ?string $columnName = null)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data, $entryId)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($entryId)
    {
        return [];
    }

    private function getHomepage(): string
    {
        if ($this->environment === ApplicationMode::INSTALLER) {
            return 'installer/index/index/';
        }

        return 'installer/update/index/';
    }
}
