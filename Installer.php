<?php

namespace ACP3\Modules\ACP3\Feeds;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Feeds
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'feeds';
    const SCHEMA_VERSION = 31;

    /**
     * @var array
     */
    protected $specialResources = [
        'Admin' => [
            'Index' => [
                'index' => 7
            ]
        ]
    ];

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [
            'feed_image' => '',
            'feed_type' => 'RSS 2.0'
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeSettings()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'acp_list', '', 7);",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'feed_image', '');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'feed_type', 'RSS 2.0');",
            ]
        ];
    }
}
