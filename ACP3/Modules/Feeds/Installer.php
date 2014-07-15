<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'feeds';
    const SCHEMA_VERSION = 31;

    /**
     * @var array
     */
    protected $specialResources = array(
        'Admin' => array(
            'Index' => array(
                'index' => 7
            )
        )
    );

    public function createTables()
    {
        return array();
    }

    public function removeTables()
    {
        return array();
    }

    public function settings()
    {
        return array(
            'feed_image' => '',
            'feed_type' => 'RSS 2.0'
        );
    }

    public function removeSettings()
    {
        return true;
    }

    public function schemaUpdates()
    {
        return array(
            31 => array(
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'acp_list', '', 7);",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'feed_image', '');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'feed_type', 'RSS 2.0');",
            )
        );
    }

}
