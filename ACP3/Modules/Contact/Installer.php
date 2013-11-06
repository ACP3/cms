<?php

namespace ACP3\Modules\Contact;

use ACP3\Core\Modules;

class Installer extends Modules\Installer
{

    const MODULE_NAME = 'contact';
    const SCHEMA_VERSION = 32;

    public function __construct()
    {
        $this->special_resources = array(
            'acp_list' => 7
        );
    }

    protected function createTables()
    {
        return array();
    }

    protected function removeTables()
    {
        return array();
    }

    protected function settings()
    {
        return array(
            'address' => '',
            'disclaimer' => '',
            'fax' => '',
            'mail' => '',
            'telephone' => '',
        );
    }

    protected function schemaUpdates()
    {
        return array(
            31 => array(
                "UPDATE `{pre}acl_resources` SET privilege_id = 7 WHERE page = 'acp_list' AND module_id = " . $this->getModuleId() . ";"
            ),
            32 => array(
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'sidebar', '', 1);",
            )
        );
    }

}
