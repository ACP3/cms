<?php

namespace ACP3\Modules\ACP3\Contact\Installer;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Contact
 */
class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'contact';

    /**
     * @return string
     */
    public function getModuleName()
    {
        return static::MODULE_NAME;
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 40;
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
            'mobile_phone' => '',
            'picture_credits' => '',
            'telephone' => '',
            'vat_id' => ''
        ];
    }

    /**
     * @return array
     */
    public function specialResources()
    {
        return [];
    }
}
