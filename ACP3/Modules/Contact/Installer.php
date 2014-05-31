<?php

namespace ACP3\Modules\Contact;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'contact';
    const SCHEMA_VERSION = 34;

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        parent::__construct($db);

        $this->specialResources = array(
            'Admin' => array(
                'Index' => array(
                    'index' => 7
                )
            )
        );
    }

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
            'address' => '',
            'disclaimer' => '',
            'fax' => '',
            'mail' => '',
            'telephone' => '',
        );
    }

    public function schemaUpdates()
    {
        return array(
            31 => array(
                "UPDATE `{pre}acl_resources` SET privilege_id = 7 WHERE page = 'acp_list' AND module_id = " . $this->getModuleId() . ";"
            ),
            32 => array(
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'sidebar', '', 1);",
            ),
            33 => array(
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "contact/", "contact/index/") WHERE uri LIKE "contact/%";',
            ),
            34 => array(
                Modules::isInstalled('menus') || Modules::isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri = "contact/index/index/" WHERE uri = "contact/list";' : '',
                Modules::isInstalled('menus') || Modules::isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri = "contact/index/imprint/" WHERE uri = "contact/imprint";' : '',
            )
        );
    }

}
