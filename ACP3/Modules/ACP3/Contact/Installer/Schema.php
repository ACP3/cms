<?php

namespace ACP3\Modules\ACP3\Contact\Installer;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Contact
 */
class Schema implements Modules\Installer\SchemaInterface
{

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'contact';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 37;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'address' => '',
            'ceo' => '',
            'disclaimer' => '',
            'fax' => '',
            'mail' => '',
            'telephone' => '',
            'vat_id' => ''
        ];
    }

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'Admin' => [
                'Index' => [
                    'index' => 7
                ]
            ]
        ];
    }
}
