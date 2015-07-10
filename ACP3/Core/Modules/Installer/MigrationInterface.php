<?php
namespace ACP3\Core\Modules\Installer;

/**
 * Interface MigrationInterface
 * @package ACP3\Core\Modules\Installer
 */
interface MigrationInterface
{
    /**
     * Returns an array with changes to the table structure and data of a module
     *
     * @return array
     */
    public function schemaUpdates();

    /**
     * Returns an array with the SQL changes needed to convert a module, so that a functions with its new name
     *
     * @return array
     */
    public function renameModule();

}