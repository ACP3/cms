<?php

namespace ACP3\Modules\Captcha;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Captcha
 */
class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'captcha';
    const SCHEMA_VERSION = 31;

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return array(
            31 => array(
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            )
        );
    }

}
