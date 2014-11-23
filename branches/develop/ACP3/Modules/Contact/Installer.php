<?php

namespace ACP3\Modules\Contact;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Contact
 */
class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'contact';
    const SCHEMA_VERSION = 35;

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
        return array(
            'address' => '',
            'disclaimer' => '',
            'fax' => '',
            'mail' => '',
            'telephone' => '',
        );
    }

    /**
     * @inheritdoc
     */
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
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri = "contact/index/index/" WHERE uri = "contact/list/";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri = "contact/index/imprint/" WHERE uri = "contact/imprint/";' : '',
            ),
            35 => array(
                'UPDATE `{pre}seo` SET uri = "contact/index/index/" WHERE uri = "contact/index/list/";',
            )
        );
    }

}