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
        return [];
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            ]
        ];
    }
}
